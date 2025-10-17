<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    
    // a utiliser dans le dme controller
    public function patientsWithoutDme()
    {
        // On charge aussi les infos de l'utilisateur pour l'affichage
        $patients = Patient::with('user')
            ->whereDoesntHave('dme') // filtre les patients qui n'ont pas encore de DME
            ->get();

        return response()->json($patients);
    }

   
   public function index(Request $request)
    {

        $perPage = (int) $request->query('per_page', 20);

        $query = Patient::with('user');

        if ($search = $request->query('q')) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('id','desc')->paginate($perPage));
    }

    /**
     * Voir un patient spécifique
     */
    public function show($id)
    {
        $patient = Patient::with([
            'user',
            'dme.antecedentsMedicaux',
            'dme.analyses.typeAnalyse',
            'dme.examens.type',
            'dme.traitements'
        ])->findOrFail($id);

        return response()->json($patient);
    }

}
