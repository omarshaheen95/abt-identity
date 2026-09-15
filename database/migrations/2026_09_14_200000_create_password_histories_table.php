<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePasswordHistoriesTable extends Migration
{
    private $tables = ['managers', 'schools', 'inspections'];

    public function up()
    {
        if (!Schema::hasTable('password_histories')) {
            Schema::create('password_histories', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->string('password');
                $table->timestamp('created_at')->nullable();

                $table->index(['model_type', 'model_id', 'created_at'], 'password_histories_owner_index');
            });
        }

        // Existing accounts have no password_changed_at, so an expiry policy would
        // lock every one of them out on the day it is switched on. Baseline them
        // at the deployment date instead: the countdown starts now, for everybody.
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'password_changed_at')) {
                DB::table($table)->whereNull('password_changed_at')->update(['password_changed_at' => now()]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('password_histories');
    }
}
