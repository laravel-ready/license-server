<?php

namespace LaravelReady\LicenseServer\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicensableProduct extends Model
{
    public function __construct(array $attributes = [])
    {
        $prefix = Config::get('theme-store.default_table_prefix', 'ls');

        $this->table = "{$prefix}_licensable_products";

        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->user_id = auth()->id() ?? null;
        });
    }

    protected $table = 'ls__licensable_products';

    protected $fillable = [
        'licensable_id',
        'licensable_type',
        'license_id',
        'user_id',
    ];

    protected $casts = [];

    protected $dates = [
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function product()
    {
        return $this->morphTo(__FUNCTION__, 'licensable_type', 'licensable_id');
    }
}
