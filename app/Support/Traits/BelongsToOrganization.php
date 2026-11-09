<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToOrganization
{
    public static function bootBelongsToOrganization(): void
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            if (filled(organization_id())) {
                $builder->where('organization_id', organization_id());
            }
        });

        static::creating(function (Model $model) {
            if (empty($model->organization_id) && filled(organization_id())) {
                $model->organization_id = organization_id();
            }
        });
    }

    public function scopeForOrganization(Builder $query, ?int $organizationId = null): Builder
    {
        if (! filled($organizationId)) {
            $organizationId = organization_id();
        }

        return $query->where('organization_id', $organizationId);
    }
}
