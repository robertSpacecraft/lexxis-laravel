<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            // role e is_active quedan resueltos por defaults/modelo
        ]);

        event(new Registered($user));

        $user->tokens()->delete();

        $token = $user->createToken('customer-register-token')->plainTextToken;

        return ApiResponse::created(
            data: [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role?->value,
                    'is_active' => (bool) $user->is_active,
                ],
            ],
            message: 'Registro correcto'
        );
    }

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
        $token = $request->user()?->currentAccessToken();

        if ($token && method_exists($token, 'delete')) {
            $token->delete();
        }

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

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

        $throttleKey = $this->tokenLoginThrottleKey($request, $data['email']);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return ApiResponse::error(
                message: "Demasiados intentos. Inténtalo de nuevo en {$seconds} segundos.",
                errors: [
                    'email' => [
                        "Demasiados intentos. Inténtalo de nuevo en {$seconds} segundos.",
                    ],
                ],
                status: 429
            );
        }

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            return ApiResponse::error(
                message: 'Credenciales incorrectas',
                status: 401
            );
        }

        if (!$user->is_active) {
            RateLimiter::hit($throttleKey, 60);

            return ApiResponse::error(
                message: 'Usuario inactivo',
                status: 403
            );
        }

        RateLimiter::clear($throttleKey);

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

    private function tokenLoginThrottleKey(Request $request, string $email): string
    {
        return 'token-login:' . Str::lower($email) . '|' . $request->ip();
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
