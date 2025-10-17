<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Examen;
use App\Models\TypeExamen;
use Illuminate\Support\Facades\Auth;
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
    ])
    ->orderBy('date_consultation', 'desc')
    ->get()
    ->map(function ($consultation) {
        return [
            'id' => $consultation->id,
            'dme_id' => $consultation->dme_id,
            'medecin_id' => $consultation->medecin_id,
            'date_consultation' => $consultation->date_consultation,
            'diagnostic' => $consultation->diagnostic,
            'traitement' => $consultation->traitement,
            'motif' => $consultation->motif,
            'poids' => $consultation->poids,
            'taille' => $consultation->taille,
            'imc' => $consultation->imc,
            'temperature' => $consultation->temperature,
            'frequence_cardiaque' => $consultation->frequence_cardiaque,
            'pression_arterielle' => $consultation->pression_arterielle,
            'dme' => $consultation->dme,
            'patient' => $consultation->dme->patient ?? null,
            'medecin' => $consultation->medecin,
            'examens' => $consultation->examens,
            'analyses' => $consultation->analyses,
        ];
    });

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
            'dme_id' => ['required', 'exists:dmes,id'],
            'date_consultation' => ['required', 'date'],
            'diagnostic' => ['nullable', 'string'],
            'traitement' => ['nullable', 'string'],
            'motif' => ['nullable', 'string'],
            'poids' => ['nullable', 'numeric'],
            'taille' => ['nullable', 'numeric'],
            'temperature' => ['nullable', 'numeric'],
            'frequence_cardiaque' => ['nullable', 'integer'],
            'pression_arterielle' => ['nullable', 'string']
        ]);

        // Si tu veux attribuer automatiquement le médecin connecté :
        $medecinId = Auth::id() ?? $request->input('medecin_id');
        $imc = null;

        if ($request->poids && $request->taille) {
            $taille_m = $request->taille / 100; // convertir cm en mètres
            $imc = $taille_m > 0 ? round($request->poids / ($taille_m ** 2), 2) : null;
        }

        $consultation = Consultation::create([
            'dme_id' => $request->dme_id,
            'medecin_id' => $medecinId,
            'date_consultation' => $request->date_consultation,
            'diagnostic' => $request->diagnostic,
            'traitement' => $request->traitement,
            'motif' => $request->motif,
            'poids' => $request->poids,
            'taille' => $request->taille,
            'imc' => $imc,
            'temperature' => $request->temperature,
            'frequence_cardiaque' => $request->frequence_cardiaque,
            'pression_arterielle' => $request->pression_arterielle,
        ]);

        // Charger les relations pour la réponse
        $consultation->load('dme', 'medecin');

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

        return response()->json([
            'id' => $consultation->id,
            'dme_id' => $consultation->dme_id,
            'medecin_id' => $consultation->medecin_id,
            'date_consultation' => $consultation->date_consultation,
            'diagnostic' => $consultation->diagnostic,
            'traitement' => $consultation->traitement,
            'motif' => $consultation->motif,
            'poids' => $consultation->poids,
            'taille' => $consultation->taille,
            'imc' => $consultation->imc,
            'temperature' => $consultation->temperature,
            'frequence_cardiaque' => $consultation->frequence_cardiaque,
            'pression_arterielle' => $consultation->pression_arterielle,
            'dme' => $consultation->dme,
            'patient' => $consultation->dme->patient ?? null,
            'medecin' => $consultation->medecin,
            'examens' => $consultation->examens,
            'analyses' => $consultation->analyses,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Consultation $consultation)
{
    $request->validate([
        'dme_id' => ['sometimes', 'exists:dmes,id'],
        'date_consultation' => ['sometimes', 'date'],
        'diagnostic' => ['nullable', 'string'],
        'traitement' => ['nullable', 'string'],
        'motif' => ['nullable', 'string'],
        'poids' => ['nullable', 'numeric'],
        'taille' => ['nullable', 'numeric'],
        'temperature' => ['nullable', 'numeric'],
        'frequence_cardiaque' => ['nullable', 'integer'],
        'pression_arterielle' => ['nullable', 'string']
    ]);

    // Mise à jour des champs seulement si fournis
    $consultation->fill($request->only([
        'dme_id',
        'date_consultation',
        'diagnostic',
        'traitement',
        'motif',
        'poids',
        'taille',
        'temperature',
        'frequence_cardiaque',
        'pression_arterielle'
    ]));

    // Recalcul automatique de l'IMC si poids et taille sont présents
    if ($consultation->poids && $consultation->taille) {
        $taille_m = $consultation->taille / 100; // convertir cm en mètres
        $consultation->imc = $taille_m > 0 ? round($consultation->poids / ($taille_m ** 2), 2) : null;
    } else {
        $consultation->imc = null;
    }

    $consultation->save();

    // Charger relations pour la réponse
    $consultation->load('dme', 'medecin');

    return response()->json($consultation, 200);
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
