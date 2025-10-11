<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeAnalyse;

class TypeAnalysesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeAnalyse::firstOrCreate(['libelle' => 'Prise de sang']);
        TypeAnalyse::firstOrCreate(['libelle' => 'Analyse d’urine']);
        TypeAnalyse::firstOrCreate(['libelle' => 'Analyse biochimique']);
        TypeAnalyse::firstOrCreate(['libelle' => 'Analyse hormonale']);
        TypeAnalyse::firstOrCreate(['libelle' => 'Autre type']);
    }
}
