<?php

declare(strict_types=1);

namespace App\Domain\Learning\Http\Controllers;

use App\Domain\Catalog\Models\Course;
use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Learning\Models\Enrollment;
use App\Domain\Learning\Models\QuizAttempt;
use App\Domain\Learning\Services\CourseProgressService;
use App\Http\Controllers\Controller;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class PlayerController extends Controller
{
    public function show(Request $request, string $courseSlug, Lesson $lesson): View|RedirectResponse
    {
        $course = $this->resolveCourse($courseSlug);
        $this->ensureLessonBelongsToCourse($course, $lesson);

        $enrollment = $request->user()?->enrollments()
            ->where('course_id', $course->id)->first();

        if (! $lesson->is_preview && ! $enrollment) {
            return $request->user() === null
                ? redirect()->to(LaravelLocalization::getLocalizedURL(app()->getLocale(), route('login')))
                : redirect()->to(LaravelLocalization::getLocalizedURL(app()->getLocale(), route('catalog.show', $course->slug)));
        }

        $module = $lesson->module;

        // Pre-test wajib: jika ada modul pre_test sebelum modul ini yang belum selesai, redirect
        $sortedModules = $course->modules->sortBy('sort')->values();
        $currentModuleIndex = $sortedModules->search(fn ($m) => $m->id === $module->id);
        if ($currentModuleIndex !== false && $enrollment) {
            for ($i = 0; $i < $currentModuleIndex; $i++) {
                $prevMod = $sortedModules[$i];
                $quiz = $prevMod->type === 'pre_test'
                    ? $prevMod->preTest
                    : ($prevMod->type === 'post_test' ? $prevMod->postTest : null);

                if ($quiz && $quiz->questions->isNotEmpty()) {
                    $passed = $enrollment->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists();
                    if (! $passed) {
                        $routeName = $prevMod->type === 'post_test' ? 'module.posttest' : 'module.pretest';

                        return redirect()->to(LaravelLocalization::getLocalizedURL(app()->getLocale(), route($routeName, ['course' => $course->slug, 'module' => $prevMod->uuid])));
                    }
                }
            }
        }

        // Backward compat: quiz-on-module pre-test/post-test
        $preTest = $module->preTest;
        if ($preTest && $preTest->questions->isNotEmpty() && $enrollment) {
            $attempted = $enrollment->quizAttempts()->where('quiz_id', $preTest->id)->exists();
            if (! $attempted) {
                return redirect()->to(LaravelLocalization::getLocalizedURL(app()->getLocale(), route('module.pretest', ['course' => $course->slug, 'module' => $module->uuid])));
            }
        }

        $completedLessonIds = $enrollment
            ? $enrollment->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id')
            : collect();

        /** @var Lesson|null $previousLesson */
        $previousLesson = $this->neighborLesson($course, $lesson, -1);
        /** @var Lesson|null $nextLesson */
        $nextLesson = $this->neighborLesson($course, $lesson, 1);

        $fileUrl = ($lesson->file_path && $request->user())
            ? URL::signedRoute('admin.uploads.show', ['path' => $lesson->file_path])
            : null;

        $totalLessons = $course->modules->flatMap->lessons->count();

        $quiz = $lesson->type === 'quiz'
            ? $lesson->quiz()->with('questions')->first()
            : null;

        $lastAttempt = ($enrollment && $quiz)
            ? $enrollment->quizAttempts()->where('quiz_id', $quiz->id)->latest()->first()
            : null;

        $passingScore = $quiz?->passing_score ?? $course->passing_grade ?? 75;

        return view('learning.player', compact(
            'course', 'lesson', 'enrollment', 'completedLessonIds',
            'previousLesson', 'nextLesson', 'fileUrl', 'totalLessons',
            'quiz', 'lastAttempt', 'passingScore'
        ));
    }

    public function complete(
        Request $request,
        string $courseSlug,
        Lesson $lesson,
        CourseProgressService $progress,
    ): RedirectResponse {
        $course = $this->resolveCourse($courseSlug);
        $this->ensureLessonBelongsToCourse($course, $lesson);

        $enrollment = $request->user()->enrollments()
            ->where('course_id', $course->id)->firstOrFail();

        if ($lesson->type === 'quiz' && $lesson->quiz()->whereHas('questions')->exists()) {
            return back()->with('error', 'Pelajaran ini berisi kuis. Selesaikan kuis untuk menandainya selesai.');
        }

        $progress->markLessonCompleted($enrollment, $lesson);

        try {
            app(GamificationService::class)->award(
                $request->user(),
                'lesson:'.$lesson->id,
                (int) config('gamification.xp.lesson', 10),
            );
        } catch (\Throwable $e) {
            Log::warning('Gamifikasi lesson gagal.', ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id, 'error' => $e->getMessage()]);
        }

        $next = $this->nextIncompleteLesson($course, $lesson, $enrollment->refresh());

        // Jika semua lesson di modul selesai, arahkan ke post-test modul (jika ada)
        $postTestRedirect = $this->redirectToNextPostTest($course, $lesson, $enrollment);
        if ($postTestRedirect) {
            return $postTestRedirect;
        }

        return redirect()->to(
            LaravelLocalization::getLocalizedURL(app()->getLocale(), route(
                'player.show',
                ['course' => $course->slug, 'lesson' => $next?->uuid ?? $lesson->uuid]
            ))
        );
    }

    public function submitQuiz(
        Request $request,
        string $courseSlug,
        Lesson $lesson,
        CourseProgressService $progress,
    ): RedirectResponse {
        $course = $this->resolveCourse($courseSlug);
        $this->ensureLessonBelongsToCourse($course, $lesson);

        if ($lesson->type !== 'quiz') {
            abort(404);
        }

        $enrollment = $request->user()->enrollments()
            ->where('course_id', $course->id)->firstOrFail();

        $quiz = $lesson->quiz()->with('questions')->first();

        if (! $quiz || $quiz->questions->isEmpty()) {
            return back()->with('error', 'Asesmen belum tersedia. Hubungi pengelola kursus.');
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer', 'min:0'],
        ]);

        $answers = $validated['answers'];
        $correct = 0;

        foreach ($quiz->questions as $question) {
            $selected = $answers[$question->id] ?? null;

            if ($selected !== null && (int) $selected === $question->correct_index) {
                $correct++;
            }
        }

        $total = $quiz->questions->count();
        $score = (int) round($correct / $total * 100);
        $passingScore = $quiz->passing_score ?? $course->passing_grade ?? 75;
        $passed = $score >= $passingScore;

        QuizAttempt::create([
            'enrollment_id' => $enrollment->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'passed' => $passed,
            'answers' => $answers,
            'finished_at' => now(),
        ]);

        if ($passed) {
            $progress->markLessonCompleted($enrollment, $lesson);

            try {
                $gamification = app(GamificationService::class);
                $gamification->award(
                    $request->user(),
                    'quiz:'.$quiz->id,
                    (int) config('gamification.xp.quiz', 25),
                );

                if ($gamification->isFinalQuiz($quiz, $course)) {
                    $gamification->award(
                        $request->user(),
                        'final:'.$course->id,
                        (int) config('gamification.xp.final_assessment', 100),
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('Gamifikasi kuis gagal.', ['user_id' => $request->user()->id, 'quiz_id' => $quiz->id, 'course_id' => $course->id, 'error' => $e->getMessage()]);
            }

            $redirect = redirect()
                ->to(LaravelLocalization::getLocalizedURL(app()->getLocale(), route('player.show', ['course' => $course->slug, 'lesson' => $lesson->uuid])))
                ->with('quiz_passed', "Selamat, kamu lulus dengan skor {$score}. Nilai minimal {$passingScore}.");

            // Jika semua lesson modul ini selesai (mis. kuis final), ke post-test berikutnya
            $postTestRedirect = $this->redirectToNextPostTest($course, $lesson, $enrollment);
            if ($postTestRedirect) {
                return $postTestRedirect->with('quiz_passed', "Selamat, kamu lulus dengan skor {$score}. Modul selesai! Lanjut ke post-test.");
            }

            return $redirect;
        }

        return redirect()
            ->to(LaravelLocalization::getLocalizedURL(app()->getLocale(), route('player.show', ['course' => $course->slug, 'lesson' => $lesson->uuid])))
            ->with('quiz_failed', "Skor kamu {$score}. Nilai minimal untuk lulus adalah {$passingScore}. Silakan coba lagi.");
    }

    private function resolveCourse(string $courseSlug): Course
    {
        return Course::withoutGlobalScope('organization')
            ->where('slug', $courseSlug)
            ->where('status', 'published')
            ->with(['category', 'modules.lessons' => fn ($q) => $q->orderBy('sort')])
            ->firstOrFail();
    }

    private function ensureLessonBelongsToCourse(Course $course, Lesson $lesson): void
    {
        if (! $course->modules->flatMap->lessons->contains('id', $lesson->id)) {
            abort(404);
        }
    }

    private function neighborLesson(Course $course, Lesson $current, int $offset): ?Lesson
    {
        $ordered = $course->modules->flatMap->lessons->values();
        $index = $ordered->search(fn ($l) => $l->id === $current->id);
        $target = $index === false ? null : ($ordered[$index + $offset] ?? null);

        return $target instanceof Lesson ? $target : null;
    }

    public function showModuleTest(Request $request, string $courseSlug, CourseModule $module, string $type): View|RedirectResponse
    {
        $course = $this->resolveCourse($courseSlug);
        abort_unless($module->course_id === $course->id, 404);

        // Post-test: cek semua lesson modul sebelumnya selesai
        if ($type === 'post') {
            $sortedModules = $course->modules->sortBy('sort')->values();
            $moduleIndex = $sortedModules->search(fn ($m) => $m->id === $module->id);
            if ($moduleIndex !== false && $moduleIndex > 0) {
                $prevModule = $sortedModules[$moduleIndex - 1];
                if ($prevModule->type === 'content') {
                    $enrollment = $request->user()?->enrollments()->where('course_id', $course->id)->first();
                    if ($enrollment) {
                        $prevLessons = $prevModule->lessons;
                        $allComplete = $prevLessons->every(fn ($l) => $enrollment->lessonProgress()->where('lesson_id', $l->id)->whereNotNull('completed_at')->exists());
                        if (! $allComplete) {
                            return redirect()->to(LaravelLocalization::getLocalizedURL(app()->getLocale(), route('catalog.show', $course->slug)));
                        }
                    }
                }
            }
        }

        $quiz = $type === 'pre' ? $module->preTest : $module->postTest;
        abort_if(! $quiz || $quiz->questions->isEmpty(), 404);

        $enrollment = $request->user()?->enrollments()->where('course_id', $course->id)->first();
        $lastAttempt = ($enrollment && $quiz)
            ? $enrollment->quizAttempts()->where('quiz_id', $quiz->id)->latest()->first()
            : null;

        $passingScore = $quiz->passing_score ?? $course->passing_grade ?? 75;

        // Biarkan halaman menampilkan skor terakhir seperti pre-test

        return view('learning.module-test', compact('course', 'module', 'quiz', 'type', 'enrollment', 'lastAttempt', 'passingScore'));
    }

    public function submitModuleTest(Request $request, string $courseSlug, CourseModule $module, string $type): RedirectResponse
    {
        $course = $this->resolveCourse($courseSlug);
        abort_unless($module->course_id === $course->id, 404);

        $quiz = $type === 'pre' ? $module->preTest : $module->postTest;
        abort_if(! $quiz || $quiz->questions->isEmpty(), 404);

        $enrollment = $request->user()->enrollments()->where('course_id', $course->id)->firstOrFail();

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'integer', 'min:0'],
        ]);

        $answers = $validated['answers'];
        $correct = 0;

        foreach ($quiz->questions as $question) {
            $selected = $answers[$question->id] ?? null;
            if ($selected !== null && (int) $selected === $question->correct_index) {
                $correct++;
            }
        }

        $total = $quiz->questions->count();
        $score = $total > 0 ? (int) round($correct / $total * 100) : 0;
        $passingScore = $quiz->passing_score ?? $course->passing_grade ?? 75;
        $passed = $score >= $passingScore;

        QuizAttempt::create([
            'enrollment_id' => $enrollment->id,
            'quiz_id' => $quiz->id,
            'score' => $score,
            'passed' => $passed,
            'answers' => $answers,
            'finished_at' => now(),
        ]);

        if ($type === 'pre') {
            // Redirect ke materi pertama di modul berikutnya (atau modul ini jika ada lesson)
            $nextLesson = $module->lessons->sortBy('sort')->first();
            if ($nextLesson) {
                return redirect()->to(
                    LaravelLocalization::getLocalizedURL(app()->getLocale(), route('player.show', ['course' => $course->slug, 'lesson' => $nextLesson->uuid]))
                )->with($passed ? 'quiz_passed' : 'quiz_failed', "Pre-test: skor {$score}. ".($passed ? 'Lanjutkan ke materi.' : 'Nilai minimal {$passingScore}.'));
            }
            // Jika tidak ada lesson, cari modul berikutnya
            $sortedModules = $course->modules->sortBy('sort')->values();
            $idx = $sortedModules->search(fn ($m) => $m->id === $module->id);
            $nextModule = $idx !== false ? ($sortedModules[$idx + 1] ?? null) : null;
            if ($nextModule && $nextModule->lessons->isNotEmpty()) {
                $firstLesson = $nextModule->lessons->sortBy('sort')->first();

                return redirect()->to(
                    LaravelLocalization::getLocalizedURL(app()->getLocale(), route('player.show', ['course' => $course->slug, 'lesson' => $firstLesson->uuid]))
                )->with($passed ? 'quiz_passed' : 'quiz_failed', "Pre-test: skor {$score}. ".($passed ? 'Lanjutkan ke materi.' : 'Nilai minimal {$passingScore}.'));
            }

            return redirect()->to(
                LaravelLocalization::getLocalizedURL(app()->getLocale(), route('catalog.show', $course->slug))
            )->with($passed ? 'quiz_passed' : 'quiz_failed', "Pre-test: skor {$score}. ".($passed ? 'Lanjutkan ke materi.' : 'Nilai minimal {$passingScore}.'));
        }

        // Post-test: sync progress to check course completion & final score
        if ($passed) {
            app(CourseProgressService::class)->sync($enrollment->refresh());

            // Redirect ke modul berikutnya (atau course show jika sudah selesai)
            $sortedModules = $course->modules->sortBy('sort')->values();
            $idx = $sortedModules->search(fn ($m) => $m->id === $module->id);
            $nextModule = $idx !== false ? ($sortedModules[$idx + 1] ?? null) : null;
            if ($nextModule && $nextModule->lessons->isNotEmpty()) {
                $firstLesson = $nextModule->lessons->sortBy('sort')->first();

                return redirect()->to(
                    LaravelLocalization::getLocalizedURL(app()->getLocale(), route('player.show', ['course' => $course->slug, 'lesson' => $firstLesson->uuid]))
                )->with('quiz_passed', "Post-test: skor {$score}. Modul selesai! Lanjutkan ke modul berikutnya.");
            }
        }

        return redirect()->to(
            LaravelLocalization::getLocalizedURL(app()->getLocale(), route('catalog.show', $course->slug))
        )->with($passed ? 'quiz_passed' : 'quiz_failed', "Post-test: skor {$score}. ".($passed ? 'Modul selesai!' : 'Nilai minimal {$passingScore}. Coba lagi.'));
    }

    /**
     * Jika semua lesson di modul selesai, arahkan ke post-test modul berikutnya.
     * Mengembalikan redirect ke post-test bila perlu, null bila belum semua lesson selesai.
     */
    private function redirectToNextPostTest(Course $course, Lesson $lesson, Enrollment $enrollment): ?RedirectResponse
    {
        $module = $lesson->module;
        $moduleLessons = $module->lessons;
        $allComplete = $moduleLessons->every(fn ($l) => $enrollment->lessonProgress()->where('lesson_id', $l->id)->whereNotNull('completed_at')->exists());

        if (! $allComplete) {
            return null;
        }

        $sortedModules = $course->modules->sortBy('sort')->values();
        $idx = $sortedModules->search(fn ($m) => $m->id === $module->id);
        $nextModule = $idx !== false ? ($sortedModules[$idx + 1] ?? null) : null;

        if ($nextModule && $nextModule->type === 'post_test') {
            $quiz = $nextModule->postTest;
            if ($quiz && $quiz->questions->isNotEmpty()) {
                $passed = $enrollment->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists();
                if (! $passed) {
                    return redirect()->to(
                        LaravelLocalization::getLocalizedURL(app()->getLocale(), route('module.posttest', ['course' => $course->slug, 'module' => $nextModule->uuid]))
                    );
                }
            }
        }

        return null;
    }

    private function nextIncompleteLesson(Course $course, Lesson $current, Enrollment $enrollment): ?Lesson
    {
        $completed = $enrollment->lessonProgress()->whereNotNull('completed_at')->pluck('lesson_id');
        $ordered = $course->modules->flatMap->lessons->values();
        $index = $ordered->search(fn ($l) => $l->id === $current->id);

        return $ordered
            ->slice($index === false ? 0 : $index + 1)
            ->first(fn ($l) => ! $completed->contains($l->id));
    }
}
