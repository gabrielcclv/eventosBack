<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
        ]);

        /** @var User $user */
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_organizer' => false
        ]);

        $token = $user->createToken('api-token', ['read_only', 'full_access'], now()->addWeek())->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token
        ], 'Usuario registrado correctamente', 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        /** @var User $user */
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Credenciales inválidas.', 'UNAUTHORIZED', 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('api-token', ['full_access'], now()->addWeek())->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token
        ], 'Inicio de sesión exitoso');
    }
    
    public function logout(Request $request): JsonResponse
    {    
        /** @var User $user */
        $user = $request->user();
        $user->tokens()->delete();

        return $this->successResponse(null, 'Sesión cerrada correctamente');
    }
}