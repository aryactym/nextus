<?php

declare(strict_types=1);

use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Learning\Models\Quiz;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::create(['name' => 'Org Sertifikat', 'slug' => 'org-sertifikat']);
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
    $this->otherUser = User::factory()->create(['organization_id' => $this->organization->id]);

    $this->course = Course::create([
        'organization_id' => $this->organization->id,
        'title' => ['id' => 'Kursus Bersertifikat'],
        'slug' => 'kursus-bersertifikat',
        'level' => 'beginner',
        'access_type' => 'free',
        'certificate_type' => 'free',
        'passing_grade' => 70,
        'status' => 'published',
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $this->course->id,
        'title' => ['id' => 'Modul 1'],
        'sort' => 1,
    ]);

    $this->textLesson = Lesson::create([
        'module_id' => $module->id,
        'title' => ['id' => 'Materi dasar'],
        'type' => 'text',
        'sort' => 1,
    ]);

    $this->quizLesson = Lesson::create([
        'module_id' => $module->id,
        'title' => ['id' => 'Asesmen akhir'],
        'type' => 'quiz',
        'sort' => 2,
    ]);

    $quiz = Quiz::create(['lesson_id' => $this->quizLesson->id]);
    $this->firstQuestion = $quiz->questions()->create([
        'question' => 'Pertanyaan pertama',
        'options' => ['Jawaban benar', 'Pilihan salah satu', 'Pilihan salah dua'],
        'correct_index' => 0,
        'sort' => 1,
    ]);
    $this->secondQuestion = $quiz->questions()->create([
        'question' => 'Pertanyaan kedua',
        'options' => ['Pilihan salah satu', 'Jawaban benar', 'Pilihan salah dua'],
        'correct_index' => 1,
        'sort' => 2,
    ]);
});

it('runs the course flow through the final assessment and certificate', function () {
    $this->actingAs($this->user)->post(route('enroll.store', $this->course->slug));

    $this->actingAs($this->user)
        ->post(route('player.complete', ['course' => $this->course->slug, 'lesson' => $this->textLesson->uuid]));

    $this->actingAs($this->user)
        ->get(route('player.show', ['course' => $this->course->slug, 'lesson' => $this->quizLesson->uuid]))
        ->assertOk()
        ->assertSee('Pertanyaan pertama')
        ->assertSee('Pertanyaan kedua')
        ->assertSee('70');

    $this->actingAs($this->user)
        ->post(route('player.quiz.submit', ['course' => $this->course->slug, 'lesson' => $this->quizLesson->uuid]), [
            'answers' => [
                $this->firstQuestion->id => 1,
                $this->secondQuestion->id => 0,
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('quiz_failed');

    $enrollment = Enrollment::query()->firstOrFail();
    expect($enrollment->status)->toBe('active')
        ->and($enrollment->certificate)->toBeNull();

    $this->actingAs($this->user)
        ->post(route('player.quiz.submit', ['course' => $this->course->slug, 'lesson' => $this->quizLesson->uuid]), [
            'answers' => [
                $this->firstQuestion->id => 0,
                $this->secondQuestion->id => 1,
            ],
        ])
        ->assertRedirect()
        ->assertSessionHas('quiz_passed');

    $enrollment->refresh();
    $certificate = $enrollment->certificate;

    expect($enrollment->status)->toBe('completed')
        ->and($enrollment->progress_percent)->toBe(100)
        ->and($enrollment->completed_at)->not->toBeNull()
        ->and($certificate)->not->toBeNull()
        ->and($certificate->code)->toStartWith('NX-');

    $this->actingAs($this->user)
        ->get(route('learning.dashboard'))
        ->assertOk()
        ->assertSee('Sertifikat saya')
        ->assertSee($certificate->code)
        ->assertSee('Lihat sertifikat');

    $this->actingAs($this->user)
        ->get(route('certificates.show', $certificate->code))
        ->assertOk()
        ->assertSee('Sertifikat penyelesaian')
        ->assertSee($this->user->name)
        ->assertSee($this->course->getTranslation('title', 'id'))
        ->assertSee($certificate->code);
});

it('blocks marking a quiz lesson complete without passing the quiz', function () {
    $this->actingAs($this->user)->post(route('enroll.store', $this->course->slug));

    $this->actingAs($this->user)
        ->post(route('player.complete', ['course' => $this->course->slug, 'lesson' => $this->quizLesson->uuid]))
        ->assertSessionHas('error');

    $this->assertDatabaseMissing('lesson_progress', ['lesson_id' => $this->quizLesson->id]);

    expect(Enrollment::query()->firstOrFail()->lessonProgress()->whereNotNull('completed_at')->count())->toBe(0);
});

it('prevents other users from viewing a certificate', function () {
    $this->actingAs($this->user)->post(route('enroll.store', $this->course->slug));
    $this->actingAs($this->user)->post(route('player.complete', ['course' => $this->course->slug, 'lesson' => $this->textLesson->uuid]));
    $this->actingAs($this->user)->post(route('player.quiz.submit', ['course' => $this->course->slug, 'lesson' => $this->quizLesson->uuid]), [
        'answers' => [
            $this->firstQuestion->id => 0,
            $this->secondQuestion->id => 1,
        ],
    ]);

    $certificate = Enrollment::query()->firstOrFail()->certificate;

    $this->actingAs($this->otherUser)
        ->get(route('certificates.show', $certificate->code))
        ->assertForbidden();
});
