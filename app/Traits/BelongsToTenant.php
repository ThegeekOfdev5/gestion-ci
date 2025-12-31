<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    /**
     * Boot le trait
     */
    protected static function bootBelongsToTenant(): void
    {
        // Auto-ajouter tenant_id lors de la création
        static::creating(function (Model $model) {
            if (!$model->tenant_id && tenancy()->initialized) {
                $model->tenant_id = tenant('id');
            }
        });

        // Auto-scope toutes les requêtes par tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (tenancy()->initialized) {
                $builder->where($builder->getQuery()->from . '.tenant_id', tenant('id'));
            }
        });
    }

    /**
     * Relation tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope pour un tenant spécifique
     */
    public function scopeForTenant(Builder $query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}
