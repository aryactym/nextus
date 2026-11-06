<?php

namespace App\Domain\Payment\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentEvent extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'order_id', 'payload', 'signature_valid', 'received_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'signature_valid' => 'boolean',
        'received_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
