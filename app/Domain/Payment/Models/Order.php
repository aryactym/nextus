<?php

namespace App\Domain\Payment\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid', 'midtrans_order_id', 'user_id', 'course_id', 'order_type',
        'nature', 'amount', 'currency', 'status', 'snap_token', 'payment_type',
        'paid_at', 'expired_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Catalog\Models\Course::class);
    }

    public function events(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaymentEvent::class);
    }
}
