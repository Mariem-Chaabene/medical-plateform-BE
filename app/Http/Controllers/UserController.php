<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Models\Medecin;
use App\Models\Patient;
use App\Models\Infirmier;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }



    /******get roles for frontend not validation in backend ********/
        public function getRoles()
            {
                $roles = Role::pluck('name'); // Récupère tous les rôles existants
                return response()->json($roles);
            }
    /***************************************************************/



    public function createUser(Request $request)
    {
        $admin = Auth::user();

        if (!$admin->hasRole('admin')) {
            return response()->json(['message' => 'Accès refusé, seulement admin'], 403);
        }

        $validRoles = Role::pluck('name')->toArray();

        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'sexe' => 'nullable|in:homme,femme',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', $validRoles),
        ]);

        // Validation selon le rôle (comme avant)
        switch ($request->role) {
            case 'medecin':
                $request->validate([
                    'specialite' => 'required|string|max:255',
                    'numero_inscription' => 'required|string|max:255',
                ]);
                break;

            case 'patient':
                $request->validate([
                    'date_naissance' => 'required|date',
                    'adresse' => 'required|string|max:255',
                    'antecedents' => 'nullable|string',
                ]);
                break;

            case 'infirmier':
                $request->validate([
                    'numero_inscription' => 'required|string|max:255',
                    'departement' => 'required|string|max:255',
                ]);
                break;
        }

        // ✅ Tout le bloc est dans une transaction
        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'sexe' => $request->sexe,
                'telephone' => $request->telephone,
            ]);

            $user->assignRole($request->role);

            switch ($request->role) {
                case 'medecin':
                    \App\Models\Medecin::create([
                        'user_id' => $user->id,
                        'specialite' => $request->specialite,
                        'numero_inscription' => $request->numero_inscription,
                    ]);
                    break;

                case 'patient':
                    \App\Models\Patient::create([
                        'user_id' => $user->id,
                        'date_naissance' => $request->date_naissance,
                        'adresse' => $request->adresse,
                        'antecedents' => $request->antecedents,
                    ]);
                    break;

                case 'infirmier':
                    \App\Models\Infirmier::create([
                        'user_id' => $user->id,
                        'numero_inscription' => $request->numero_inscription,
                        'departement' => $request->departement,
                    ]);
                    break;
            }

            return response()->json([
                'message' => 'Utilisateur créé avec succès',
                'user' => $user->load('roles'),
            ]);
        });
    }



    // ----------------------
    // LIST USERS (ADMIN ONLY)
    // ----------------------
    public function listUsers()
    {
        $admin = Auth::user();

        if (!$admin->hasRole('admin')) {
            return response()->json(['message' => 'Accès refusé, seulement admin'], 403);
        }

        $users = User::with('roles')->get();

        return response()->json($users);
    }
}
