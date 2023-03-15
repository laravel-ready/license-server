<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLsIpAddressesTable extends Migration
{
    protected string $prefix;
    protected string $table;

    public function __construct()
    {
        $this->prefix = Config::get('license-server.default_table_prefix', 'ls');

        $this->table = "{$this->prefix}_ip_addresses";
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->foreignId('license_id')
                    ->constrained("{$this->prefix}_licenses")
                    ->onDelete('cascade');

                $table->ipAddress('ip_address');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable($this->table)) {
            Schema::table($this->table, function (Blueprint $table) {
                $table->dropIfExists();
            });
        }
    }
}
