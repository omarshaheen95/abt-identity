<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddProctoringSettingsToSchoolsTable extends Migration
{
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->json('proctoring_settings')->nullable()->after('logo');
        });
        DB::table('schools')->update([
            'proctoring_settings' => json_encode(['desktop_only' => false, 'screenshot' => false, 'selfie' => false]),
        ]);
        DB::table('settings')->whereIn('key', ['exam_proctoring_enabled', 'exam_desktop_only'])->delete();
    }

    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('proctoring_settings');
        });
    }
}
