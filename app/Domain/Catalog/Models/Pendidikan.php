<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendidikan extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'level'];

    protected $casts = [
        'level' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'pendidikan_id');
    }

    public function courseRequirements(): HasMany
    {
        return $this->hasMany(CourseRequirement::class, 'pendidikan_id');
    }
}
