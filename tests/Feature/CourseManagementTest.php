<?php

declare(strict_types=1);

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Course;
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
