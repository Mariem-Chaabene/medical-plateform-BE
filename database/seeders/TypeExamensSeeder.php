<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TypeExamen;

class TypeExamensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeExamen::firstOrCreate(['code'=>'BIO'], ['libelle'=>'Analyse biologique']);
        TypeExamen::firstOrCreate(['code'=>'RAD'], ['libelle'=>'Radiographie']);
        TypeExamen::firstOrCreate(['code'=>'SCAN'], ['libelle'=>'Scanner / CT']);
        TypeExamen::firstOrCreate(['code'=>'IRM'], ['libelle'=>'IRM']);
        TypeExamen::firstOrCreate(['code'=>'ECG'], ['libelle'=>'ECG']);
    }
}
