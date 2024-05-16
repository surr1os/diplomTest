<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckJwt extends Controller
{
    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->input('token');

        try {
            JWT::decode($token, new Key('sekret_key', 'HS256'));
            return response()->json(['valid' => true]);
        } catch (\Exception $e) {
            return response()->json(['valid' => false, 'error' => $e->getMessage()]);
        }
    }
}
