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
        Schema::create('antecedents_medicaux', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dme_id')->constrained('dmes')->cascadeOnDelete();
        $table->string('nom_maladie');
        $table->date('date_diagnostic')->nullable();
        $table->text('remarques')->nullable();
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
        Schema::dropIfExists('antecedent_medicals');
    }
};
