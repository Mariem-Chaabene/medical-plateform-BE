<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Analyse;
use App\Models\HistoriqueDme;
use Illuminate\Support\Facades\DB;

class AnalyseController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Analyse::with(['dme', 'consultation', 'typeAnalyse'])->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'dme_id' => 'required|exists:dmes,id',
            'type_analyse_id' => 'required|exists:type_analyses,id',
            'date_analyse' => 'required|date',
            'resultat' => 'nullable|string',
            'remarques' => 'nullable|string',
            'consultation_id' => 'nullable|exists:consultations,id',
        ]);

        $analyse = Analyse::create($request->all());

        return response()->json([
            'message' => 'Analyse enregistrée avec succès',
            'data' => $analyse
        ], 201);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function show($id)
    {
        return Analyse::with(['dme', 'consultation', 'typeAnalyse'])->findOrFail($id);
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
        $analyse = Analyse::where('dme_id', $dmeId)->findOrFail($id);
        return DB::transaction(function() use ($analyse, $request, $dmeId) {
            $old = $analyse->toArray();
            $analyse->update($request->all());
            HistoriqueDme::create([
                'dme_id' => $dmeId,
                'user_id' => auth()->id(),
                'action' => 'update_analyse',
                'old' => $old,
                'new' => $analyse->toArray()
            ]);
            return response()->json($analyse);
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
        $analyse = Analyse::where('dme_id', $dmeId)->findOrFail($id);
        return DB::transaction(function() use ($analyse, $dmeId) {
            $old = $analyse->toArray();
            $analyse->delete();
            HistoriqueDme::create([
                'dme_id' => $dmeId,
                'user_id' => auth()->id(),
                'action' => 'delete_analyse',
                'old' => $old,
                'new' => null
            ]);
            return response()->json(['message'=>'Analyse supprimée']);
        });
    }
}
