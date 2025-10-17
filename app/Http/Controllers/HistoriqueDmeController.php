<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\HistoriqueDme;
class HistoriqueDmeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        $query = HistoriqueDme::with('user', 'dme.patient');

        // Filtrer par DME
        if ($dmeId = $request->query('dme_id')) {
            $query->where('dme_id', $dmeId);
        }

        // Filtrer par action
        if ($action = $request->query('action')) {
            $query->where('action', $action);
        }

        return response()->json($query->orderBy('created_at', 'desc')->paginate($perPage));
    }

    /**
     * Voir un historique spécifique
     */
    public function show($id)
    {
        $historique = HistoriqueDme::with('user', 'dme.patient')->findOrFail($id);
        return response()->json($historique);
    }

    /**
     * Supprimer un historique (optionnel)
     */
    public function destroy($id)
    {
        $historique = HistoriqueDme::findOrFail($id);
        $historique->delete();

        return response()->json(['message' => 'Historique supprimé']);
    }
}
