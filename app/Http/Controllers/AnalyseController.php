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
    public function index($dmeId)
    {
        return response()->json(Analyse::with('type')->where('dme_id', $dmeId)->get());
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
            'type_analyse_id' => 'required|integer|exists:type_analyses,id',
            'date_analyse' => 'nullable|date',
            'resultat' => 'nullable|string',
            'remarques' => 'nullable|string'
        ]);

        return DB::transaction(function() use ($request, $dmeId) {
            $analyse = Analyse::create([
                'dme_id' => $dmeId,
                'type_analyse_id' => $request->type_analyse_id,
                'date_analyse' => $request->date_analyse,
                'resultat' => $request->resultat,
                'remarques' => $request->remarques
            ]);

            HistoriqueDme::create([
                'dme_id' => $dmeId,
                'user_id' => auth()->id(),
                'action' => 'create_analyse',
                'old' => null,
                'new' => $analyse->toArray()
            ]);

            return response()->json($analyse, 201);
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
        $analyse = Analyse::with('type')->where('dme_id', $dmeId)->findOrFail($id);
        return response()->json($analyse);
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
