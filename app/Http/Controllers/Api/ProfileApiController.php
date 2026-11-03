<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Attributes as OA;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileApiController extends Controller
{
    /**
     * Display the authenticated user's profile.
     */
    #[OA\Get(path: '/profile', operationId: 'getProfile', tags: ['Profile'], summary: 'Ambil profil pengguna login', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'Profil pengguna', content: new OA\JsonContent(properties: [new OA\Property(property: 'user', type: 'object')])), new OA\Response(response: 401, description: 'Tidak terautentikasi')])]
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    #[OA\Patch(path: '/profile', operationId: 'updateProfile', tags: ['Profile'], summary: 'Ubah nama dan email profil', security: [['bearerAuth' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'email'], properties: [new OA\Property(property: 'name', type: 'string', maxLength: 255), new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 255)])), responses: [new OA\Response(response: 200, description: 'Profil diperbarui'), new OA\Response(response: 401, description: 'Tidak terautentikasi'), new OA\Response(response: 422, description: 'Validasi gagal')])]
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $request->user()->fresh(),
        ]);
    }

    /**
     * Delete the user's account.
     */
    #[OA\Delete(path: '/profile', operationId: 'deleteProfile', tags: ['Profile'], summary: 'Hapus akun', security: [['bearerAuth' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['password'], properties: [new OA\Property(property: 'password', type: 'string', format: 'password')])), responses: [new OA\Response(response: 200, description: 'Akun dihapus'), new OA\Response(response: 401, description: 'Tidak terautentikasi'), new OA\Response(response: 422, description: 'Password tidak valid')])]
    public function destroy(Request $request): JsonResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Account deleted successfully.',
        ]);
    }
}
