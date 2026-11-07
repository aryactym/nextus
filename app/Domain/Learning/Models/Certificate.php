<?php

declare(strict_types=1);

namespace App\Domain\Learning\Models;

use App\Domain\Catalog\Models\Course;
use App\Models\User;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property int $enrollment_id
 * @property int $user_id
 * @property int $course_id
 * @property string $code
 * @property Carbon $issued_at
 * @property Carbon|null $revoked_at
 */
class Certificate extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid',
        'enrollment_id',
        'user_id',
        'course_id',
        'code',
        'issued_at',
        'revoked_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }
}
