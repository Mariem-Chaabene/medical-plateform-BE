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
        Schema::create('analyses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dme_id')->constrained('dmes')->cascadeOnDelete();
        $table->foreignId('consultation_id')->nullable()->constrained('consultations')->cascadeOnDelete();
        $table->foreignId('type_analyse_id')->constrained('type_analyses')->cascadeOnDelete(); // ✅ directement ici
        $table->dateTime('date_analyse')->nullable();
        $table->text('resultat')->nullable();
        $table->text('remarques')->nullable();
        $table->timestamps();
        $table->index(['dme_id','type_analyse_id']);
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('analyses');
    }
};
