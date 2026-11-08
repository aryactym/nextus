<?php

declare(strict_types=1);

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Learning\Models\Quiz;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Setup user dengan permission manage courses dan sebuah organisasi.
 *
 * @return array{user: User, organization: Organization}
 */
function courseUserWithOrganization(): array
{
    $organization = Organization::create([
        'name' => 'Org Kursus',
        'slug' => 'org-kursus',
    ]);
    Session::put('organization_id', $organization->id);
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $user->givePermissionTo(Permission::firstOrCreate([
        'name' => 'manage courses',
        'guard_name' => 'web',
    ]));

    return ['user' => $user, 'organization' => $organization];
}

it('shows the course index page', function () {
    ['user' => $user] = courseUserWithOrganization();
    $this->actingAs($user)
        ->get(route('admin.courses.index'))
        ->assertOk();
});

it('stores a new course', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();

    $response = $this->actingAs($user)->post(route('admin.courses.store'), [
        'title' => ['id' => 'Pengantar Zakat', 'en' => 'Introduction to Zakat'],
        'slug' => 'pengantar-zakat',
        'level' => 'beginner',
        'access_type' => 'free',
        'certificate_type' => 'free',
        'status' => 'draft',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.courses.index'));
    $course = Course::withoutGlobalScopes()->where('slug', 'pengantar-zakat')->first();
    expect($course)->not->toBeNull()
        ->and($course->organization_id)->toBe($organization->id)
        ->and($course->getTranslation('title', 'id'))->toBe('Pengantar Zakat')
        ->and($course->getTranslation('title', 'en'))->toBe('Introduction to Zakat')
        ->and($course->status)->toBe('draft');
});

it('filters the course index by keyword and attributes', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();
    $category = Category::create([
        'organization_id' => $organization->id,
        'name' => ['id' => 'Filantropi'],
        'slug' => 'filantropi',
    ]);

    Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Pemetaan Sosial Ekonomi'],
        'slug' => 'pemetaan-sosial',
        'status' => 'published',
        'access_type' => 'free',
        'category_id' => $category->id,
    ]);
    Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Manajemen Risiko Program'],
        'slug' => 'manajemen-risiko',
        'status' => 'draft',
        'access_type' => 'donation',
    ]);

    $this->actingAs($user)
        ->get(route('admin.courses.index', ['q' => 'pemetaan']))
        ->assertOk()
        ->assertSee('Pemetaan Sosial Ekonomi')
        ->assertDontSee('Manajemen Risiko Program');

    $this->actingAs($user)
        ->get(route('admin.courses.index', ['status' => 'draft']))
        ->assertOk()
        ->assertSee('Manajemen Risiko Program')
        ->assertDontSee('Pemetaan Sosial Ekonomi');

    $this->actingAs($user)
        ->get(route('admin.courses.index', ['access_type' => 'free']))
        ->assertOk()
        ->assertSee('Pemetaan Sosial Ekonomi')
        ->assertDontSee('Manajemen Risiko Program');

    $this->actingAs($user)
        ->get(route('admin.courses.index', ['category' => (string) $category->id]))
        ->assertOk()
        ->assertSee('Pemetaan Sosial Ekonomi')
        ->assertDontSee('Manajemen Risiko Program');
});

it('lets an admin manage questions for a quiz lesson', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Kuis'],
        'slug' => 'kursus-kuis',
    ]);
    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => ['id' => 'Modul Kuis'],
        'sort' => 1,
    ]);
    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => ['id' => 'Asesmen'],
        'type' => 'quiz',
        'sort' => 1,
    ]);

    $this->actingAs($user)
        ->post(route('admin.lessons.quiz.questions.store', $lesson), [
            'question' => 'Apa fokus utama kursus ini?',
            'options' => ['Filantropi Islam', 'Pemasaran', 'Desain', 'Administrasi'],
            'correct_index' => 0,
        ])
        ->assertRedirect(route('admin.courses.edit', $course->uuid));

    $question = $lesson->quiz()->firstOrFail()->questions()->firstOrFail();
    expect($question->options)->toHaveCount(4)
        ->and($question->correct_index)->toBe(0);

    $this->actingAs($user)
        ->delete(route('admin.quiz-questions.destroy', $question))
        ->assertRedirect(route('admin.courses.edit', $course->uuid));

    expect($lesson->quiz()->firstOrFail()->questions()->count())->toBe(0);
});

