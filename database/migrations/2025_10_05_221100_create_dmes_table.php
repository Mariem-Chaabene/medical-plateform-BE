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

        Schema::create('dmes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->unique()->constrained('patients')->cascadeOnDelete();
        $table->enum('groupe_sanguin', ['A+','A-','B+','B-','AB+','AB-','O+','O-'])->nullable();
        $table->text('notes_medicales')->nullable();
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
        Schema::dropIfExists('dmes');
    }
};
