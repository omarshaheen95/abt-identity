<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proctor_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_term_id');
            $table->enum('type', ['selfie', 'screenshot']);
            $table->string('file_path');
            $table->unsignedInteger('capture_minute')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_term_id']);
            $table->foreign('student_term_id')->references('id')->on('student_terms')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proctor_images');
    }
};
