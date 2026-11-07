<?php

declare(strict_types=1);

use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Organization\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::create(['name' => 'Org Publik', 'slug' => 'org-publik']);
});

function publicPublishedCourse(Organization $organization, array $overrides = []): Course
{
    return Course::create(array_merge([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Pengantar Zakat', 'en' => 'Introduction to Zakat'],
        'slug' => 'pengantar-zakat',
        'level' => 'beginner',
        'access_type' => 'free',
        'certificate_type' => 'free',
        'status' => 'published',
        'published_at' => now(),
    ], $overrides));
}

it('shows the public catalog index', function () {
    publicPublishedCourse($this->organization);

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('Pengantar Zakat');
});

it('shows a published course detail', function () {
    $course = publicPublishedCourse($this->organization);
    CourseModule::create(['course_id' => $course->id, 'title' => ['id' => 'Modul 1'], 'sort' => 1]);
    Lesson::create(['module_id' => $course->modules->first()->id, 'title' => ['id' => 'Kenapa zakat'], 'type' => 'video', 'sort' => 1]);

    $this->get(route('catalog.show', $course->slug))
        ->assertOk()
        ->assertSee('Pengantar Zakat')
        ->assertSee('Modul 1')
        ->assertSee('Tentang kursus')
        ->assertSee('Kurikulum')
        ->assertSee('Penyusun kursus');
});

it('returns 404 for a draft course', function () {
    publicPublishedCourse($this->organization, ['slug' => 'draft-kursus', 'status' => 'draft']);

    $this->get(route('catalog.show', 'draft-kursus'))->assertNotFound();
});

it('returns 404 for a missing slug', function () {
    $this->get(route('catalog.show', 'tidak-ada'))->assertNotFound();
});

it('excludes draft courses from the catalog', function () {
    publicPublishedCourse($this->organization, ['slug' => 'hidden-draft', 'status' => 'draft', 'title' => ['id' => 'Kursus Tersembunyi']]);

    $this->get(route('catalog.index'))->assertOk()->assertDontSee('Kursus Tersembunyi');
});

it('filters the catalog by level access and duration', function () {
    publicPublishedCourse($this->organization, [
        'slug' => 'pemula-gratis',
        'title' => ['id' => 'Pemula Gratis'],
        'level' => 'beginner',
        'access_type' => 'free',
        'duration_minutes' => 120,
    ]);

    publicPublishedCourse($this->organization, [
        'slug' => 'lanjutan-berbayar',
        'title' => ['id' => 'Lanjutan Berbayar'],
        'level' => 'advanced',
        'access_type' => 'paid',
        'duration_minutes' => 420,
    ]);

    $this->get(route('catalog.index', [
        'level' => ['advanced'],
        'akses' => ['paid'],
        'durasi' => ['l'],
    ]))
        ->assertOk()
        ->assertSee('Lanjutan Berbayar')
        ->assertDontSee('Pemula Gratis');
});

it('paginates the catalog at twelve courses per page', function () {
    foreach (range(1, 13) as $index) {
        publicPublishedCourse($this->organization, [
            'slug' => 'kursus-'.$index,
            'title' => ['id' => 'Kursus '.str_pad((string) $index, 2, '0', STR_PAD_LEFT)],
        ]);
    }

    $this->get(route('catalog.index'))
        ->assertOk()
        ->assertSee('Menampilkan 12 dari 13 kursus');

    $this->get(route('catalog.index', ['page' => 2]))
        ->assertOk()
        ->assertSee('Menampilkan 1 dari 13 kursus');
});
