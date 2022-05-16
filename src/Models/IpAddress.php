<?php

namespace LaravelReady\LicenseServer\Models;

use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IpAddress extends Model
{
    public function __construct(array $attributes = [])
    {
        $prefix = Config::get('theme-store.default_table_prefix', 'ls');

        $this->table = "{$prefix}_ip_addresses";

        parent::__construct($attributes);
    }

    protected $table = 'ls_ip_addresses';

    protected $fillable = [
        'license_id',
        'ip_address',
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
}
