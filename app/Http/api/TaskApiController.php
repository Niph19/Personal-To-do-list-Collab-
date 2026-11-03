<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar tugas berhasil diambil.',
            'data' => Tugas::all(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $task = Tugas::find($id);
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Tugas tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Detail tugas berhasil diambil.', 'data' => $task]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:todo,done'],
        ]);
        $task = Tugas::create(['status' => 'todo', ...$data]);

        return response()->json(['success' => true, 'message' => 'Tugas berhasil dibuat.', 'data' => $task], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $task = Tugas::find($id);
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Tugas tidak ditemukan.'], 404);
        }

        $task->update($request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'status' => ['sometimes', 'in:todo,done'],
        ]));

        return response()->json(['success' => true, 'message' => 'Tugas berhasil diperbarui.', 'data' => $task->fresh()]);
    }

    public function destroy(int $id): JsonResponse
    {
        $task = Tugas::find($id);
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Tugas tidak ditemukan.'], 404);
        }

        $task->delete();
        return response()->json(['success' => true, 'message' => 'Tugas berhasil dihapus.']);
    }
}
