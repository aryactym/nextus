<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Catalog\Models\Course;
use App\Domain\Organization\Models\Organization;
use App\Support\Traits\BelongsToOrganization;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use BelongsToOrganization, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid', 'organization_id', 'course_id', 'title', 'type', 'format',
        'location', 'starts_at', 'ends_at', 'quota', 'registration_url',
        'status', 'description',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /** Upcoming events that are not draft. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', '!=', 'draft');
    }

    /** Upcoming: starts_at in the future or today. */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>=', now()->startOfDay());
    }

    /** Past: starts_at before today. */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('starts_at', '<', now()->startOfDay());
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'webinar' => 'Webinar',
            'pelatihan' => 'Pelatihan',
            'batch' => 'Batch kursus',
            default => ucfirst($this->type),
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open' => 'Pendaftaran dibuka',
            'soon' => 'Segera dibuka',
            'full' => 'Penuh',
            'closed' => 'Selesai',
            'draft' => 'Draf',
            default => ucfirst($this->status),
        };
    }

    public function statusBadge(): string
    {
        return match ($this->status) {
            'open' => 'bg-green-lt',
            'soon' => 'bg-azure-lt',
            'full' => 'bg-orange-lt',
            'closed' => 'bg-gray-lt',
            default => 'bg-secondary-lt',
        };
    }
}
