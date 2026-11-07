<?php

namespace App\Domain\Catalog\Models;

use App\Domain\Learning\Models\Enrollment;
use App\Domain\Organization\Models\Organization;
use App\Support\Traits\BelongsToOrganization;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Course extends Model
{
    use BelongsToOrganization, HasFactory, HasTranslations, HasUuid, SoftDeletes;

    public $translatable = ['title', 'summary', 'description'];

    protected $fillable = [
        'uuid', 'organization_id', 'category_id', 'ownership', 'visibility',
        'title', 'summary', 'description', 'slug', 'level', 'duration_minutes',
        'thumbnail', 'certificate_background', 'access_type', 'certificate_type', 'min_amount',
        'suggested_amounts', 'allow_custom_amount', 'currency', 'passing_grade',
        'status', 'published_at', 'created_by',
    ];

    protected $casts = [
        'suggested_amounts' => 'array',
        'deleted_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
