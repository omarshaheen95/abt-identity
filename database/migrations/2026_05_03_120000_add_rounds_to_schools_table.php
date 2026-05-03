<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRoundsToSchoolsTable extends Migration
{
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->json('rounds')->nullable()->after('proctoring_settings');
        });

        DB::table('schools')->update([
            'rounds' => json_encode(['september' => false, 'february' => false, 'may' => true]),
        ]);
    }

    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('rounds');
        });
    }
}
