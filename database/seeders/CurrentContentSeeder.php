<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Course;
use App\Domain\Learning\Models\Quiz;
use App\Domain\Organization\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CurrentContentSeeder extends Seeder
{
    public function run(): void
    {
        $path = __DIR__.'/data/content.json';
        if (! file_exists($path)) {
            return;
        }

        $courses = json_decode(file_get_contents($path), true);
        if (! is_array($courses)) {
            return;
        }

        $organization = Organization::where('slug', 'yayasan-filantropi-islam')->first()
            ?? Organization::create(['name' => 'Yayasan Filantropi Islam', 'slug' => 'yayasan-filantropi-islam', 'status' => 'active']);

        foreach ($courses as $courseData) {
            $category = null;
            if (! empty($courseData['category_slug'])) {
                $category = Category::firstOrCreate(
                    ['slug' => $courseData['category_slug'], 'organization_id' => $organization->id],
                    ['name' => ['id' => $courseData['category_slug']], 'sort' => 1]
                );
            }

            $course = Course::withoutGlobalScopes()->firstOrCreate(
                ['slug' => $courseData['slug'], 'organization_id' => $organization->id],
                [
                    'category_id' => $category?->id,
                    'title' => $courseData['title'],
                    'summary' => $courseData['summary'],
                    'description' => $courseData['description'],
                    'level' => $courseData['level'],
                    'duration_minutes' => $courseData['duration_minutes'],
                    'access_type' => $courseData['access_type'],
                    'certificate_type' => $courseData['certificate_type'],
                    'passing_grade' => $courseData['passing_grade'] ?? 75,
                    'status' => $courseData['status'] ?? 'published',
                    'visibility' => $courseData['visibility'] ?? 'public',
                    'is_featured' => $courseData['is_featured'] ?? false,
                    'featured_sort' => $courseData['featured_sort'] ?? 0,
                    'published_at' => now(),
                ]
            );

            if (! $course->thumbnail) {
                $source = public_path('images/course-'.(abs(crc32($course->slug)) % 3 + 1).'.webp');
                if (file_exists($source)) {
                    $path = 'courses/thumbnails/'.$course->slug.'.webp';
                    Storage::disk('uploads')->put($path, file_get_contents($source));
                    $course->update(['thumbnail' => $path]);
                }
            }

            if ($course->modules()->exists()) {
                continue;
            }

            foreach ($courseData['modules'] as $moduleData) {
                $module = $course->modules()->create([
                    'title' => $moduleData['title'],
                    'type' => $moduleData['type'] ?? 'content',
                    'sort' => $moduleData['sort'] ?? 1,
                ]);

                foreach ($moduleData['quizzes'] ?? [] as $quizData) {
                    $quiz = Quiz::firstOrCreate([
                        'module_id' => $module->id,
                        'type' => $quizData['type'],
                    ]);
                    $quiz->update([
                        'passing_score' => $quizData['passing_score'],
                        'max_attempts' => $quizData['max_attempts'] ?? 1,
                    ]);

                    foreach ($quizData['questions'] ?? [] as $q) {
                        $quiz->questions()->firstOrCreate(
                            ['sort' => $q['sort']],
                            [
                                'question' => $q['question'],
                                'options' => $q['options'],
                                'correct_index' => $q['correct_index'],
                            ]
                        );
                    }
                }

                foreach ($moduleData['lessons'] as $lessonData) {
                    $lesson = $module->lessons()->create([
                        'title' => $lessonData['title'],
                        'type' => $lessonData['type'],
                        'content' => $lessonData['content'],
                        'duration' => $lessonData['duration'] ?? 0,
                        'is_preview' => $lessonData['is_preview'] ?? false,
                        'video_url' => $lessonData['video_url'],
                        'file_path' => $lessonData['file_path'],
                        'sort' => $lessonData['sort'] ?? 1,
                    ]);

                    if ($lesson->type === 'quiz' && ! empty($lessonData['quiz_questions'])) {
                        $quiz = Quiz::firstOrCreate(['lesson_id' => $lesson->id]);
                        foreach ($lessonData['quiz_questions'] as $q) {
                            $quiz->questions()->firstOrCreate(
                                ['sort' => $q['sort']],
                                [
                                    'question' => $q['question'],
                                    'options' => $q['options'],
                                    'correct_index' => $q['correct_index'],
                                ]
                            );
                        }
                    }
                }
            }
        }
    }
}
