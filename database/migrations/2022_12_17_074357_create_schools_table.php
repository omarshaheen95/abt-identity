<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name');
            $table->string('email');
            $table->string('password');
            $table->string('logo')->nullable();
            $table->string('url')->nullable();
            $table->string('mobile')->nullable();
            $table->string('country')->nullable()->default('uae');
            $table->string('curriculum_type')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->text('last_login_info')->nullable();
            $table->string('lang')->default('ar');
            $table->boolean('active')->default(0);
            $table->boolean('student_login');
            $table->unsignedBigInteger('available_year_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('available_year_id')->on('years')->references('id')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('schools');
    }
}
