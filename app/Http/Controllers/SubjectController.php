<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    protected function ensureAdmin()
    {
        $user = auth()->user();
        if (!$user || ! (
            ($user->hasRole('ADMIN') || $user->hasRole('administrador'))
            || (property_exists($user, 'is_admin') ? $user->is_admin : ($user->is_admin ?? false))
        )) {
            abort(response()->json(['message' => 'Forbidden'], 403));
        }
    }

    /**
     * @OA\Get(
     *     path="/api/subjects",
     *     summary="CU08 - Listar materias",
     *     tags={"Materias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Response(response=200, description="Lista de materias"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index()
    {
        // Permitir acceso a usuarios autenticados
        // $this->ensureAdmin();
        return response()->json(Subject::orderBy('created_at', 'desc')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/subjects",
     *     summary="CU08 - Crear materia",
     *     tags={"Materias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"code", "name"},
     *         @OA\Property(property="code", type="string", example="SIS-101"),
     *         @OA\Property(property="name", type="string", example="Programación I"),
     *         @OA\Property(property="credits", type="integer", nullable=true, example=4),
     *         @OA\Property(property="description", type="string", nullable=true)
     *     )),
     *     @OA\Response(response=201, description="Materia creada"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Código duplicado")
     * )
     */
    public function store(Request $request)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'code' => 'required|string|unique:subjects,code',
            'name' => 'required|string',
            'credits' => 'nullable|integer',
            'description' => 'nullable|string',
            'semester' => 'nullable|integer|min:1|max:10',
            'theoretical_hours' => 'nullable|integer|min:0',
            'practical_hours' => 'nullable|integer|min:0',
            'prerequisites' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $subject = Subject::create($data);
        return response()->json($subject, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/subjects/{id}",
     *     summary="CU08 - Ver materia",
     *     tags={"Materias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Materia encontrada"),
     *     @OA\Response(response=404, description="No encontrada")
     * )
     */
    public function show($id)
    {
        $this->ensureAdmin();
        return response()->json(Subject::findOrFail($id));
    }

    /**
     * @OA\Patch(
     *     path="/api/subjects/{id}",
     *     summary="CU08 - Actualizar materia",
     *     tags={"Materias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="code", type="string"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="credits", type="integer", nullable=true),
     *         @OA\Property(property="description", type="string", nullable=true)
     *     )),
     *     @OA\Response(response=200, description="Materia actualizada"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Código duplicado")
     * )
     */
    public function update(Request $request, $id)
    {
        $this->ensureAdmin();
        $subject = Subject::findOrFail($id);

        $data = $request->validate([
            'code' => 'sometimes|required|string|unique:subjects,code,' . $id,
            'name' => 'sometimes|required|string',
            'credits' => 'nullable|integer',
            'description' => 'nullable|string',
            'semester' => 'nullable|integer|min:1|max:10',
            'theoretical_hours' => 'nullable|integer|min:0',
            'practical_hours' => 'nullable|integer|min:0',
            'prerequisites' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $subject->fill($data);
        $subject->save();

        return response()->json($subject);
    }

    /**
     * @OA\Delete(
     *     path="/api/subjects/{id}",
     *     summary="CU08 - Eliminar materia",
     *     tags={"Materias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Materia eliminada"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function destroy($id)
    {
        $this->ensureAdmin();
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return response()->json(['message' => 'Subject deleted', 'id' => $id]);
    }
}
