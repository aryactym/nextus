<?php

namespace App\Console\Commands;

use App\Domain\Catalog\Models\Course;
use App\Domain\Learning\Models\Quiz;
use Illuminate\Console\Command;

class ExportContentSeeder extends Command
{
    protected $signature = 'content:export-seed';

    protected $description = 'Export current course content to database/seeders/data/content.json';

    public function handle(): int
    {
        $data = [];

        foreach (Course::withoutGlobalScopes()->with(['modules.lessons', 'modules.quizzes.questions'])->get() as $course) {
            $modules = [];
            foreach ($course->modules->sortBy('sort') as $module) {
                $lessons = [];
                foreach ($module->lessons->sortBy('sort') as $lesson) {
                    $quizQuestions = [];
                    if ($lesson->type === 'quiz') {
                        $lessonQuiz = Quiz::where('lesson_id', $lesson->id)->first();
                        if ($lessonQuiz) {
                            $quizQuestions = $lessonQuiz->questions->map(fn ($q) => [
                                'question' => $q->question,
                                'options' => $q->options,
                                'correct_index' => $q->correct_index,
                                'sort' => $q->sort,
                            ])->values()->all();
                        }
                    }

                    $lessons[] = [
                        'title' => $lesson->getTranslations('title'),
                        'type' => $lesson->type,
                        'content' => $lesson->getTranslations('content'),
                        'duration' => $lesson->duration,
                        'is_preview' => (bool) $lesson->is_preview,
                        'video_url' => $lesson->video_url,
                        'file_path' => $lesson->file_path,
                        'sort' => $lesson->sort,
                        'quiz_questions' => $quizQuestions,
                    ];
                }

                $quizzes = [];
                foreach ($module->quizzes as $quiz) {
                    $quizzes[] = [
                        'type' => $quiz->type,
                        'passing_score' => $quiz->passing_score,
                        'max_attempts' => $quiz->max_attempts,
                        'questions' => $quiz->questions->map(fn ($q) => [
                            'question' => $q->question,
                            'options' => $q->options,
                            'correct_index' => $q->correct_index,
                            'sort' => $q->sort,
                        ])->values()->all(),
                    ];
                }

                $modules[] = [
                    'title' => $module->getTranslations('title'),
                    'type' => $module->type,
                    'sort' => $module->sort,
                    'lessons' => $lessons,
                    'quizzes' => $quizzes,
                ];
            }

            $data[] = [
                'title' => $course->getTranslations('title'),
                'summary' => $course->getTranslations('summary'),
                'description' => $course->getTranslations('description'),
                'slug' => $course->slug,
                'level' => $course->level,
                'duration_minutes' => $course->duration_minutes,
                'access_type' => $course->access_type,
                'certificate_type' => $course->certificate_type,
                'passing_grade' => $course->passing_grade,
                'status' => $course->status,
                'visibility' => $course->visibility,
                'is_featured' => (bool) $course->is_featured,
                'featured_sort' => $course->featured_sort,
                'organization_id' => $course->organization_id,
                'category_slug' => optional($course->category)->slug,
                'modules' => $modules,
            ];
        }

        $path = base_path('database/seeders/data/content.json');
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info('Exported '.count($data).' courses to '.$path);

        return self::SUCCESS;
    }
}
