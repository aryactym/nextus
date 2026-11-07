<?php

declare(strict_types=1);

namespace App\Domain\Admin\Models;

use App\Models\User;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'slug', 'title', 'meta_description', 'content',
        'is_published', 'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
