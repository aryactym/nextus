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
        'course_id', 'prerequisite_course_id', 'min_grade', 'pendidikan_id', 'min_age', 'max_age', 'notes',
    ];

    protected $casts = [
        'min_grade' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
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

    public function pendidikan(): BelongsTo
    {
        return $this->belongsTo(Pendidikan::class);
    }
}
