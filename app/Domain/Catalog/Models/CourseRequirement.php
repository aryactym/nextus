<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseRequirement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id', 'prerequisite_course_id', 'prerequisite_category_id', 'min_grade', 'pendidikan_id', 'min_age', 'max_age', 'notes',
    ];

    protected $casts = [
        'min_grade' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'prerequisite_category_id' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function prerequisiteCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'prerequisite_course_id');
    }

    public function prerequisiteCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'prerequisite_category_id');
    }

    public function pendidikan(): BelongsTo
    {
        return $this->belongsTo(Pendidikan::class);
    }

    /**
     * Check if user has completed all courses in the prerequisite category.
     * Returns true if requirement is satisfied, false if not, null if no category requirement.
     */
    public function checkUserCompletedPrerequisiteCategory(User $user): ?bool
    {
        if ($this->prerequisite_category_id === null) {
            return null; // No category prerequisite set
        }

        $prereqCategory = $this->prerequisiteCategory;
        if ($prereqCategory === null) {
            return null;
        }

        // Get all courses in the prerequisite category
        $coursesInCategory = $prereqCategory->courses()->get()->keyBy('id');
        $completedCourseIds = $user?->enrollments()
            ->where('status', 'completed')
            ->pluck('course_id')
            ->toArray();

        // User must have completed ALL courses in this category
        foreach ($coursesInCategory as $course) {
            if (! in_array($course->id, $completedCourseIds)) {
                return false; // Missing course
            }
        }

        return true; // All courses completed
    }
}
