<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TraitementChronique;
use App\Models\HistoriqueDme;
use Illuminate\Support\Facades\DB;

class TraitementChroniqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // liste des traitements
    public function index(Request $request, $dmeId)
    {
        $onlyActive = $request->query('active', null); // ?active=1
        $query = TraitementChronique::where('dme_id', $dmeId);
        if (!is_null($onlyActive)) {
            $query->where('is_active', (bool)$onlyActive);
        }
        return response()->json($query->orderBy('date_debut', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $dmeId)
{
    $request->validate([
        'nom_medicament' => 'required|string|max:255',
        'dosage' => 'nullable|string|max:255',
        'frequence' => 'nullable|string|max:255',
        'date_debut' => 'nullable|date',
        'is_replace' => 'nullable|boolean',
        'replace_id' => 'nullable|integer'
    ]);

    return DB::transaction(function() use ($request, $dmeId) {
        if ($request->boolean('is_replace') && $request->filled('replace_id')) {
            $old = TraitementChronique::findOrFail($request->replace_id);
            $old->update([
                'is_active' => false,
                'date_fin' => now()->toDateString()
            ]);

            HistoriqueDme::create([
                'dme_id' => $dmeId,
                'user_id' => auth()->id(),
                'action' => 'terminate_treatment',
                'old' => $old->toArray(),
                'new' => null
            ]);
        }

        $treatment = TraitementChronique::create([
            'dme_id' => $dmeId,
            'nom_medicament' => $request->nom_medicament,
            'dosage' => $request->dosage,
            'frequence' => $request->frequence,
            'date_debut' => $request->date_debut,
            'is_active' => true,
            'remarques' => $request->remarques
        ]);

        HistoriqueDme::create([
            'dme_id' => $dmeId,
            'user_id' => auth()->id(),
            'action' => 'create_treatment',
            'old' => null,
            'new' => $treatment->toArray(),
        ]);

        return response()->json($treatment, 201);
    });
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($dmeId, $id)
    {
        $t = TraitementChronique::where('dme_id', $dmeId)->findOrFail($id);
        return response()->json($t);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $dmeId, $id)
    {
        $t = TraitementChronique::where('dme_id', $dmeId)->findOrFail($id);
        $request->validate([
            'nom_medicament' => 'sometimes|string|max:255',
            'dosage' => 'sometimes|string|max:255',
            'frequence' => 'sometimes|string|max:255',
            'date_debut' => 'sometimes|date',
            'date_fin' => 'sometimes|date',
            'remarques' => 'sometimes|string',
            'is_active' => 'sometimes|boolean'
        ]);

        return DB::transaction(function() use ($t, $request, $dmeId) {
            $old = $t->toArray();
            $t->update($request->all());
            HistoriqueDme::create([
                'dme_id' => $dmeId,
                'user_id' => auth()->id(),
                'action' => 'update_treatment',
                'old' => $old,
                'new' => $t->toArray()
            ]);
            return response()->json($t);
        });
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($dmeId, $id)
    {
        $t = TraitementChronique::where('dme_id', $dmeId)->findOrFail($id);
        return DB::transaction(function() use ($t, $dmeId) {
            $old = $t->toArray();
            $t->delete();
            HistoriqueDme::create([
                'dme_id' => $dmeId,
                'user_id' => auth()->id(),
                'action' => 'delete_treatment',
                'old' => $old,
                'new' => null
            ]);
            return response()->json(['message'=>'Traitement supprimé']);
        });
    }
}
