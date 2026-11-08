<?php

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'website_url',
        'description',
        'sort',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
