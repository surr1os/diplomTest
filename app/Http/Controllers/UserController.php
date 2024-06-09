<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getAllUsers(Request $request) : JsonResponse
    {
        $users = User::all()->map(function($user) {
            return ['name' => $user->name];
        })->toArray();
        return response()->json($users, 201);
    }
}
