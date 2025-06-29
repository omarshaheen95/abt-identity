<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('student_import_files', function (Blueprint $table) {
            $table->enum('process_type', \App\Helpers\Constant::UPLOAD_TYPE)->nullable()->after('path');
            $table->text('data')->nullable()->after('failures');
            $table->boolean('with_abt_id')->default(false)->after('deleted_row_count');
        });
    }

    public function down(): void
    {
        Schema::table('student_import_files', function (Blueprint $table) {
            $table->dropColumn(['process_type', 'data', 'with_abt_id']);
        });
    }
};
