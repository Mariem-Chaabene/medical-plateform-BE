<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\TypeExamen;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        return response()->json(Consultation::with('dme')->get());
    }



    // 📌 Historique d’un patient (pour dossier)
    public function historiquePatient($patientId)
    {
        $consultations = Consultation::with(['medecin.user'])
            ->where('patient_id', $patientId)
            ->orderBy('date_consultation', 'desc')
            ->get();

        return response()->json($consultations);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
        // 📌 Ajouter une nouvelle consultation
    public function store(Request $request)
    {
        $request->validate([
            'dme_id' => 'required|integer|exists:dmes,id',
            'medecin_id' => 'required|integer|exists:medecins,id',
            'date_consultation' => 'required|date',
            'diagnostic' => 'nullable|string',
            'examens' => 'nullable|array'
        ]);

        return DB::transaction(function() use ($request) {
            $consultation = Consultation::create($request->only(['dme_id','medecin_id','date_consultation','diagnostic']));

            if (!empty($request->examens)) {
                foreach ($request->examens as $exam) {
                    $type = TypeExamen::where('code', $exam['type_examen_code'])->firstOrFail();
                    Examen::create([
                        'consultation_id' => $consultation->id,
                        'type_examen_id' => $type->id,
                        'date_examen' => $exam['date_examen'] ?? null,
                        'etat' => 'pending',
                        'remarques' => $exam['remarques'] ?? null
                    ]);
                }
            }

            return response()->json($consultation->load('examens'), 201);
        });
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Récupère la consultation avec ses examens, le DME et le patient associé
        $consultation = Consultation::with([
            'examens',      // tous les examens liés
            'dme.patient'   // le DME et le patient associé
        ])->findOrFail($id);

        return response()->json($consultation);
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
        $consultation = Consultation::findOrFail($id);
        $consultation->update($request->all());
        return response()->json($consultation);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        return response()->json(['message' => 'Consultation supprimée']);
    }
}
