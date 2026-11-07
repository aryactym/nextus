<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $badge
 * @property string|null $tier
 */
class UserBadge extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'badge',
        'tier',
        'awarded_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
