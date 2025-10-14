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
        Schema::table('consultations', function (Blueprint $table) {
            $table->string('motif')->nullable()->after('traitement');
            $table->float('poids')->nullable()->after('motif'); // en kg
            $table->float('taille')->nullable()->after('poids'); // en cm ou m selon ton choix
            $table->float('imc')->nullable()->after('taille');
            $table->float('temperature')->nullable()->after('imc'); // en °C
            $table->integer('frequence_cardiaque')->nullable()->after('temperature'); // en bpm
            $table->string('pression_arterielle')->nullable()->after('frequence_cardiaque'); // ex: "120/80"
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn([
                'motif',
                'poids',
                'taille',
                'imc',
                'temperature',
                'frequence_cardiaque',
                'pression_arterielle',
            ]);
        });
    }
};
