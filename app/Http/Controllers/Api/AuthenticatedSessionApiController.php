<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'Todo List API')]
#[OA\Server(url: '/api', description: 'Laravel API')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer', bearerFormat: 'Sanctum token')]
class AuthenticatedSessionApiController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    #[OA\Post(path: '/login', operationId: 'login', tags: ['Authentication'], summary: 'Login', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['email', 'password'], properties: [new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'), new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8)])), responses: [new OA\Response(response: 200, description: 'Login berhasil', content: new OA\JsonContent(properties: [new OA\Property(property: 'message', type: 'string'), new OA\Property(property: 'user', type: 'object'), new OA\Property(property: 'access_token', type: 'string')])), new OA\Response(response: 401, description: 'Email atau password salah'), new OA\Response(response: 422, description: 'Validasi gagal')])]
    public function store(LoginRequest $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', "min:8"],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                    'message' => 'Email atau password salah.',
                    ], 401);
        }

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully.',
            'user' => $user,
            'access_token' => $token,
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    #[OA\Delete(path: '/login', operationId: 'logout', tags: ['Authentication'], summary: 'Logout', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'Logout berhasil'), new OA\Response(response: 401, description: 'Tidak terautentikasi')])]
    public function destroy(Request $request): JsonResponse
    {
        $token = $request->user()?->currentAccessToken();
        if ($token instanceof \Laravel\Sanctum\PersonalAccessToken) {
            $token->delete();
        }

        return response()->json([
            'message' => 'User logged out successfully.',
        ]);
    }
}
