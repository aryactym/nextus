<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Http\Controllers;

use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Domain\Learning\Models\Quiz;
use App\Domain\Learning\Models\QuizQuestion;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function storeQuestion(Request $request, Lesson $lesson): RedirectResponse
    {
        abort_unless($request->user()->can('manage courses'), 403);

        $lesson = Lesson::query()
            ->whereHas('module.course', fn (Builder $query) => $query->forOrganization())
            ->findOrFail($lesson->getKey());

        abort_unless($lesson->type === 'quiz', 422);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'options' => ['required', 'array', 'min:2', 'max:6'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'min:0', 'max:5'],
        ]);

        $options = array_values($validated['options']);

        if ((int) $validated['correct_index'] >= count($options)) {
            return back()
                ->withErrors(['correct_index' => 'Jawaban benar harus salah satu pilihan yang tersedia.'])
                ->withInput();
        }

        $quiz = Quiz::firstOrCreate(['lesson_id' => $lesson->id]);
        $quiz->questions()->create([
            'question' => $validated['question'],
            'options' => $options,
            'correct_index' => (int) $validated['correct_index'],
            'sort' => ($quiz->questions()->max('sort') ?? 0) + 1,
        ]);

        return redirect()
            ->route('admin.courses.edit', $lesson->module->course)
            ->with('success', 'Soal kuis berhasil ditambahkan.');
    }

    public function storeModuleQuestion(Request $request, CourseModule $module, string $type): RedirectResponse
    {
        abort_unless($request->user()->can('manage courses'), 403);

        $module = CourseModule::query()
            ->whereHas('course', fn (Builder $query) => $query->forOrganization())
            ->findOrFail($module->getKey());

        abort_unless(in_array($type, ['pre', 'post'], true), 422);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'options' => ['required', 'array', 'min:2', 'max:6'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'min:0', 'max:5'],
        ]);

        $options = array_values($validated['options']);

        if ((int) $validated['correct_index'] >= count($options)) {
            return back()
                ->withErrors(['correct_index' => 'Jawaban benar harus salah satu pilihan yang tersedia.'])
                ->withInput();
        }

        $quiz = Quiz::firstOrCreate([
            'module_id' => $module->id,
            'type' => $type,
        ]);
        $quiz->questions()->create([
            'question' => $validated['question'],
            'options' => $options,
            'correct_index' => (int) $validated['correct_index'],
            'sort' => ($quiz->questions()->max('sort') ?? 0) + 1,
        ]);

        return redirect()
            ->route('admin.courses.edit', $module->course)
            ->with('success', 'Soal '.($type === 'pre' ? 'pre-test' : 'post-test').' berhasil ditambahkan.');
    }

    public function updateModuleSettings(Request $request, CourseModule $module, string $type): RedirectResponse
    {
        abort_unless($request->user()->can('manage courses'), 403);

        $module = CourseModule::query()
            ->whereHas('course', fn (Builder $query) => $query->forOrganization())
            ->findOrFail($module->getKey());

        abort_unless(in_array($type, ['pre', 'post'], true), 422);

        $validated = $request->validate([
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $quiz = Quiz::firstOrCreate([
            'module_id' => $module->id,
            'type' => $type,
        ]);

        $quiz->update([
            'max_attempts' => $validated['max_attempts'] ?? 1,
        ]);

        return redirect()
            ->route('admin.courses.edit', $module->course)
            ->with('success', 'Pengaturan '.($type === 'pre' ? 'pre-test' : 'post-test').' berhasil diperbarui.');
    }

    public function updateQuestion(Request $request, QuizQuestion $question): RedirectResponse
    {
        abort_unless($request->user()->can('manage courses'), 403);

        $question->load('quiz');

        if ($question->quiz->lesson_id) {
            $question->quiz->load('lesson.module.course');
            $course = $question->quiz->lesson->module->course;
        } else {
            $question->quiz->load('module.course');
            $course = $question->quiz->module->course;
        }

        abort_unless($course->organization_id === organization_id(), 404);

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'options' => ['required', 'array', 'min:2', 'max:6'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'min:0', 'max:5'],
        ]);

        $options = [];
        $indexMap = [];

        foreach ($validated['options'] as $index => $text) {
            $text = trim((string) $text);

            if ($text === '') {
                continue;
            }

            $indexMap[(int) $index] = count($options);
            $options[] = $text;
        }

        if (count($options) < 2) {
            return back()
                ->withErrors(['options' => 'Isi minimal dua pilihan.'])
                ->withInput();
        }

        $selected = (int) $validated['correct_index'];

        if (! array_key_exists($selected, $indexMap)) {
            return back()
                ->withErrors(['correct_index' => 'Jawaban benar harus salah satu pilihan yang terisi.'])
                ->withInput();
        }

        $question->update([
            'question' => $validated['question'],
            'options' => $options,
            'correct_index' => $indexMap[$selected],
        ]);

        return redirect()
            ->route('admin.courses.edit', $course)
            ->with('success', 'Soal kuis berhasil diperbarui.');
    }

    public function destroyQuestion(QuizQuestion $question): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage courses'), 403);

        $question->load('quiz');

        if ($question->quiz->lesson_id) {
            $question->quiz->load('lesson.module.course');
            $course = $question->quiz->lesson->module->course;
        } else {
            $question->quiz->load('module.course');
            $course = $question->quiz->module->course;
        }

        abort_unless($course->organization_id === organization_id(), 404);

        $question->delete();

        return redirect()
            ->route('admin.courses.edit', $course)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
