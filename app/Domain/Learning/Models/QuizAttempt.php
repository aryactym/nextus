<?php

declare(strict_types=1);

namespace App\Domain\Learning\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $enrollment_id
 * @property int $quiz_id
 * @property int $score
 * @property bool $passed
 * @property array<int|string, int>|null $answers
 * @property Carbon|null $finished_at
 */
class QuizAttempt extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'enrollment_id',
        'quiz_id',
        'score',
        'passed',
        'answers',
        'finished_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'passed' => 'boolean',
        'answers' => 'array',
        'finished_at' => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
