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
        Schema::create('marking_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->enum('status', ['Pending', 'Accepted', 'In Progress', 'Completed', 'Rejected']);
            $table->unsignedBigInteger('year_id');
            $table->text('grades')->nullable();
            $table->string('email')->nullable();
            $table->string('notes')->nullable();
            $table->enum('section', [0, 1, 2])->default(0)->comment('0: all, 1: Arabs, 2: Non-Arabs');
            $table->enum('round', ['september', 'february', 'may']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marking_requests');
    }
};