it('lets an admin update and delete a module', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Modul'],
        'slug' => 'kursus-modul',
    ]);
    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => ['id' => 'Modul Awal'],
        'sort' => 1,
    ]);

    $this->actingAs($user)
        ->put(route('admin.modules.update', $module), [
            'title' => ['id' => 'Modul Diperbarui', 'en' => 'Updated Module'],
            'sort' => 2,
        ])
        ->assertRedirect(route('admin.courses.edit', $course->uuid));

    $module->refresh();
    expect($module->getTranslation('title', 'id'))->toBe('Modul Diperbarui')
        ->and($module->sort)->toBe(2);

    $this->actingAs($user)
        ->delete(route('admin.modules.destroy', $module))
        ->assertRedirect(route('admin.courses.edit', $course->uuid));

    $this->assertSoftDeleted('course_modules', ['id' => $module->id]);
});

it('lets an admin update and delete a lesson', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Pelajaran'],
        'slug' => 'kursus-pelajaran',
    ]);
    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => ['id' => 'Modul 1'],
        'sort' => 1,
    ]);
    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => ['id' => 'Pelajaran Awal'],
        'type' => 'text',
        'sort' => 1,
    ]);

    $this->actingAs($user)
        ->put(route('admin.lessons.update', $lesson), [
            'title' => ['id' => 'Pelajaran Diperbarui'],
            'type' => 'text',
            'content' => ['id' => 'Konten baru'],
            'duration' => 15,
            'sort' => 3,
            'is_preview' => '1',
        ])
        ->assertRedirect(route('admin.courses.edit', $course->uuid));

    $lesson->refresh();
    expect($lesson->getTranslation('title', 'id'))->toBe('Pelajaran Diperbarui')
        ->and($lesson->duration)->toBe(15)
        ->and($lesson->sort)->toBe(3)
        ->and($lesson->is_preview)->toBeTrue();

    $this->actingAs($user)
        ->delete(route('admin.lessons.destroy', $lesson))
        ->assertRedirect(route('admin.courses.edit', $course->uuid));

    $this->assertSoftDeleted('lessons', ['id' => $lesson->id]);
});

it('lets an admin update a quiz question', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Kuis Ubah'],
        'slug' => 'kursus-kuis-ubah',
    ]);
    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => ['id' => 'Modul Kuis'],
        'sort' => 1,
    ]);
    $lesson = Lesson::create([
        'module_id' => $module->id,
        'title' => ['id' => 'Asesmen'],
        'type' => 'quiz',
        'sort' => 1,
    ]);
    $quiz = Quiz::create(['lesson_id' => $lesson->id]);
    $question = $quiz->questions()->create([
        'question' => 'Pertanyaan lama',
        'options' => ['A', 'B', 'C', 'D'],
        'correct_index' => 2,
        'sort' => 1,
    ]);

    $this->actingAs($user)
        ->put(route('admin.quiz-questions.update', $question), [
            'question' => 'Pertanyaan baru',
            'options' => ['Satu', 'Dua', 'Tiga', '', ''],
            'correct_index' => 1,
        ])
        ->assertRedirect(route('admin.courses.edit', $course->uuid));

    $question->refresh();
    expect($question->question)->toBe('Pertanyaan baru')
        ->and($question->options)->toBe(['Satu', 'Dua', 'Tiga'])
        ->and($question->correct_index)->toBe(1);
});

it('rejects storing a course with duplicate slug in the same organization', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();
    Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Existing'],
        'slug' => 'existing-slug',
    ]);

    $this->actingAs($user)->post(route('admin.courses.store'), [
        'title' => ['id' => 'Duplikat'],
        'slug' => 'existing-slug',
        'level' => 'beginner',
        'access_type' => 'free',
        'certificate_type' => 'free',
        'status' => 'draft',
    ])->assertSessionHasErrors('slug');
});

