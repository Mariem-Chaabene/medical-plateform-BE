<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use App\Models\TypeExamen;


class TypeExamenController extends Controller
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
        $q = TypeExamen::query();

        if ($search = $request->query('q')) {
            $q->where(function($sub) use ($search) {
                $sub->where('code', 'like', "%{$search}%")
                    ->orWhere('libelle', 'like', "%{$search}%");
            });
        }

        // Option: tri
        $sort = $request->query('sort', 'code'); // code ou libelle
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
            'code' => 'required|string|max:20|unique:type_examens,code',
            'libelle' => 'required|string|max:255',
        ]);

        return DB::transaction(function() use ($data) {
            $type = TypeExamen::create($data);
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
        $type = TypeExamen::findOrFail($id);
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
        $type = TypeExamen::findOrFail($id);

        $data = $request->validate([
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('type_examens', 'code')->ignore($type->id),
            ],
            'libelle' => 'sometimes|required|string|max:255',
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
        $type = TypeExamen::findOrFail($id);

        return DB::transaction(function() use ($type) {
            // Option de sécurité : empêcher la suppression si des examens existent
            if ($type->examens()->exists()) {
                return response()->json([
                    'message' => 'Impossible de supprimer : des examens existent pour ce type.'
                ], 422);
            }

            $type->delete();
            return response()->json(['message' => 'Type d\'examen supprimé.']);
        });
    }
    
}
