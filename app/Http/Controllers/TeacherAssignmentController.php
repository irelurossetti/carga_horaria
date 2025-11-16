<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeacherAssignment;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

class TeacherAssignmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/assignments",
     *     summary="CU13 - Listar asignaciones de carga horaria",
     *     tags={"Asignaciones"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="teacher_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="period_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Lista de asignaciones")
     * )
     */
    public function index(Request $request)
    {
        $query = TeacherAssignment::query();
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->query('teacher_id'));
        }
        if ($request->has('period_id')) {
            $query->where('period_id', $request->query('period_id'));
        }
        return response()->json($query->with(['subject','group','teacher'])->get());
    }

    /**
     * @OA\Post(
     *     path="/api/teachers/{id}/assignments",
     *     summary="CU13 - Crear asignación de carga horaria",
     *     tags={"Asignaciones"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"teacher_id"},
     *         @OA\Property(property="teacher_id", type="integer"),
     *         @OA\Property(property="subject_id", type="integer", nullable=true),
     *         @OA\Property(property="group_id", type="integer", nullable=true),
     *         @OA\Property(property="period_id", type="integer", nullable=true)
     *     )),
     *     @OA\Response(response=201, description="Asignación creada")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => 'required|integer|exists:teachers,id',
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'group_id' => 'nullable|integer|exists:groups,id',
            'period_id' => 'nullable|integer|exists:academic_periods,id',
            'horas_semanales' => 'nullable|integer|min:1',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'observaciones' => 'nullable|string|max:1000',
            'tipo_asignacion' => 'nullable|string|max:100',
        ]);

        $data['assigned_by'] = Auth::id();

        $assignment = TeacherAssignment::create($data);

        return response()->json($assignment->load(['subject', 'group', 'teacher']), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/assignments/{id}",
     *     summary="CU13 - Ver asignación por ID",
     *     tags={"Asignaciones"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Asignación encontrada")
     * )
     */
    public function show($id)
    {
        $assignment = TeacherAssignment::with(['subject','group','teacher'])->findOrFail($id);
        return response()->json($assignment);
    }

    /**
     * @OA\Patch(
     *     path="/api/assignments/{id}",
     *     summary="CU13 - Actualizar asignación",
     *     tags={"Asignaciones"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="subject_id", type="integer", nullable=true),
     *         @OA\Property(property="group_id", type="integer", nullable=true),
     *         @OA\Property(property="period_id", type="integer", nullable=true)
     *     )),
     *     @OA\Response(response=200, description="Asignación actualizada")
     * )
     */
    public function update(Request $request, $id)
    {
        $assignment = TeacherAssignment::findOrFail($id);
        $data = $request->validate([
            'subject_id' => 'nullable|integer|exists:subjects,id',
            'group_id' => 'nullable|integer|exists:groups,id',
            'period_id' => 'nullable|integer|exists:academic_periods,id',
        ]);
        $assignment->update($data);
        return response()->json($assignment);
    }

    /**
     * @OA\Delete(
     *     path="/api/assignments/{id}",
     *     summary="CU13 - Eliminar asignación",
     *     tags={"Asignaciones"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Asignación eliminada")
     * )
     */
    public function destroy($id)
    {
        $assignment = TeacherAssignment::findOrFail($id);
        $assignment->delete();
        return response()->json(['message' => 'Assignment deleted', 'id' => (int) $id]);
    }
}
