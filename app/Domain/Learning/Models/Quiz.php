<?php

declare(strict_types=1);

namespace App\Domain\Learning\Models;

use App\Domain\Catalog\Models\CourseModule;
use App\Domain\Catalog\Models\Lesson;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $uuid
 * @property int|null $lesson_id
 * @property int|null $module_id
 * @property string $type
 * @property int|null $passing_score
 * @property int|null $max_attempts
 */
class Quiz extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'lesson_id',
        'module_id',
        'type',
        'passing_score',
        'max_attempts',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function scopePre($query)
    {
        return $query->where('type', 'pre');
    }

    public function scopePost($query)
    {
        return $query->where('type', 'post');
    }

    public function scopeLessonType($query)
    {
        return $query->where('type', 'lesson');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
