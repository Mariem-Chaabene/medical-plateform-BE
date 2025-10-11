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
        Schema::create('traitements_chroniques', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dme_id')->constrained('dmes')->cascadeOnDelete();
        $table->string('nom_medicament');
        $table->string('dosage')->nullable();       
        $table->string('frequence')->nullable();    
        $table->date('date_debut')->nullable();
        $table->date('date_fin')->nullable();
        $table->boolean('is_active')->default(true);
        $table->text('remarques')->nullable();
        $table->timestamps();
        $table->index(['dme_id','is_active']);
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('traitement_chroniques');
    }
};
