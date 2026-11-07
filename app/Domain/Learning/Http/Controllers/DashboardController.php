<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Learning\Models\Enrollment;
use App\Http\Controllers\Controller;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $enrollments = $request->user()->enrollments()
            ->with([
                'course' => fn ($q) => $q->withoutGlobalScope('organization')->with(['category', 'modules.lessons']),
                'certificate',
            ])
            ->orderByDesc('enrolled_at')
            ->get();

        $completedByEnrollment = $enrollments->mapWithKeys(fn (Enrollment $e) => [
            $e->id => $e->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id'),
        ]);

        $nextLessonByEnrollment = $enrollments->mapWithKeys(function (Enrollment $e) use ($completedByEnrollment) {
            $completed = $completedByEnrollment[$e->id];
            $first = $e->course->modules->flatMap->lessons->first(fn ($l) => ! $completed->contains($l->id));

            return [$e->id => $first];
        });

        $certificates = $request->user()->certificates()
            ->with('course')
            ->whereNull('revoked_at')
            ->latest('issued_at')
            ->get();

        $gamification = app(GamificationService::class);
        $totalXp = $gamification->totalXp($request->user());
        $level = $gamification->levelForXp($totalXp);
        $badges = $gamification->badgesForUser($request->user());

        $nextThreshold = $level['next_threshold'];
        $currentThreshold = (int) $level['threshold'];
        $levelProgressPercent = $nextThreshold !== null && $nextThreshold > $currentThreshold
            ? (int) round(($totalXp - $currentThreshold) / ($nextThreshold - $currentThreshold) * 100)
            : 100;

        return view('learning.dashboard', compact(
            'enrollments',
            'completedByEnrollment',
            'nextLessonByEnrollment',
            'certificates',
            'totalXp',
            'level',
            'badges',
            'levelProgressPercent',
        ));
    }

    public function achievements(Request $request, GamificationService $gamification): View
    {
        $totalXp = $gamification->totalXp($request->user());
        $level = $gamification->levelForXp($totalXp);
        $badges = $gamification->badgesForUser($request->user());

        return view('learning.achievements', compact('totalXp', 'level', 'badges'));
    }
}
