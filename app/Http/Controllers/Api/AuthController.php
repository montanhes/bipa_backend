<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GoogleLoginRequest;
use App\Models\User;
use App\Services\GoogleTokenVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function google(GoogleLoginRequest $request, GoogleTokenVerifier $verifier): JsonResponse
    {
        $claims = $verifier->verify($request->validated('id_token'));

        if ($claims === null) {
            return response()->json(['message' => 'Token do Google inválido.'], 401);
        }

        $user = User::updateOrCreate(
            ['google_id' => $claims['sub']],
            ['name' => $claims['name'], 'email' => $claims['email'], 'avatar' => $claims['picture']],
        );

        return response()->json([
            'token' => $user->createToken('bipa')->plainTextToken,
            'user' => $user->only(['id', 'name', 'email', 'avatar']),
        ]);
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
