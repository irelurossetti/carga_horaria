<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SubstituteService;
use App\Models\Schedule;
use App\Models\ClassCancellation;
use App\Models\Incident;

class SubstituteController extends Controller
{
    protected $substituteService;

    public function __construct(SubstituteService $substituteService)
    {
        $this->substituteService = $substituteService;
    }

    /**
     * Buscar docentes disponibles para suplencia
     * 
     * GET /api/substitutes/available
     */
    public function findAvailable(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'exclude_teacher_id' => 'nullable|integer|exists:teachers,id',
        ]);

        $availableTeachers = $this->substituteService->findAvailableSubstitutes(
            $data['date'],
            $data['start_time'],
            $data['end_time'],
            $data['exclude_teacher_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'data' => $availableTeachers,
            'count' => $availableTeachers->count(),
        ]);
    }

    /**
     * Asignar un suplente a una clase
     * 
     * POST /api/substitutes/assign
     */
    public function assign(Request $request)
    {
        $data = $request->validate([
            'schedule_id' => 'required|integer|exists:schedules,id',
            'substitute_teacher_id' => 'nullable|integer|exists:teachers,id',
            'original_teacher_id' => 'required|integer|exists:teachers,id',
            'reason' => 'nullable|string|max:500',
            'is_external' => 'nullable|boolean',
            'external_name' => 'required_if:is_external,true|string|max:255',
            'external_email' => 'nullable|email|max:255',
            'external_phone' => 'nullable|string|max:20',
        ]);

        try {
            $externalData = null;
            if ($data['is_external'] ?? false) {
                $externalData = [
                    'name' => $data['external_name'],
                    'email' => $data['external_email'] ?? null,
                    'phone' => $data['external_phone'] ?? null,
                ];
            }
            
            $attendance = $this->substituteService->assignSubstitute(
                $data['schedule_id'],
                $data['substitute_teacher_id'] ?? null,
                $data['original_teacher_id'],
                $data['reason'] ?? null,
                $externalData
            );

            return response()->json([
                'success' => true,
                'message' => 'Suplente asignado exitosamente',
                'data' => $attendance,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al asignar suplente',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar todas las suplencias
     * 
     * GET /api/substitutes
     */
    public function index(Request $request)
    {
        try {
            $query = \App\Models\Attendance::whereRaw('is_substitute = true')
                ->with(['substituteTeacher', 'originalTeacher', 'schedule.group.subject', 'schedule.room']);

            // Filtros opcionales
            if ($request->has('start_date')) {
                $query->where('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->where('created_at', '<=', $request->end_date);
            }

            if ($request->has('substitute_teacher_id')) {
                $query->where('substitute_teacher_id', $request->substitute_teacher_id);
            }

            $substitutes = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $substitutes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar suplencias',
                'error' => $e->getMessage(),
                'data' => []
            ], 200); // Devolver 200 para que el frontend no falle
        }
    }

    /**
     * Obtener estadísticas de suplencias
     * 
     * GET /api/substitutes/stats
     */
    public function stats(Request $request)
    {
        try {
            $stats = $this->substituteService->getSubstituteStats(
                $request->start_date ?? null,
                $request->end_date ?? null
            );

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar estadísticas',
                'error' => $e->getMessage(),
                'data' => [
                    'total' => 0,
                    'by_teacher' => []
                ]
            ], 200);
        }
    }

    /**
     * Buscar suplente para una cancelación específica
     * 
     * GET /api/substitutes/for-cancellation/{cancellationId}
     */
    public function forCancellation($cancellationId)
    {
        $cancellation = ClassCancellation::with('schedule.teacher')->findOrFail($cancellationId);
        $schedule = $cancellation->schedule;

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Horario no encontrado',
            ], 404);
        }

        // Obtener la fecha de la próxima clase (asumiendo que es hoy o la fecha del schedule)
        $date = now()->format('Y-m-d');
        
        $availableTeachers = $this->substituteService->findAvailableSubstitutes(
            $date,
            substr($schedule->start_time, 0, 5),
            substr($schedule->end_time, 0, 5),
            $schedule->teacher_id
        );

        return response()->json([
            'success' => true,
            'cancellation' => $cancellation,
            'schedule' => $schedule,
            'available_substitutes' => $availableTeachers,
            'count' => $availableTeachers->count(),
        ]);
    }

    /**
     * Buscar suplente para una incidencia específica
     * 
     * GET /api/substitutes/for-incident/{incidentId}
     */
    public function forIncident($incidentId)
    {
        $incident = Incident::with('room')->findOrFail($incidentId);

        if ($incident->type !== 'Ausencia Docente') {
            return response()->json([
                'success' => false,
                'message' => 'Esta incidencia no es de tipo "Ausencia Docente"',
            ], 400);
        }

        // Buscar el horario actual del docente
        $teacherId = $incident->teacher_id;
        $currentTime = now()->format('H:i');
        $dayOfWeek = now()->dayOfWeek;

        $schedule = Schedule::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró un horario activo para este docente',
            ], 404);
        }

        $availableTeachers = $this->substituteService->findAvailableSubstitutes(
            now()->format('Y-m-d'),
            substr($schedule->start_time, 0, 5),
            substr($schedule->end_time, 0, 5),
            $teacherId
        );

        return response()->json([
            'success' => true,
            'incident' => $incident,
            'schedule' => $schedule,
            'available_substitutes' => $availableTeachers,
            'count' => $availableTeachers->count(),
        ]);
    }
}
