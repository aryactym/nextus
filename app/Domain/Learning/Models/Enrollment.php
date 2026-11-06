<?php

namespace App\Domain\Learning\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid', 'user_id', 'course_id', 'order_id', 'status',
        'progress_percent', 'enrolled_at', 'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
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

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Payment\Models\Order::class);
    }

    public function lessonProgress(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }
}
