<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->nullable()->constrained('terms')->onDelete('cascade');
            $table->foreignId('level_id')->nullable()->constrained('levels')->onDelete('cascade');
            $table->string('original_file_name');
            $table->string('file_name');

            $table->integer('created_rows_count')->default(0);
            $table->integer('updated_rows_count')->default(0);
            $table->integer('deleted_rows_count')->default(0);
            $table->integer('failed_rows_count')->default(0);
            $table->string('file_path');
            $table->enum('status', ['New', 'Uploading', 'Completed', 'Failed']);

            $table->enum('process_type', ['Create', 'Update', 'Delete']);
            $table->boolean('delete_with_rows')->default(0);
            $table->morphs('author');
            $table->text('error')->nullable();
            $table->json('failures')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_files');
    }
}
