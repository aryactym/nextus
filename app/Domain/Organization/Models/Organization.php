<?php

namespace App\Domain\Organization\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Organization extends Model
{
    use HasFactory, HasTranslations, HasUuid, SoftDeletes;

    public $translatable = ['name'];

    protected $fillable = [
        'uuid', 'name', 'slug', 'type', 'status', 'logo', 'brand',
        'default_locale', 'linkedin_org_id', 'custom_domain',
        'midtrans_config', 'settings',
    ];

    protected $casts = [
        'brand' => 'array',
        'midtrans_config' => 'array',
        'settings' => 'array',
        'deleted_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
