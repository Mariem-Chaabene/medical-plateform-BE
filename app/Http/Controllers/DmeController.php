<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Dme;
use App\Models\Patient;

class DmeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // 📌 Lister tous les DME (admin only)
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        $query = Dme::with('patient.user');

        if ($search = $request->query('q')) {
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('adresse', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        return response()->json($query->orderBy('id','desc')->paginate($perPage));
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'groupe_sanguin' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'notes_medicales' => 'nullable|string'
        ]);

        // Vérifier que le patient n'a pas déjà un DME
        $patient = Patient::findOrFail($request->patient_id);
        if ($patient->dme) {
            return response()->json([
                'message' => 'Ce patient a déjà un DME.'
            ], 422);
        }

        // Création dans une transaction pour plus de sécurité
        $dme = DB::transaction(function () use ($request) {
            return Dme::create([
                'patient_id' => $request->patient_id,
                'groupe_sanguin' => $request->groupe_sanguin,
                'notes_medicales' => $request->notes_medicales
            ]);
        });

        return response()->json($dme->load('patient'), 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // 📌 Voir le DME d’un patient spécifique
    public function show($id)
    {
        $dme = Dme::with([
            'patient.user',
            'antecedentsMedicaux',
            'consultations.examens.type',
            'analyses.typeAnalyse',
            'traitements',
            'historiques.user'
        ])->findOrFail($id);

        return response()->json($dme);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dme = Dme::findOrFail($id);

        $data = $request->validate([
            'groupe_sanguin' => 'sometimes|nullable|in:' . implode(',', Dme::GROUPES_SANGUINS),
            'notes_medicales' => 'sometimes|nullable|string'
        ]);

        return DB::transaction(function() use ($dme, $data) {
            $dme->update($data);
            return response()->json($dme->load('patient.user'));
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $dme = Dme::findOrFail($id);

        return DB::transaction(function() use ($dme) {
            $dme->delete();
            return response()->json(['message' => 'DME supprimé']);
        });
    }


    public function create()
    {
        return response()->json([
            'groupes_sanguins' => Dme::GROUPES_SANGUINS,
        ]);
    }
}
