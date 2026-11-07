<?php

declare(strict_types=1);

namespace App\Domain\Learning\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $uuid
 * @property int $quiz_id
 * @property string $question
 * @property array<int, string> $options
 * @property int $correct_index
 * @property int $sort
 */
class QuizQuestion extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'quiz_id',
        'question',
        'options',
        'correct_index',
        'sort',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_index' => 'integer',
        'sort' => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
