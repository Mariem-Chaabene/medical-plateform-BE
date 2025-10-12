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
        Schema::create('examens', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dme_id')->constrained('dmes')->cascadeOnDelete();
        $table->foreignId('consultation_id')->nullable()->constrained('consultations')->cascadeOnDelete();
        $table->foreignId('type_examen_id')->constrained('type_examens')->restrictOnDelete();
        $table->dateTime('date_examen')->nullable();
        $table->enum('etat', ['en_attente','en_cours','termine'])->default('en_attente');
        $table->text('resultat')->nullable();
        $table->text('remarques')->nullable();
        $table->timestamps();
        $table->index(['consultation_id','type_examen_id']);
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('examens');
    }
};
