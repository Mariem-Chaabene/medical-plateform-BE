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
        Schema::create('historique_dme', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dme_id')->constrained('dmes')->cascadeOnDelete();
        $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // qui a fait l'action
        $table->string('action'); // ex: 'create_traitement','update_dme', 'terminate_treatment'
        $table->json('old')->nullable();
        $table->json('new')->nullable();
        $table->timestamps();
        $table->index(['dme_id','action']);
    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historique_dmes');
    }
};
