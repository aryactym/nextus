<?php

declare(strict_types=1);

use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Learning\Models\LessonProgress;
use App\Domain\Learning\Models\Quiz;
use App\Domain\Organization\Models\Organization;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\XpEvent;
use App\Services\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::create(['name' => 'Org Gamifikasi', 'slug' => 'org-gamifikasi']);
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);

    $this->course = Course::create([
        'organization_id' => $this->organization->id,
        'title' => ['id' => 'Kursus Gamifikasi'],
        'slug' => 'kursus-gamifikasi',
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
    $this->question = $quiz->questions()->create([
        'question' => 'Satu tambah satu?',
        'options' => ['2', '3'],
        'correct_index' => 0,
        'sort' => 1,
    ]);

    $this->actingAs($this->user)->post(route('enroll.store', $this->course->slug));
});

it('does not double XP when the same action is repeated', function () {
    $this->actingAs($this->user)
        ->post(route('player.complete', ['course' => $this->course->slug, 'lesson' => $this->textLesson->uuid]))
        ->assertRedirect();

    $this->actingAs($this->user)
        ->post(route('player.complete', ['course' => $this->course->slug, 'lesson' => $this->textLesson->uuid]))
        ->assertRedirect();

    expect(XpEvent::where('user_id', $this->user->id)->where('event_key', 'lesson:'.$this->textLesson->id)->count())->toBe(1)
        ->and((int) XpEvent::where('user_id', $this->user->id)->sum('points'))->toBe(10);
});

it('levels up exactly at each threshold', function () {
    $service = app(GamificationService::class);

    expect($service->levelForXp(0)['level'])->toBe(1)
        ->and($service->levelForXp(99)['level'])->toBe(1)
        ->and($service->levelForXp(100)['level'])->toBe(2)
        ->and($service->levelForXp(299)['level'])->toBe(2)
        ->and($service->levelForXp(300)['level'])->toBe(3)
        ->and($service->levelForXp(599)['level'])->toBe(3)
        ->and($service->levelForXp(600)['level'])->toBe(4)
        ->and($service->levelForXp(999)['level'])->toBe(4)
        ->and($service->levelForXp(1000)['level'])->toBe(5);
});

it('upgrades Tuntas tier at 5, 25, 100, and 400 completed lessons', function () {
    $service = app(GamificationService::class);

    expect($service->tuntasTierFor(0))->toBeNull()
        ->and($service->tuntasTierFor(4))->toBeNull()
        ->and($service->tuntasTierFor(5))->toBe('default')
        ->and($service->tuntasTierFor(24))->toBe('default')
        ->and($service->tuntasTierFor(25))->toBe('bronze')
        ->and($service->tuntasTierFor(99))->toBe('bronze')
        ->and($service->tuntasTierFor(100))->toBe('silver')
        ->and($service->tuntasTierFor(399))->toBe('silver')
        ->and($service->tuntasTierFor(400))->toBe('gold')
        ->and($service->tuntasTierFor(500))->toBe('gold');
});

it('awards Tuntas default badge after five completed lessons', function () {
    $enrollment = Enrollment::query()->firstOrFail();

    for ($i = 1; $i <= 5; $i++) {
        $lesson = Lesson::create([
            'module_id' => $this->textLesson->module_id,
            'title' => ['id' => 'Pelajaran pelengkap '.$i],
            'type' => 'text',
            'sort' => 10 + $i,
        ]);

        LessonProgress::create([
            'enrollment_id' => $enrollment->id,
            'lesson_id' => $lesson->id,
            'completed_at' => now(),
        ]);
    }

    app(GamificationService::class)->evaluateBadges($this->user->refresh());

    expect(UserBadge::where('user_id', $this->user->id)->where('badge', 'tuntas')->first()?->tier)->toBe('default');
});

it('has a badge image file for every slug and tier in config', function () {
    foreach ((array) config('gamification.badges', []) as $badge) {
        foreach ((array) ($badge['images'] ?? []) as $tier) {
            $path = public_path($tier);
            expect(file_exists($path))->toBeTrue("Gambar badge hilang: {$tier}");
            expect(filesize($path))->toBeGreaterThan(0);
        }
    }
});

it('still marks a lesson complete when the gamification service throws', function () {
    $this->mock(GamificationService::class, function ($mock) {
        $mock->shouldReceive('award')->andThrow(new RuntimeException('gamifikasi booom'));
    });

    $this->actingAs($this->user)
        ->post(route('player.complete', ['course' => $this->course->slug, 'lesson' => $this->textLesson->uuid]))
        ->assertRedirect();

    $this->assertDatabaseHas('lesson_progress', ['lesson_id' => $this->textLesson->id]);
});

it('awards XP, badges, and level through the full course flow', function () {
    $this->actingAs($this->user)
        ->post(route('player.complete', ['course' => $this->course->slug, 'lesson' => $this->textLesson->uuid]));

    $this->actingAs($this->user)
        ->post(route('player.quiz.submit', ['course' => $this->course->slug, 'lesson' => $this->quizLesson->uuid]), [
            'answers' => [$this->question->id => 0],
        ])
        ->assertSessionHas('quiz_passed');

    $keys = XpEvent::where('user_id', $this->user->id)->pluck('event_key')->all();
    sort($keys);

    expect($keys)->toContain('lesson:'.$this->textLesson->id)
        ->and($keys)->toContain('quiz:'.$this->quizLesson->quiz->id)
        ->and($keys)->toContain('final:'.$this->course->id)
        ->and($keys)->toContain('course:'.$this->course->id)
        ->and((int) XpEvent::where('user_id', $this->user->id)->sum('points'))->toBe(285)
        ->and(UserBadge::where('user_id', $this->user->id)->pluck('badge')->all())->toContain('sekali-jalan')
        ->and(UserBadge::where('user_id', $this->user->id)->pluck('badge')->all())->toContain('alumni');

    $this->actingAs($this->user)
        ->get(route('learning.dashboard'))
        ->assertOk()
        ->assertSee('Level 2 Penyimak, 285/300 XP')
        ->assertSee('Lihat pencapaian');

    $this->actingAs($this->user)
        ->get(route('learning.achievements'))
        ->assertOk()
        ->assertSee('Tuntas')
        ->assertSee('Sekali Jalan')
        ->assertSee('Alumni');
});
