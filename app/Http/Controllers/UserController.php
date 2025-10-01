<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


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


    // create user (for admin only)
    public function createUser(Request $request)
    {
        $admin = Auth::user();

        // Vérifier si l'utilisateur connecté est admin
        if (!$admin->hasRole('admin')) {
            return response()->json(['message' => 'Accès refusé, seulement admin'], 403);
        }

        // Validation dynamique des rôles
        $validRoles = Role::pluck('name')->toArray();

        // Valider les données
        $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'sexe' => 'nullable|in:homme,femme',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', $validRoles)
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'sexe' => $request->sexe,
            'telephone' => $request->telephone,
        ]);

        // Assignation du rôle via Spatie
        $user->assignRole($request->role);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user
        ]);
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
