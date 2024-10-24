<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('id_number')->nullable();
            $table->string('name');
            $table->string('email');
            $table->string('nationality')->nullable();
            $table->string('password')->nullable();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('year_id');
            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
            $table->string('grade_name')->nullable();

            $table->string('dob')->nullable();
            $table->enum('gender', ['boy', 'girl']);
            $table->boolean('sen')->default(0);
            $table->boolean('g_t')->default(0);
            $table->boolean('arab')->default(0);
            $table->boolean('citizen')->default(0);
            $table->string('lang')->default('ar');
            $table->dateTime('last_login')->nullable();
            $table->text('last_login_info')->nullable();
            $table->boolean('demo');
            $table->json('demo_data');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
            $table->foreign('year_id')->references('id')->on('years')->cascadeOnDelete();
            $table->foreign('level_id')->references('id')->on('levels')->cascadeOnDelete();
            $table->foreign('file_id')->references('id')->on('student_import_files')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('students');
    }
}
