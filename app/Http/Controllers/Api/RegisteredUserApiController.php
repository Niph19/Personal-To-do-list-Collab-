<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use OpenApi\Attributes as OA;

class RegisteredUserApiController extends Controller
{
    /**
     * @throws ValidationException
     */
    #[OA\Post(path: '/register', operationId: 'register', tags: ['Authentication'], summary: 'Register akun baru', requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'email', 'password', 'password_confirmation'], properties: [new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Hanif'), new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'), new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8), new OA\Property(property: 'password_confirmation', type: 'string', format: 'password')])) , responses: [new OA\Response(response: 200, description: 'Akun berhasil dibuat; response memuat user dan access_token'), new OA\Response(response: 422, description: 'Validasi gagal')])]
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', 'min:8', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => $user,
            'access_token' => $token,
        ]);
    }
}
