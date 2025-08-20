<?php
// app/Http/Requests/Auth/RegisterRequest.php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Enum;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public endpoint; add throttling via middleware
    }

    public function rules(): array
    {
        return [
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','email','max:255','unique:users,email'],
            'password' => ['required', Password::defaults()],
            'role'     => ['nullable', new Enum(UserRole::class)],
        ];
    }
}
