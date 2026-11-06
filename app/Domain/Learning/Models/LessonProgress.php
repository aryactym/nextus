<?php

namespace App\Domain\Learning\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonProgress extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'enrollment_id', 'lesson_id', 'completed_at', 'last_position',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Catalog\Models\Lesson::class);
    }
}
