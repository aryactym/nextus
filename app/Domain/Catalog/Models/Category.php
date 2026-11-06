<?php

namespace App\Domain\Catalog\Models;

use App\Support\Traits\BelongsToOrganization;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use BelongsToOrganization, HasFactory, HasTranslations, HasUuid, SoftDeletes;

    public $translatable = ['name'];

    protected $fillable = [
        'uuid', 'name', 'slug', 'sort', 'organization_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
