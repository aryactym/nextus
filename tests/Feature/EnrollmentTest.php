<?php

declare(strict_types=1);

use App\Domain\Catalog\Models\Course;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::create(['name' => 'Org Daftar', 'slug' => 'org-daftar']);
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
});

function enrollmentCourse(Organization $organization, array $overrides = []): Course
{
    return Course::create(array_merge([
        'organization_id' => $organization->id,
        'title' => ['id' => 'Kursus Daftar'],
        'slug' => 'kursus-daftar',
        'level' => 'beginner',
        'access_type' => 'free',
        'status' => 'published',
        'published_at' => now(),
    ], $overrides));
}

it('redirects guests to login when enrolling', function () {
    enrollmentCourse($this->organization);

    $this->post(route('enroll.store', 'kursus-daftar'))->assertRedirect(route('login'));
});

it('enrolls a user in a free course', function () {
    enrollmentCourse($this->organization);

    $this->actingAs($this->user)
        ->post(route('enroll.store', 'kursus-daftar'))
        ->assertRedirect(route('catalog.show', 'kursus-daftar'));

    $this->assertDatabaseHas('enrollments', ['user_id' => $this->user->id, 'status' => 'active']);
});

it('does not duplicate enrollment on second attempt', function () {
    enrollmentCourse($this->organization);

    $this->actingAs($this->user)->post(route('enroll.store', 'kursus-daftar'));
    $this->actingAs($this->user)->post(route('enroll.store', 'kursus-daftar'));

    expect(Enrollment::where('user_id', $this->user->id)->count())->toBe(1);
});

it('rejects enrollment for paid course', function () {
    enrollmentCourse($this->organization, ['slug' => 'kursus-bayar', 'access_type' => 'paid']);

    $this->actingAs($this->user)
        ->post(route('enroll.store', 'kursus-bayar'))
        ->assertRedirect(route('catalog.show', 'kursus-bayar'))
        ->assertSessionHas('enroll_error');

    expect(Enrollment::where('user_id', $this->user->id)->count())->toBe(0);
});

it('returns 404 for draft course enrollment', function () {
    enrollmentCourse($this->organization, ['slug' => 'kursus-draft', 'status' => 'draft']);

    $this->actingAs($this->user)
        ->post(route('enroll.store', 'kursus-draft'))
        ->assertNotFound();
});
