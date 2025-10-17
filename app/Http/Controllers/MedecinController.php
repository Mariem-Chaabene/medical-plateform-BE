<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medecin;

class MedecinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);

        $query = Medecin::with('user');

        if ($search = $request->query('q')) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('id','desc')->paginate($perPage));
    }

    /**
     * Voir un médecin spécifique
     */
    public function show($id)
    {
        $medecin = Medecin::with('user')->findOrFail($id);

        return response()->json($medecin);
    }
}
