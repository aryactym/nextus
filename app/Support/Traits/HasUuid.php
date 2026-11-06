<?php

namespace App\Support\Traits;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function scopeByUuid(Builder $query, string $uuid): Builder
    {
        return $query->where('uuid', $uuid);
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->where('uuid', $value)->first() ?? abort(404);
    }
}
