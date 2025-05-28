<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE settings MODIFY COLUMN type ENUM('text', 'radio', 'checkbox', 'color', 'file', 'multi_select', 'select', 'number', 'password', 'textarea', 'date')");

        Schema::table('settings', function (Blueprint $table) {
            $table->string('group')->after('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE settings MODIFY COLUMN type ENUM('text', 'textarea', 'checkbox', 'password', 'file', 'color')");
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['group']);
        });
    }
}
