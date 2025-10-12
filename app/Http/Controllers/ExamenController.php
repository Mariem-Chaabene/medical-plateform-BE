<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Examen;
use App\Models\Consultation;
use App\Models\TypeExamen;
use Illuminate\Support\Facades\DB;

class ExamenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // 📌 Lister tous les examens (admin)
    public function index()
    {
        return response()->json(
            Examen::with(['dme', 'typeExamen'])->get()
        );
    }


    // 📌 Historique d’examens d’un patient
    public function historiquePatient($patientId)
    {
        $examens = Examen::with('typeExamen')
            ->where('patient_id', $patientId)
            ->orderBy('date_examen', 'desc')
            ->get();

        return response()->json($examens);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // 📌 Créer un examen
    // public function store(Request $request, $patientId)
    // {
    //     $validated = $request->validate([
    //         'dme_id' => 'required|exists:dmes,id',
    //         'type_examen_id' => 'required|exists:type_examens,id',
    //         'date_examen' => 'required|date',
    //         'resultats' => 'nullable|string',
    //     ]);

    //     $examen = Examen::create([
    //         'dme_id' => 'required|exists:dmes,id',
    //         'type_examen_id' => $validated['type_examen_id'],
    //         'date_examen' => $validated['date_examen'],
    //         'resultats' => $validated['resultats'] ?? null,
    //     ]);

    //     return response()->json($examen, 201);
    // }


public function storeForConsultation(Request $request, $consultationId)
{
    $request->validate([
        'examens' => 'required|array'
    ]);

    return DB::transaction(function() use ($request, $consultationId) {
        $consultation = Consultation::findOrFail($consultationId);

        $examens = [];
        foreach ($request->examens as $exam) {
            $type = TypeExamen::where('code', $exam['type_examen_code'])->firstOrFail();
            $examens[] = Examen::create([
                'consultation_id' => $consultation->id,
                'type_examen_id' => $type->id,
                'date_examen' => $exam['date_examen'] ?? null,
                'etat' => 'pending',
                'remarques' => $exam['remarques'] ?? null
            ]);
        }

        return response()->json($examens, 201);
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
        $examen = Examen::with(['dme', 'typeExamen'])->findOrFail($id);
        return response()->json($examen);
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
        $examen = Examen::findOrFail($id);

        $data = $request->validate([
            'type_examen_id' => 'sometimes|exists:type_examens,id',
            'date' => 'sometimes|date',
            'resultats' => 'nullable|string'
        ]);

        $examen->update($data);
        return response()->json($examen);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $examen = Examen::findOrFail($id);
        $examen->delete();

        return response()->json(['message' => 'Examen supprimé']);
    }
}
