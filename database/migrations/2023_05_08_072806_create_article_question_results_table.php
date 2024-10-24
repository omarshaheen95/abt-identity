<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleQuestionResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_question_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('student_term_id');
            $table->text('text_answer')->nullable();
            $table->string('answer_file_path')->nullable();
            $table->float('mark')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('question_id')->on('questions')->references('id')->cascadeOnDelete();
            $table->foreign('student_term_id')->on('student_terms')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_question_results');
    }
}
