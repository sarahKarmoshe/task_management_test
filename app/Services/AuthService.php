<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user and create an API token.
     */
    public function register(array $data): array
    {
        $payload = Arr::only($data, ['name','email','password','role']);

        $payload['role'] = $payload['role'] ?? UserRole::User;

        $payload['password'] = Hash::make($payload['password']);

        $user = User::create($payload);

        $token = $user->createToken('auth')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * Login with email/password and return a fresh token.
     */
    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout: delete current token only.
     * To logout-all, call $user->tokens()->delete() instead.
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
