<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Infirmier; 
class InfermierController extends Controller
{
        public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);

        $query = Infirmier::with('user');

        if ($search = $request->query('q')) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->orderBy('id','desc')->paginate($perPage));
    }

    /**
     * Voir un infirmier spécifique
     */
    public function show($id)
    {
       
        $infirmier = Infirmier::with('user')->findOrFail($id);

        return response()->json($infirmier);
    }
}
