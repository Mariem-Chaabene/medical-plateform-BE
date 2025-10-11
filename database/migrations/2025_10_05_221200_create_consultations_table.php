<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultations', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('dme_id');
        $table->unsignedBigInteger('medecin_id');
        $table->dateTime('date_consultation');
        $table->text('diagnostic')->nullable();
        $table->text('traitement')->nullable();
        $table->timestamps();
        $table->foreign('dme_id')->references('id')->on('dmes')->onDelete('cascade');
        $table->foreign('medecin_id')->references('id')->on('users')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultations');
    }
};
