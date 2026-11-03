<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TaskApiController extends Controller
{
    #[OA\Get(path: '/tugas', operationId: 'listTasks', tags: ['Tasks'], summary: 'Daftar tugas', security: [['bearerAuth' => []]], responses: [new OA\Response(response: 200, description: 'Daftar tugas'), new OA\Response(response: 401, description: 'Tidak terautentikasi')])]
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Daftar tugas berhasil diambil.',
            'data' => Tugas::all(),
        ]);
    }

    #[OA\Get(path: '/tugas/{id}', operationId: 'getTask', tags: ['Tasks'], summary: 'Detail tugas', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Detail tugas'), new OA\Response(response: 404, description: 'Tugas tidak ditemukan'), new OA\Response(response: 401, description: 'Tidak terautentikasi')])]
    public function show(int $id): JsonResponse
    {
        $task = Tugas::find($id);
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Tugas tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Detail tugas berhasil diambil.', 'data' => $task]);
    }

    #[OA\Post(path: '/tugas', operationId: 'createTask', tags: ['Tasks'], summary: 'Buat tugas', security: [['bearerAuth' => []]], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['title'], properties: [new OA\Property(property: 'title', type: 'string', maxLength: 255), new OA\Property(property: 'deadline', type: 'string', format: 'date-time', nullable: true), new OA\Property(property: 'status', type: 'string', enum: ['todo', 'done'], default: 'todo') ])), responses: [new OA\Response(response: 201, description: 'Tugas dibuat'), new OA\Response(response: 401, description: 'Tidak terautentikasi'), new OA\Response(response: 422, description: 'Validasi gagal')])]
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

    #[OA\Patch(path: '/tugas/{id}', operationId: 'updateTask', tags: ['Tasks'], summary: 'Ubah tugas atau status todo/done', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [new OA\Property(property: 'title', type: 'string', maxLength: 255), new OA\Property(property: 'deadline', type: 'string', format: 'date-time', nullable: true), new OA\Property(property: 'status', type: 'string', enum: ['todo', 'done'])])), responses: [new OA\Response(response: 200, description: 'Tugas diperbarui'), new OA\Response(response: 404, description: 'Tugas tidak ditemukan'), new OA\Response(response: 401, description: 'Tidak terautentikasi'), new OA\Response(response: 422, description: 'Validasi gagal')])]
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

    #[OA\Delete(path: '/tugas/{id}', operationId: 'deleteTask', tags: ['Tasks'], summary: 'Hapus tugas', security: [['bearerAuth' => []]], parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))], responses: [new OA\Response(response: 200, description: 'Tugas dihapus'), new OA\Response(response: 404, description: 'Tugas tidak ditemukan'), new OA\Response(response: 401, description: 'Tidak terautentikasi')])]
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
