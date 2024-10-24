<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionStandardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_standards', function (Blueprint $table) {
            $table->id();
            $table->text('standard');
            $table->unsignedBigInteger('question_id');
            $table->integer('mark');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('question_id')->on('questions')->references('id')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_standards');
    }
}
