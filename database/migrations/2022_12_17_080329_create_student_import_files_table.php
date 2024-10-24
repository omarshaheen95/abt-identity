<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentImportFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_import_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('year_id');
            $table->string('original_file_name');
            $table->string('file_name');
            $table->integer('row_count')->default(0);
            $table->integer('failed_row_count')->default(0);
            $table->string('path');
            $table->boolean('status')->default(1)->comment('1=>New , 2=>Uploading 3=>Completed 4=>Failures ,5=>Errors');
            $table->boolean('delete_with_user')->default(0);
            $table->text('error')->nullable();
            $table->text('failures')->nullable();
            $table->integer('updated_row_count')->default(0);
            $table->boolean('update')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('school_id')->on('schools')->references('id')->cascadeOnDelete();
            $table->foreign('year_id')->on('years')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_import_files');
    }
}
