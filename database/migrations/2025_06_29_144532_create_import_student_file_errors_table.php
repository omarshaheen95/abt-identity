<?php
/*
Dev Omar Shaheen
Devomar095@gmail.com
WhatsApp +972592554320
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('import_student_file_errors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('student_import_file_id')->constrained()->cascadeOnDelete();
            $table->integer('row_num')->nullable();
            $table->text('data')->nullable();
            $table->boolean('updated')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_student_file_errors');
    }
};
