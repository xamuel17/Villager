<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('userdetails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('userID')->nullable();
            $table->string('gender')->nullable();
            $table->longText('bio')->nullable();
            $table->string('martialStatus')->nullable();
            $table->longText('lowerQualification')->nullable();
            $table->longText('higherQualification1')->nullable();
            $table->longText('higherQualification2')->nullable();
            $table->longText('skills')->nullable();
            $table->longText('work')->nullable();
            $table->integer('followers')->default(0);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('userdetails');
    }
}
