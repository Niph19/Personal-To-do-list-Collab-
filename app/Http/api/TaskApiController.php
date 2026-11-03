<?php

namespace App\Http\api;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    /**
     * Get all tasks for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        // Filter by authenticated user
        $tasks = Tugas::where('user_id', $request->user()->id)->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil seluruh tugas',
            'data' => $tasks,
        ]);
    }

    /**
     * Get single task detail
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $task = Tugas::where('user_id', $request->user()->id)->find($id);
        
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil seluruh tugas',
            'data' => $task
        ]);
    }

    /**
     * Create new task for authenticated user
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'deadline' => ['required', 'date'],
            'status' => ['sometimes', 'in:todo,done'],
        ]);
        
        // Create task for authenticated user
        $task = $request->user()->tugas()->create([
            'title' => $data['title'],
            'deadline' => $data['deadline'],
            'status' => $data['status'] ?? 'todo',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dibuat',
            'data' => $task
        ], 201);
    }

    /**
     * Update existing task
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $task = Tugas::where('user_id', $request->user()->id)->find($id);
        
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak ditemukan'
            ], 404);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'deadline' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:todo,done'],
        ]);

        $task->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dibuat',
            'data' => $task->fresh()
        ]);
    }

    /**
     * Delete task
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $task = Tugas::where('user_id', $request->user()->id)->find($id);
        
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas tidak dapat ditemukan atau sudah dihapus'
            ], 404);
        }

        $task->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dihapus'
        ]);
    }
}
