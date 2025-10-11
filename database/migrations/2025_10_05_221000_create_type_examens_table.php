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
        Schema::create('type_examens', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();   // ex: BIO, RAD, SCAN, ECG
        $table->string('libelle');          // ex: "Analyse biologique", "Radiographie"
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
        Schema::dropIfExists('type_examens');
    }
};
