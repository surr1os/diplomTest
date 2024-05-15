<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Guid\Guid;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->authenticate();
        $user = $request->user();

        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 60*60*2, // 2 hours expiration time
        ];
        $token = JWT::encode($payload, 'sekret_key', 'HS256');

        $request->session()->regenerate();

        return response()->json(['token' => $token, 'userName' => $user->name, 'userId' => $user->id]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
