<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFillBlankAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fill_blank_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_term_id');
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fill_blank_question_id')->constrained()->cascadeOnDelete();
            $table->string('answer_fill_blank_question_uid')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('answer_fill_blank_question_uid')
                ->references('uid')
                ->on('fill_blank_questions')
                ->cascadeOnDelete();
            $table->foreign('student_term_id')->references('id')->on('student_terms')->cascadeOnDelete();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fill_blank_question_results');
    }
}
