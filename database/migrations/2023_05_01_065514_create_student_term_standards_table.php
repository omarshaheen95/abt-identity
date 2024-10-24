<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentTermStandardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_term_standards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_term_id');
            $table->unsignedBigInteger('question_standard_id');
            $table->float('mark');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('student_term_id')->on('student_terms')->references('id')->cascadeOnDelete();
            $table->foreign('question_standard_id')->on('question_standards')->references('id')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_term_standards');
    }
}
