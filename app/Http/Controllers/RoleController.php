<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
     public function listRoles(): JsonResponse
    {
        $roles = Role::all();
        return response()->json($roles);
    }
}
