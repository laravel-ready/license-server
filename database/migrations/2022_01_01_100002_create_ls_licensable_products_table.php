<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLsLicensableProductsTable extends Migration
{
    protected string $prefix;
    protected string $table;
    protected string $uniqueIndexName;

    public function __construct()
    {
        $this->prefix = Config::get('license-server.default_table_prefix', 'ls');

        $this->table = "{$this->prefix}_licensable_products";

        $this->uniqueIndexName = "{$this->prefix}_licensable_products_id_type_id_index";
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

                $table->timestamps();
            });
        }

        if (Schema::hasTable($this->table)) {
            // add unique index for 'license_id', 'licensable_type', 'licensable_id'
            Schema::table($this->table, function (Blueprint $table) {
                $isUniqueIndexExists = collect(DB::select("SHOW INDEXES FROM {$this->table}"))
                    ->pluck('Key_name')
                    ->contains($this->uniqueIndexName);

                if (!$isUniqueIndexExists) {
                    $table->unique(
                        ['license_id', 'licensable_type', 'licensable_id'],
                        $this->uniqueIndexName
                    );
                }
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
                $table->dropConstrainedForeignId('license_id');

                $isUniqueIndexExists = collect(DB::select("SHOW INDEXES FROM {$this->table}"))
                    ->pluck('Key_name')
                    ->contains($this->uniqueIndexName);

                if ($isUniqueIndexExists) {
                    $table->dropUnique($this->uniqueIndexName);
                }

                $table->dropIfExists();
            });
        }
    }
}
