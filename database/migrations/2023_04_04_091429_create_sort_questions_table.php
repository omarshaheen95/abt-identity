<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSortQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sort_questions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->index();
            $table->unsignedBigInteger('question_id');
            $table->string('content')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('ordered');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('question_id')->references('id')->on('questions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sort_questions');
    }
}
