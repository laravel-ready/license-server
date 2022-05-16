<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLsLicensableProductsTable extends Migration
{
    public function __construct()
    {
        $this->prefix = Config::get('license-server.default_table_prefix', 'ls');

        $this->table = "{$this->prefix}_licensable_products";
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

                $table->morphs('licensable');

                $table->foreignId('license_id')
                    ->nullable()
                    ->constrained("{$this->prefix}_licenses")
                    ->onDelete('cascade');

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained("users")
                    ->onDelete('cascade');

                $table->index(['licensable_id', 'licensable_type']);

                $table->unique(
                    ['license_id', 'licensable_id', 'licensable_type'],
                    'ls_licensable_products_licensable_type_licensable_id_index'
                );

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
