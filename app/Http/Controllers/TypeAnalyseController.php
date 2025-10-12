<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TypeAnalyse;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\DB;

class TypeAnalyseController extends Controller
{


    public function __construct()
    {
        // Exemple : seules les personnes avec le rôle admin peuvent créer/modifier/supprimer
        // $this->middleware(['auth:api']);
        // $this->middleware('role:admin')->only(['store', 'update', 'destroy']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        $q = TypeAnalyse::query();

        if ($search = $request->query('q')) {
            $q->where('libelle', 'like', "%{$search}%");
        }

        $sort = $request->query('sort', 'libelle');
        $dir = $request->query('dir', 'asc');

        $q->orderBy($sort, $dir);

        return response()->json($q->paginate($perPage));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'libelle' => 'required|string|max:255|unique:type_analyses,libelle',
        ]);

        return DB::transaction(function() use ($data) {
            $type = TypeAnalyse::create($data);
            return response()->json($type, 201);
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
        $type = TypeAnalyse::findOrFail($id);
        return response()->json($type);
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
        $type = TypeAnalyse::findOrFail($id);

        $data = $request->validate([
            'libelle' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('type_analyses', 'libelle')->ignore($type->id),
            ],
        ]);

        return DB::transaction(function() use ($type, $data) {
            $type->update($data);
            return response()->json($type);
        });
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = TypeAnalyse::findOrFail($id);

        return DB::transaction(function() use ($type) {
            if ($type->analyses()->exists()) {
                return response()->json([
                    'message' => 'Impossible de supprimer : des analyses existent pour ce type.'
                ], 422);
            }

            $type->delete();
            return response()->json(['message' => 'Type d\'analyse supprimé.']);
        });
    }
}
