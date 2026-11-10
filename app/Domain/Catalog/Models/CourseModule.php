<?php

namespace App\Domain\Catalog\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CourseModule extends Model
{
    use HasFactory, HasTranslations, HasUuid, SoftDeletes;

    public $translatable = ['title'];

    protected $fillable = [
        'uuid', 'course_id', 'title', 'sort',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'module_id')->orderBy('sort');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(\App\Domain\Learning\Models\Quiz::class, 'module_id');
    }

    public function preTest(): HasOne
    {
        return $this->hasOne(\App\Domain\Learning\Models\Quiz::class, 'module_id')->where('type', 'pre');
    }

    public function postTest(): HasOne
    {
        return $this->hasOne(\App\Domain\Learning\Models\Quiz::class, 'module_id')->where('type', 'post');
    }
}
