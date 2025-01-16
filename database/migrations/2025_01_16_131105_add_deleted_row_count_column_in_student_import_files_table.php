<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedRowCountColumnInStudentImportFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_import_files', function (Blueprint $table) {
            $table->integer('deleted_row_count')->default(0)->after('updated_row_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_import_file', function (Blueprint $table) {
            $table->dropColumn('deleted_row_count');
        });
    }
}
