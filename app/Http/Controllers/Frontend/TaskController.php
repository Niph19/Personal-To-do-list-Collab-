<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TaskController extends Controller
{
    private $apiBaseUrl = 'http://localhost:8000/api';

    /**
     * Get all tasks from backend API
     * GET /api/tugas
     * Response: { success, message, data: [...tugas] }
     * BE structure: id (int), title, deadline (datetime), status (todo/done)
     */
    public function index(Request $request)
    {
        try {
            $token = $request->bearerToken();
            
            $response = Http::withToken($token)
                ->get($this->apiBaseUrl . '/tugas');

            if ($response->successful()) {
                // Pass through response sesuai kontrak BE
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tugas'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single task detail (optional - FE tidak pakai, tapi ada di kontrak)
     * GET /api/tugas/{id}
     * Response: { success, message, data: {id, title, deadline, status} }
     */
    public function show(Request $request, $id)
    {
        try {
            $token = $request->bearerToken();
            
            $response = Http::withToken($token)
                ->get($this->apiBaseUrl . "/tugas/{$id}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak ditemukan'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new task
     * POST /api/tugas
     * Body: { title, deadline, status }
     * Response: { success, message, data: {id, title, deadline, status} }
     */
    public function store(Request $request)
    {
        try {
            $token = $request->bearerToken();
            
            $response = Http::withToken($token)
                ->post($this->apiBaseUrl . '/tugas', [
                    'title' => $request->title,
                    'deadline' => $request->deadline,
                    'status' => $request->status ?? 'todo'
                ]);

            if ($response->successful()) {
                return response()->json($response->json(), 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tugas'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update existing task
     * PUT /api/tugas/{id}
     * Body: { title, deadline, status }
     * Response: { success, message, data: {id, title, deadline, status} }
     */
    public function update(Request $request, $id)
    {
        try {
            $token = $request->bearerToken();
            
            $response = Http::withToken($token)
                ->put($this->apiBaseUrl . "/tugas/{$id}", [
                    'title' => $request->title,
                    'deadline' => $request->deadline,
                    'status' => $request->status
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tugas'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete task
     * DELETE /api/tugas/{id}
     * Response: { success, message }
     */
    public function destroy(Request $request, $id)
    {
        try {
            $token = $request->bearerToken();
            
            $response = Http::withToken($token)
                ->delete($this->apiBaseUrl . "/tugas/{$id}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak dapat ditemukan atau sudah dihapus'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
