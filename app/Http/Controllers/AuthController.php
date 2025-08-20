<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'user'  => new UserResource($result['user']),
            'token' => $result['token'],
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated('email'),
            $request->validated('password'),
        );

        return response()->json([
            'user'  => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        $this->authService->logout($user);

        return response()->json(['message' => 'Logged out']);
    }
}
