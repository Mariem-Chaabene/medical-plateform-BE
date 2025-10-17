<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AntecedentMedical;

class AntecedentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AntecedentMedical::with('dme.patient.user')->get();
    }

    /**
     * Créer un nouvel antécédent pour un DME
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'dme_id' => 'required|exists:dmes,id',
            'nom_maladie' => 'required|string|max:255',
            'date_diagnostic' => 'nullable|date',
            'remarques' => 'nullable|string'
        ]);

        $antecedent = AntecedentMedical::create($validated);

        return response()->json($antecedent, 201);
    }

    /**
     * Voir un antécédent spécifique
     */
    public function show($id)
    {
        $antecedent = AntecedentMedical::with('dme.patient.user')->findOrFail($id);
        return response()->json($antecedent);
    }

    /**
     * Modifier un antécédent
     */
    public function update(Request $request, $id)
    {
        $antecedent = AntecedentMedical::findOrFail($id);

        $data = $request->validate([
            'nom_maladie' => 'sometimes|required|string|max:255',
            'date_diagnostic' => 'sometimes|nullable|date',
            'remarques' => 'sometimes|nullable|string'
        ]);

        $antecedent->update($data);

        return response()->json($antecedent);
    }

    /**
     * Supprimer un antécédent
     */
    public function destroy($id)
    {
        $antecedent = AntecedentMedical::findOrFail($id);
        $antecedent->delete();

        return response()->json(['message' => 'Antécédent supprimé avec succès']);
    }
}
