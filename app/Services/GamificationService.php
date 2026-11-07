<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Catalog\Models\Course;
use App\Domain\Learning\Models\LessonProgress;
use App\Domain\Learning\Models\Quiz;
use App\Domain\Learning\Models\QuizAttempt;
use App\Models\User;
use App\Models\UserBadge;
use App\Models\XpEvent;

class GamificationService
{
    /**
     * Beri XP sekali untuk event_key tertentu. Bila event_key sudah ada, tidak dobel.
     */
    public function award(User $user, string $eventKey, int $points): XpEvent
    {
        $event = XpEvent::firstOrCreate(
            ['user_id' => $user->id, 'event_key' => $eventKey],
            ['points' => $points, 'created_at' => now()],
        );

        $this->evaluateBadges($user);

        return $event;
    }

    /**
     * Nilai ulang ketiga badge dari aktivitas belajar saat ini.
     */
    public function evaluateBadges(User $user): void
    {
        $this->evaluateTuntas($user);
        $this->evaluateSekaliJalan($user);
        $this->evaluateAlumni($user);
    }

    public function totalXp(User $user): int
    {
        return (int) XpEvent::where('user_id', $user->id)->sum('points');
    }

    /**
     * @return array{level: int, name_id: string, name_en: string, threshold: int, next_threshold: int|null}
     */
    public function levelForXp(int $xp): array
    {
        $levels = collect(config('gamification.levels', []))->sortBy('threshold')->values();

        $current = $levels->first() ?? ['level' => 1, 'name_id' => 'Pemula', 'name_en' => 'Beginner', 'threshold' => 0];
        $nextThreshold = null;

        foreach ($levels as $candidate) {
            if ($xp < (int) $candidate['threshold']) {
                $nextThreshold = (int) $candidate['threshold'];
                break;
            }
            $current = $candidate;
        }

        return [
            'level' => (int) $current['level'],
            'name_id' => (string) ($current['name_id'] ?? ''),
            'name_en' => (string) ($current['name_en'] ?? ''),
            'threshold' => (int) ($current['threshold'] ?? 0),
            'next_threshold' => $nextThreshold,
        ];
    }

    public function currentLevel(User $user): array
    {
        return $this->levelForXp($this->totalXp($user));
    }

    public function completedLessonCount(User $user): int
    {
        return (int) LessonProgress::query()
            ->whereHas('enrollment', fn ($query) => $query->where('user_id', $user->id))
            ->whereNotNull('completed_at')
            ->distinct('lesson_id')
            ->count('lesson_id');
    }

    public function tuntasTierFor(int $completedCount): ?string
    {
        $tiers = config('gamification.tuntas_tiers', []);

        $current = null;
        foreach ($tiers as $tier => $threshold) {
            if ($completedCount >= (int) $threshold) {
                $current = (string) $tier;
            }
        }

        return $current;
    }

    /**
     * @return array{tier: string, threshold: int}|null
     */
    public function nextTuntasTier(int $completedCount): ?array
    {
        foreach ((array) config('gamification.tuntas_tiers', []) as $tier => $threshold) {
            if ($completedCount < (int) $threshold) {
                return ['tier' => (string) $tier, 'threshold' => (int) $threshold];
            }
        }

        return null;
    }

    /**
     * Daftar badge dengan status (diperoleh/terkunci) untuk UI.
     *
     * @return array<int, array{
     *     slug: string,
     *     badge: array<string, mixed>,
     *     awarded: UserBadge|null,
     *     tier: string|null,
     *     earned: bool,
     *     progress_current: int|null,
     *     progress_total: int|null
     * }>
     */
    public function badgesForUser(User $user): array
    {
        $awarded = UserBadge::where('user_id', $user->id)->get()->keyBy('badge');
        $completedCount = $this->completedLessonCount($user);

        return collect(config('gamification.badges', []))
            ->map(function (array $badge) use ($awarded, $completedCount): array {
                $slug = (string) ($badge['slug'] ?? '');
                $record = $awarded->get($slug);
                $tier = $record?->tier;

                $progressCurrent = null;
                $progressTotal = null;

                if ($slug === 'tuntas') {
                    $progressCurrent = $completedCount;
                    $next = $this->nextTuntasTier($completedCount);
                    $progressTotal = $next['threshold'] ?? null;
                }

                return [
                    'slug' => $slug,
                    'badge' => $badge,
                    'awarded' => $record,
                    'tier' => $tier,
                    'earned' => $record !== null,
                    'progress_current' => $progressCurrent,
                    'progress_total' => $progressTotal,
                ];
            })
            ->values()
            ->all();
    }

    private function evaluateTuntas(User $user): void
    {
        $count = $this->completedLessonCount($user);
        $tier = $this->tuntasTierFor($count);

        if ($tier === null) {
            return;
        }

        $existing = UserBadge::where('user_id', $user->id)->where('badge', 'tuntas')->first();

        if ($existing === null) {
            UserBadge::create([
                'user_id' => $user->id,
                'badge' => 'tuntas',
                'tier' => $tier,
                'awarded_at' => now(),
            ]);

            return;
        }

        $thresholds = config('gamification.tuntas_tiers', []);
        $existingThreshold = (int) ($thresholds[$existing->tier] ?? 0);
        $newThreshold = (int) ($thresholds[$tier] ?? 0);

        if ($newThreshold > $existingThreshold) {
            $existing->update(['tier' => $tier, 'awarded_at' => now()]);
        }
    }

    private function evaluateSekaliJalan(User $user): void
    {
        if ($this->hasFirstAttemptFinalPass($user)) {
            UserBadge::firstOrCreate(
                ['user_id' => $user->id, 'badge' => 'sekali-jalan'],
                ['tier' => null, 'awarded_at' => now()],
            );
        }
    }

    private function evaluateAlumni(User $user): void
    {
        if ($user->certificates()->whereNull('revoked_at')->exists()) {
            UserBadge::firstOrCreate(
                ['user_id' => $user->id, 'badge' => 'alumni'],
                ['tier' => null, 'awarded_at' => now()],
            );
        }
    }

    private function hasFirstAttemptFinalPass(User $user): bool
    {
        $attempts = QuizAttempt::query()
            ->whereHas('enrollment', fn ($query) => $query->where('user_id', $user->id))
            ->where('passed', true)
            ->where('score', '>=', 90)
            ->orderBy('finished_at')
            ->orderBy('created_at')
            ->with(['quiz.lesson', 'enrollment.course'])
            ->get();

        foreach ($attempts as $attempt) {
            $earlierExists = QuizAttempt::where('enrollment_id', $attempt->enrollment_id)
                ->where('quiz_id', $attempt->quiz_id)
                ->where(function ($query) use ($attempt): void {
                    $query->where('finished_at', '<', $attempt->finished_at)
                        ->orWhere(fn ($q) => $q->where('finished_at', $attempt->finished_at)->where('id', '<', $attempt->id));
                })
                ->exists();

            if ($earlierExists) {
                continue;
            }

            if ($this->isFinalQuiz($attempt->quiz, $attempt->enrollment->course)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Asesmen akhir = kuis pelajaran terakhir dalam urutan kursus.
     */
    public function isFinalQuiz(?Quiz $quiz, ?Course $course): bool
    {
        if ($quiz === null || $course === null) {
            return false;
        }

        $course->loadMissing('modules.lessons');

        $lastLessonId = $course->modules->flatMap->lessons->last()?->id;

        return $lastLessonId !== null && (int) $quiz->lesson_id === (int) $lastLessonId;
    }
}