it('allows the same slug in different organizations', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();

    $otherOrg = Organization::create(['name' => 'Org Lain', 'slug' => 'org-lain-course']);
    Course::create([
        'organization_id' => $otherOrg->id,
        'title' => ['id' => 'Kursus Org Lain'],
        'slug' => 'shared-slug',
    ]);

    $response = $this->actingAs($user)->post(route('admin.courses.store'), [
        'title' => ['id' => 'Kursus Org Kita'],
        'slug' => 'shared-slug',
        'level' => 'beginner',
        'access_type' => 'free',
        'certificate_type' => 'free',
        'status' => 'draft',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.courses.index'));
    expect(Course::withoutGlobalScopes()->where('slug', 'shared-slug')->count())->toBe(2);
});

it('shows the course edit page', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Edit'],
        'slug' => 'kursus-edit',
    ]);

    $this->actingAs($user)
        ->get(route('admin.courses.edit', $course->uuid))
        ->assertOk();
});

it('updates an existing course', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Sebelum Update'],
        'slug' => 'sebelum-update',
        'status' => 'draft',
    ]);

    $response = $this->actingAs($user)->put(route('admin.courses.update', $course->uuid), [
        'title' => ['id' => 'Sesudah Update', 'en' => 'After Update'],
        'slug' => 'sesudah-update',
        'level' => 'intermediate',
        'access_type' => 'donation',
        'certificate_type' => 'free',
        'status' => 'published',
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.courses.index'));
    $course->refresh();
    expect($course->getTranslation('title', 'id'))->toBe('Sesudah Update')
        ->and($course->slug)->toBe('sesudah-update')
        ->and($course->status)->toBe('published');
});

it('deletes a course with soft delete', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Hapus'],
        'slug' => 'kursus-hapus',
    ]);

    $this->actingAs($user)
        ->delete(route('admin.courses.destroy', $course->uuid))
        ->assertRedirect(route('admin.courses.index'));

    $this->assertSoftDeleted('courses', ['id' => $course->id]);
});

it('returns 403 when updating a course without permission', function () {
    $organization = Organization::create(['name' => 'Org', 'slug' => 'org-403-course']);
    Session::put('organization_id', $organization->id);
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Test'],
        'slug' => 'test-403-course',
    ]);

    $this->actingAs($user)
        ->put(route('admin.courses.update', $course->uuid), [
            'title' => ['id' => 'Ubah'],
            'slug' => 'ubah',
            'level' => 'beginner',
            'access_type' => 'free',
            'certificate_type' => 'free',
            'status' => 'draft',
        ])
        ->assertForbidden();
});

it('returns 404 when editing a course from another organization', function () {
    ['user' => $user] = courseUserWithOrganization();

    $otherOrg = Organization::create(['name' => 'Lain', 'slug' => 'lain-course-idor']);
    $otherCourse = Course::create([
        'organization_id' => $otherOrg->id,
        'title' => ['id' => 'Milik Lain'],
        'slug' => 'milik-lain-course',
    ]);

    $this->actingAs($user)
        ->get(route('admin.courses.edit', $otherCourse->uuid))
        ->assertNotFound();
});

it('saves course requirement (prerequisite) when updating course', function () {
    ['user' => $user, 'organization' => $organization] = courseUserWithOrganization();

    $prereq = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Prasyarat'],
        'slug' => 'kursus-prasyarat',
        'status' => 'published',
    ]);

    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Utama'],
        'slug' => 'kursus-utama',
        'level' => 'intermediate',
        'access_type' => 'free',
        'certificate_type' => 'free',
        'status' => 'draft',
    ]);

    $this->actingAs($user)
        ->put(route('admin.courses.update', $course->uuid), [
            'title' => ['id' => 'Kursus Utama'],
            'slug' => 'kursus-utama',
            'level' => 'intermediate',
            'access_type' => 'free',
            'certificate_type' => 'free',
            'status' => 'draft',
            'prerequisite_course_id' => $prereq->id,
            'min_grade' => 80,
            'min_age' => 18,
            'notes' => 'Sudah lulus kursus prasyarat',
        ])
        ->assertRedirect(route('admin.courses.index'));

    $course->refresh();
    expect($course->requirement)->not->toBeNull();
    expect($course->requirement->prerequisite_course_id)->toBe($prereq->id);
    expect($course->requirement->min_grade)->toBe(80);
    expect($course->requirement->min_age)->toBe(18);
    expect($course->requirement->notes)->toBe('Sudah lulus kursus prasyarat');
});
