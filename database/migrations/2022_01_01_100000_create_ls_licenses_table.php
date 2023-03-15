<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLsLicensesTable extends Migration
{
    protected string $prefix;
    protected string $table;

    public function __construct()
    {
        $this->prefix = Config::get('license-server.default_table_prefix', 'ls');

        $this->table = "{$this->prefix}_licenses";
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

                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('cascade');

                $table->foreignId('created_by')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('cascade');

                $table->string('domain', 200)->nullable()->unique();
                $table->uuid('license_key')->unique();
                $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
                $table->dateTime('expiration_date')->nullable();
                $table->boolean('is_trial')->default(false);
                $table->boolean('is_lifetime')->default(false);

                $table->softDeletes();
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
