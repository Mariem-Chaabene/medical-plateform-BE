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

    /**
     * Lister toutes les consultations d’un DME
     */
    public function index()
    {
        $consultations = Consultation::with([
            'dme.patient',
            'medecin',
            'examens.typeExamen',
            'analyses.typeAnalyse'
        ])->get();

        return response()->json($consultations);
    }



    // // 📌 Historique d’un patient (pour dossier)
    // public function historiquePatient($patientId)
    // {
    //     $consultations = Consultation::with(['medecin.user'])
    //         ->where('patient_id', $patientId)
    //         ->orderBy('date_consultation', 'desc')
    //         ->get();

    //     return response()->json($consultations);
    // }


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
            'dme_id' => 'required|exists:dmes,id',
            'date_consultation' => 'required|date',
            'diagnostic' => 'nullable|string',
            'traitement' => 'nullable|string',
        ]);

        // si tu veux prendre le médecin connecté :
        $medecinId = Auth::id(); 

        $consultation = Consultation::create([
            'dme_id' => $request->dme_id,
            'medecin_id' => $medecinId,
            'date_consultation' => $request->date_consultation,
            'diagnostic' => $request->diagnostic,
            'traitement' => $request->traitement
        ]);

        return response()->json($consultation, 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     /**
     * Afficher une seule consultation avec détails
     */
    public function show($id)
    {
        $consultation = Consultation::with([
            'dme.patient',
            'medecin',
            'examens.typeExamen',
            'analyses.typeAnalyse'
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

        $request->validate([
            'date_consultation' => 'nullable|date',
            'diagnostic' => 'nullable|string',
            'traitement' => 'nullable|string',
        ]);

        $consultation->update([
            'date_consultation' => $request->date_consultation ?? $consultation->date_consultation,
            'diagnostic' => $request->diagnostic ?? $consultation->diagnostic,
            'traitement' => $request->traitement ?? $consultation->traitement
        ]);

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
        DB::transaction(function () use ($id) {
            $consultation = Consultation::with(['examens', 'analyses'])->findOrFail($id);

            // Supprimer les examens et analyses liés avant la consultation
            $consultation->examens()->delete();
            $consultation->analyses()->delete();
            $consultation->delete();
        });

        return response()->json(['message' => 'Consultation supprimée avec succès.']);
    }

    /**
     * Récupérer les consultations d'un DME spécifique
     */
    public function getByDme($dmeId)
    {
        $consultations = Consultation::where('dme_id', $dmeId)
            ->with(['medecin', 'examens.typeExamen', 'analyses.typeAnalyse'])
            ->orderBy('date_consultation', 'desc')
            ->get();

        return response()->json($consultations);
    }


}
