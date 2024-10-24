<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentImportFileLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_import_file_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_import_file_id');
            $table->integer('row_num')->nullable();
            $table->text('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('student_import_file_id')->references('id')->on('student_import_files')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_import_file_logs');
    }
}
