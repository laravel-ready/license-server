<?php

namespace LaravelReady\LicenseServer\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;

use LaravelReady\LicenseServer\Models\LicensableProduct;

trait Licensable
{
    /**
     * Licensable product relationship
     */
    public function licensable(): MorphOne
    {
        return $this->morphOne(LicensableProduct::class, 'licensable')
            ->select('id', 'licensable_id', 'licensable_type', 'license_id', 'user_id')
            ->with([
                'license' => fn ($query) => $query->first(),
            ]);
    }

    /**
     * Licensable product item relationship
     */
    public function licensed(): HasOne
    {
        return $this->hasOne(LicensableProduct::class);
    }

    /**
     * Licensed to product relationship
     */
    public function licensedTo(): HasOne
    {
        return $this->hasOne(LicensableProduct::class)->with([
            'product'
        ]);
    }
}
