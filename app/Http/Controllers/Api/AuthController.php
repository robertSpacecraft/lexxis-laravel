<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        return ApiResponse::success(
            data: null,
            message: 'Login correcto'
        );
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return ApiResponse::success(
            data: null,
            message: 'Logout correcto'
        );
    }

    public function tokenLogin(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return ApiResponse::error(
                message: 'Credenciales incorrectas',
                status: 401
            );
        }

        if (!$user->is_active) {
            return ApiResponse::error(
                message: 'Usuario inactivo',
                status: 403
            );
        }

        $user->tokens()->delete();

        $token = $user->createToken('postman-token')->plainTextToken;

        return ApiResponse::success(
            data: [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role?->value,
                ],
            ],
            message: 'Login correcto'
        );
    }

    public function updateMe(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'string', 'min:6'],
        ]);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return ApiResponse::success(
            data: $user->fresh(),
            message: 'Perfil actualizado correctamente.'
        );
    }
}
