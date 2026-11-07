<?php

declare(strict_types=1);

use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::create(['name' => 'Org Belajar', 'slug' => 'org-belajar']);
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);

    $this->course = Course::create([
        'organization_id' => $this->organization->id,
        'title' => ['id' => 'Kursus Belajar'],
        'slug' => 'kursus-belajar',
        'level' => 'beginner',
        'access_type' => 'free',
        'status' => 'published',
        'published_at' => now(),
    ]);
    $module = CourseModule::create(['course_id' => $this->course->id, 'title' => ['id' => 'Modul 1'], 'sort' => 1]);
    $this->previewLesson = Lesson::create(['module_id' => $module->id, 'title' => ['id' => 'Preview'], 'type' => 'text', 'is_preview' => true, 'sort' => 1]);
    $this->lockedLesson = Lesson::create(['module_id' => $module->id, 'title' => ['id' => 'Terkunci'], 'type' => 'text', 'sort' => 2]);
});

it('redirects guests to login for the learner dashboard', function () {
    $this->get(route('learning.dashboard'))->assertRedirect(route('login'));
});

it('shows the learner dashboard with enrolled course', function () {
    Enrollment::create(['user_id' => $this->user->id, 'course_id' => $this->course->id, 'status' => 'active', 'enrolled_at' => now()]);

    $this->actingAs($this->user)
        ->get(route('learning.dashboard'))
        ->assertOk()
        ->assertSee('Kursus Belajar');
});

it('shows empty learner dashboard without enrollment', function () {
    $this->actingAs($this->user)
        ->get(route('learning.dashboard'))
        ->assertOk()
        ->assertSee('Belum ada kursus');
});

it('allows guests to view preview lessons', function () {
    $this->get(route('player.show', ['course' => 'kursus-belajar', 'lesson' => $this->previewLesson->uuid]))
        ->assertOk()
        ->assertSee('Preview');
});

it('redirects guests to login for locked lessons', function () {
    $this->get(route('player.show', ['course' => 'kursus-belajar', 'lesson' => $this->lockedLesson->uuid]))
        ->assertRedirect(route('login'));
});

it('redirects non-enrolled users to the course page', function () {
    $this->actingAs($this->user)
        ->get(route('player.show', ['course' => 'kursus-belajar', 'lesson' => $this->lockedLesson->uuid]))
        ->assertRedirect(route('catalog.show', 'kursus-belajar'));
});

it('shows the player to enrolled users', function () {
    Enrollment::create(['user_id' => $this->user->id, 'course_id' => $this->course->id, 'status' => 'active', 'enrolled_at' => now()]);

    $this->actingAs($this->user)
        ->get(route('player.show', ['course' => 'kursus-belajar', 'lesson' => $this->lockedLesson->uuid]))
        ->assertOk()
        ->assertSee('Terkunci');
});

it('marks a lesson complete and updates progress', function () {
    Enrollment::create(['user_id' => $this->user->id, 'course_id' => $this->course->id, 'status' => 'active', 'enrolled_at' => now()]);

    $this->actingAs($this->user)
        ->post(route('player.complete', ['course' => 'kursus-belajar', 'lesson' => $this->previewLesson->uuid]))
        ->assertRedirect();

    $this->assertDatabaseHas('lesson_progress', ['lesson_id' => $this->previewLesson->id]);
    $enrollment = Enrollment::where('user_id', $this->user->id)->first();
    expect($enrollment->progress_percent)->toBe(50);
});

it('completes enrollment when all lessons are done', function () {
    Enrollment::create(['user_id' => $this->user->id, 'course_id' => $this->course->id, 'status' => 'active', 'enrolled_at' => now()]);

    $this->actingAs($this->user)->post(route('player.complete', ['course' => 'kursus-belajar', 'lesson' => $this->previewLesson->uuid]));
    $this->actingAs($this->user)->post(route('player.complete', ['course' => 'kursus-belajar', 'lesson' => $this->lockedLesson->uuid]));

    $enrollment = Enrollment::where('user_id', $this->user->id)->first();
    expect($enrollment->progress_percent)->toBe(100)
        ->and($enrollment->status)->toBe('completed')
        ->and($enrollment->completed_at)->not->toBeNull();
});
