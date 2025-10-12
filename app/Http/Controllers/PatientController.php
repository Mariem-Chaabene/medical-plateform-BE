<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    /**
     * Afficher la liste des patients (ADMIN ONLY)
     */
    public function index()
    {
        $patients = Patient::with('user')->get();

        return response()->json([
            'count' => $patients->count(),
            'data' => $patients
        ]);
    }
// a utiliser dans le dme controller
    public function patientsWithoutDme()
    {
        // On charge aussi les infos de l'utilisateur pour l'affichage
        $patients = Patient::with('user')
            ->whereDoesntHave('dme') // filtre les patients qui n'ont pas encore de DME
            ->get();

        return response()->json($patients);
    }

    /**
     * Afficher un patient
     */
    public function show($id)
    {
        $patient = Patient::with('user')->findOrFail($id);
        return response()->json($patient);
    }

    
    /**
     * Mettre à jour un patient + ses infos User
     */

    public function update(Request $request, $id)
    {
        // Récupérer le patient avec les infos User
        $patient = Patient::with('user')->findOrFail($id);

        // Validation partielle : tous les champs sont optionnels
        $validated = $request->validate([
            // Champs User
            'name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $patient->user_id,
            'sexe' => 'nullable|in:homme,femme',
            'telephone' => 'nullable|string|max:20',

            // Champs Patient
            'date_naissance' => 'nullable|date',
            'adresse' => 'nullable|string|max:255',
            'antecedents' => 'nullable|string',
        ]);

        // Mise à jour dans une transaction pour sécurité
        DB::transaction(function () use ($patient, $validated) {
            // Mise à jour User uniquement si les champs sont passés
            $patient->user->update([
                'name' => $validated['name'] ?? $patient->user->name,
                'surname' => $validated['surname'] ?? $patient->user->surname,
                'email' => $validated['email'] ?? $patient->user->email,
                'sexe' => $validated['sexe'] ?? $patient->user->sexe,
                'telephone' => $validated['telephone'] ?? $patient->user->telephone,
            ]);

            // Mise à jour Patient uniquement si les champs sont passés
            $patient->update([
                'date_naissance' => $validated['date_naissance'] ?? $patient->date_naissance,
                'adresse' => $validated['adresse'] ?? $patient->adresse,
                'antecedents' => $validated['antecedents'] ?? $patient->antecedents,
            ]);
        });

        return response()->json([
            'message' => 'Patient mis à jour avec succès',
            'patient' => $patient->fresh()->load('user')
        ]);
    }

    /**
     * Supprimer un patient + user associé
     */
    public function destroy($id)
    {
        
        $patient = Patient::with('user')->findOrFail($id);

        DB::transaction(function () use ($patient) {
            $patient->delete();
            $patient->user->delete();
        });

        return response()->json(['message' => 'Patient supprimé avec succès']);
    }
}
