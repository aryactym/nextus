<?php

namespace App\Domain\Catalog\Models;

use App\Domain\Learning\Models\Quiz;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Lesson extends Model
{
    use HasFactory, HasTranslations, HasUuid, SoftDeletes;

    public $translatable = ['title', 'content'];

    protected $fillable = [
        'uuid', 'module_id', 'title', 'type', 'content', 'video_url',
        'file_path', 'duration', 'is_preview', 'sort',
    ];

    protected $casts = [
        'content' => 'array',
        'is_preview' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }
}
