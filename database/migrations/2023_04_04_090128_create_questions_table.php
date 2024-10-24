<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('term_id');
            $table->enum('type',['true_false','multiple_choice','matching','sorting','article','fill_blank']);
            $table->foreignId('subject_id')->constrained('subjects');
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->string('audio')->nullable();
            $table->string('question_reader')->nullable();
            $table->float('mark');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('term_id')->on('terms')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
