<?php

use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates a module for the course in the request path', function () {
    $organization = Organization::create([
        'name' => 'Organisasi Uji',
        'slug' => 'organisasi-uji',
    ]);
    Session::put('organization_id', $organization->id);
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $user->givePermissionTo(Permission::create([
        'name' => 'manage courses',
        'guard_name' => 'web',
    ]));
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Uji'],
        'slug' => 'kursus-uji',
    ]);

    $response = $this->actingAs($user)
        ->post(route('admin.courses.modules.store', $course), [
            'title' => ['id' => 'Modul Pertama'],
        ]);

    $response->assertRedirect(route('admin.courses.edit', $course));
    $module = CourseModule::query()->where('course_id', $course->id)->firstOrFail();
    $this->assertModelExists($module);
    expect($module->getTranslation('title', 'id'))->toBe('Modul Pertama');
});

it('creates a lesson for the module in the request path', function () {
    $organization = Organization::create([
        'name' => 'Organisasi Uji',
        'slug' => 'organisasi-uji',
    ]);
    Session::put('organization_id', $organization->id);
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $user->givePermissionTo(Permission::create([
        'name' => 'manage courses',
        'guard_name' => 'web',
    ]));
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Uji'],
        'slug' => 'kursus-uji',
    ]);
    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => ['id' => 'Modul Pertama'],
    ]);

    $response = $this->actingAs($user)
        ->post(route('admin.modules.lessons.store', $module), [
            'title' => ['id' => 'Pelajaran Pertama', 'en' => 'First Lesson'],
            'type' => 'text',
            'content' => ['id' => 'Isi pelajaran'],
        ]);

    $response->assertRedirect(route('admin.courses.edit', $course));
    $lesson = Lesson::query()->where('module_id', $module->id)->firstOrFail();
    $this->assertModelExists($lesson);
    expect($lesson->getTranslation('title', 'en'))->toBe('First Lesson');
    expect($lesson->getTranslation('content', 'id'))->toBe('Isi pelajaran');
});

it('stores PDF lesson files on the private uploads disk', function () {
    $organization = Organization::create([
        'name' => 'Organisasi Uji',
        'slug' => 'organisasi-uji',
    ]);
    Session::put('organization_id', $organization->id);
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $user->givePermissionTo(Permission::create([
        'name' => 'manage courses',
        'guard_name' => 'web',
    ]));
    $course = Course::create([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Uji'],
        'slug' => 'kursus-uji',
    ]);
    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => ['id' => 'Modul Pertama'],
    ]);
    Storage::fake('uploads');

    $response = $this->actingAs($user)
        ->post(route('admin.modules.lessons.store', $module), [
            'title' => ['id' => 'Materi PDF'],
            'type' => 'pdf',
            'file' => UploadedFile::fake()->createWithContent('materi.pdf', '%PDF-1.4 materi uji'),
        ]);

    $response->assertRedirect(route('admin.courses.edit', $course));
    $lesson = Lesson::query()->where('module_id', $module->id)->firstOrFail();
    expect($lesson->file_path)->not->toBeNull();
    Storage::disk('uploads')->assertExists($lesson->file_path);
});

it('returns 404 when a user deletes a module from another organization', function () {
    $organization = Organization::create([
        'name' => 'Organisasi Pengguna',
        'slug' => 'organisasi-pengguna',
    ]);
    $otherOrganization = Organization::create([
        'name' => 'Organisasi Lain',
        'slug' => 'organisasi-lain',
    ]);
    Session::put('organization_id', $organization->id);
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $user->givePermissionTo(Permission::create([
        'name' => 'manage courses',
        'guard_name' => 'web',
    ]));
    $otherCourse = Course::create([
        'organization_id' => $otherOrganization->id,
        'title' => ['id' => 'Kursus Organisasi Lain'],
        'slug' => 'kursus-organisasi-lain',
    ]);
    $otherModule = CourseModule::create([
        'course_id' => $otherCourse->id,
        'title' => ['id' => 'Modul Organisasi Lain'],
    ]);

    $response = $this->actingAs($user)
        ->delete(route('admin.modules.destroy', $otherModule));

    $response->assertNotFound();
    $this->assertModelExists($otherModule);
});

it('returns 404 when a user deletes a lesson from another organization', function () {
    $organization = Organization::create([
        'name' => 'Organisasi Pengguna',
        'slug' => 'organisasi-pengguna',
    ]);
    $otherOrganization = Organization::create([
        'name' => 'Organisasi Lain',
        'slug' => 'organisasi-lain',
    ]);
    Session::put('organization_id', $organization->id);
    $user = User::factory()->create(['organization_id' => $organization->id]);
    $user->givePermissionTo(Permission::create([
        'name' => 'manage courses',
        'guard_name' => 'web',
    ]));
    $otherCourse = Course::create([
        'organization_id' => $otherOrganization->id,
        'title' => ['id' => 'Kursus Organisasi Lain'],
        'slug' => 'kursus-organisasi-lain',
    ]);
    $otherModule = CourseModule::create([
        'course_id' => $otherCourse->id,
        'title' => ['id' => 'Modul Organisasi Lain'],
    ]);
    $otherLesson = Lesson::create([
        'module_id' => $otherModule->id,
        'title' => ['id' => 'Pelajaran Organisasi Lain'],
        'type' => 'text',
    ]);

    $response = $this->actingAs($user)
        ->delete(route('admin.lessons.destroy', $otherLesson));

    $response->assertNotFound();
    $this->assertModelExists($otherLesson);
});
