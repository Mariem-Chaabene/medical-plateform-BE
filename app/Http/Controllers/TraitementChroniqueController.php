<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TraitementChronique;
use App\Models\Dme;
use Illuminate\Support\Facades\DB;

class TraitementChroniqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // liste des traitements
     public function index($dmeId)
    {
        $dme = Dme::findOrFail($dmeId);

        $traitements = $dme->traitements()->orderByDesc('is_active')->orderBy('date_debut')->get();

        return response()->json($traitements);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $dmeId)
    {
        $dme = Dme::findOrFail($dmeId);

        $data = $request->validate([
            'nom_medicament' => 'required|string|max:255',
            'dosage'         => 'nullable|string|max:255',
            'frequence'      => 'nullable|string|max:255',
            'date_debut'     => 'nullable|date',
            'date_fin'       => 'nullable|date|after_or_equal:date_debut',
            'is_active'      => 'boolean',
            'remarques'      => 'nullable|string',
        ]);

        $data['dme_id'] = $dme->id;

        $traitement = DB::transaction(function () use ($data) {
            return TraitementChronique::create($data);
        });

        return response()->json($traitement, 201);
    }

    /**
     * Afficher un traitement spécifique
     */
    public function show($dmeId, $id)
    {
        $traitement = TraitementChronique::where('dme_id', $dmeId)->findOrFail($id);

        return response()->json($traitement);
    }

    /**
     * Modifier un traitement
     */
    public function update(Request $request, $dmeId, $id)
    {
        $traitement = TraitementChronique::where('dme_id', $dmeId)->findOrFail($id);

        $data = $request->validate([
            'nom_medicament' => 'sometimes|required|string|max:255',
            'dosage'         => 'sometimes|nullable|string|max:255',
            'frequence'      => 'sometimes|nullable|string|max:255',
            'date_debut'     => 'sometimes|nullable|date',
            'date_fin'       => 'sometimes|nullable|date|after_or_equal:date_debut',
            'is_active'      => 'sometimes|boolean',
            'remarques'      => 'sometimes|nullable|string',
        ]);

        $traitement->update($data);

        return response()->json($traitement);
    }

    /**
     * Supprimer un traitement
     */
    public function destroy($dmeId, $id)
    {
        $traitement = TraitementChronique::where('dme_id', $dmeId)->findOrFail($id);

        $traitement->delete();

        return response()->json(['message' => 'Traitement supprimé avec succès']);
    }
}
