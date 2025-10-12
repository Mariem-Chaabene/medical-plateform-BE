<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\AntecedentMedical;
use App\Http\Models\HistoriqueDme;

class AntecedentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($dmeId)
    {
        return response()->json(AntecedentMedical::where('dme_id', $dmeId)->orderBy('date', 'desc')->get());
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
        'type' => 'required|string|max:255',
        'description' => 'nullable|string',
        'date' => 'nullable|date'
    ]);

    return DB::transaction(function() use ($request, $dmeId) {
        $antecedent = AntecedentMedical::create([
            'dme_id' => $dmeId,
            'type' => $request->type,
            'description' => $request->description,
            'date' => $request->date
        ]);

        HistoriqueDme::create([
            'dme_id' => $dmeId,
            'user_id' => auth()->id(),
            'action' => 'create_antecedent',
            'old' => null,
            'new' => $antecedent->toArray()
        ]);

        return response()->json($antecedent, 201);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
