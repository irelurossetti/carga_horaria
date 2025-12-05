<?php

namespace App\Http\Controllers;

use App\Models\TeacherAvailability;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherAvailabilityController extends Controller
{
    /**
     * Vista de disponibilidad del docente
     */
    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('dashboard')->with('error', 'No tienes un perfil de docente.');
        }

        return view('docente.availability', compact('teacher'));
    }

    /**
     * Obtener disponibilidad del docente
     */
    public function getAvailability(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'No teacher profile'], 403);
        }

        $availabilities = TeacherAvailability::where('teacher_id', $teacher->id)->get();

        return response()->json([
            'success' => true,
            'availabilities' => $availabilities
        ]);
    }

    /**
     * Guardar/Actualizar disponibilidad
     */
    public function saveAvailability(Request $request)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
            'is_available' => 'required|boolean',
            'preference' => 'nullable|string|in:preferred,neutral,avoid',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'No teacher profile'], 403);
        }

        try {
            $availability = TeacherAvailability::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'day_of_week' => $validated['day_of_week'],
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                ],
                [
                    'is_available' => $validated['is_available'],
                    'preference' => $validated['preference'] ?? 'neutral',
                    'notes' => $validated['notes'] ?? null,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Disponibilidad guardada exitosamente',
                'availability' => $availability
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar múltiples disponibilidades
     */
    public function saveBulkAvailability(Request $request)
    {
        $validated = $request->validate([
            'availabilities' => 'required|array',
            'availabilities.*.day_of_week' => 'required|string',
            'availabilities.*.start_time' => 'required',
            'availabilities.*.end_time' => 'required',
            'availabilities.*.is_available' => 'required|boolean',
            'availabilities.*.preference' => 'nullable|string|in:preferred,neutral,avoid',
        ]);

        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'No teacher profile'], 403);
        }

        DB::beginTransaction();
        try {
            $saved = [];
            foreach ($validated['availabilities'] as $avail) {
                $availability = TeacherAvailability::updateOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'day_of_week' => $avail['day_of_week'],
                        'start_time' => $avail['start_time'],
                        'end_time' => $avail['end_time'],
                    ],
                    [
                        'is_available' => $avail['is_available'],
                        'preference' => $avail['preference'] ?? 'neutral',
                    ]
                );
                $saved[] = $availability;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($saved) . ' disponibilidades guardadas',
                'availabilities' => $saved
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar disponibilidad
     */
    public function deleteAvailability($id)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'No teacher profile'], 403);
        }

        try {
            $availability = TeacherAvailability::where('id', $id)
                ->where('teacher_id', $teacher->id)
                ->firstOrFail();

            $availability->delete();

            return response()->json([
                'success' => true,
                'message' => 'Disponibilidad eliminada'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Limpiar todas las disponibilidades del docente
     */
    public function clearAvailability()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'No teacher profile'], 403);
        }

        try {
            TeacherAvailability::where('teacher_id', $teacher->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Todas las disponibilidades han sido eliminadas'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar: ' . $e->getMessage()
            ], 500);
        }
    }
}
