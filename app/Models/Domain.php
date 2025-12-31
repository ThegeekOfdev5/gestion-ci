<?php

namespace App\Models;

use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

class Domain extends BaseDomain
{
    protected $fillable = ['domain', 'tenant_id'];

    // // Validation pour s'assurer qu'on ne stocke que le sous-domaine
    // public function setDomainAttribute($value)
    // {
    //     $mainDomain = parse_url(config('app.url'), PHP_URL_HOST);

    //     // Si le domaine complet est fourni, extraire le sous-domaine
    //     if (Str::contains($value, $mainDomain)) {
    //         $value = str_replace('.' . $mainDomain, '', $value);
    //     }

    //     $this->attributes['domain'] = $value;
    // }
}
