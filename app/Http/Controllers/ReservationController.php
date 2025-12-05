<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Resource;
use App\Models\ClassCancellation;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reservations/available",
     *     summary="CU22 - Consultar aulas liberadas por anulación",
     *     tags={"Reservas"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="start_time", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="end_time", in="query", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Lista de aulas liberadas por anulaciones")
     * )
     */
    public function available(Request $request)
    {
        // Teachers and admins can query
        $user = $request->user();
        if (! $user || (! $user->hasRole('DOCENTE') && ! $user->hasRole('ADMIN') && ! $user->hasRole('docente') && ! $user->hasRole('administrador'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'date' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        // Find cancellations for the given date/time (if provided), otherwise return recent cancellations
        $query = ClassCancellation::query();
        if (! empty($data['date'])) {
            $query->whereHas('schedule', function($q) use ($data) {
                $q->where('date', $data['date']);
            });
        }
        $cancellations = $query->with('schedule.room')->get();

        $rooms = [];
        foreach ($cancellations as $c) {
            $room = $c->schedule->room ?? null;
            if ($room) {
                $rooms[$room->id] = $room;
            }
        }

        return response()->json(array_values($rooms));
    }

    /**
     * @OA\Post(
     *     path="/api/reservations",
     *     summary="CU22 - Reservar aula liberada por anulación",
     *     tags={"Reservas"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"room_id","reserved_at","expires_at"},
     *         @OA\Property(property="room_id", type="integer"),
     *         @OA\Property(property="schedule_id", type="integer", nullable=true),
     *         @OA\Property(property="reserved_at", type="string", format="date-time"),
     *         @OA\Property(property="expires_at", type="string", format="date-time"),
     *         @OA\Property(property="notes", type="string", nullable=true)
     *     )),
     *     @OA\Response(response=201, description="Reserva creada"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user || (! $user->hasRole('DOCENTE') && ! $user->hasRole('ADMIN') && ! $user->hasRole('docente') && ! $user->hasRole('administrador'))) {
            return response()->json(['message' => 'Forbidden - Requiere rol ADMIN o DOCENTE'], 403);
        }

        $data = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id',
            'schedule_id' => 'nullable|integer|exists:schedules,id',
            'reserved_at' => 'required|date_format:Y-m-d H:i:s',
            'expires_at' => 'required|date_format:Y-m-d H:i:s',
            'notes' => 'nullable|string',
            'resources' => 'nullable|array', // Array de recursos a asignar
            'resources.*.id' => 'required|integer|exists:resources,id',
            'resources.*.quantity' => 'nullable|integer|min:1',
            'resources.*.notes' => 'nullable|string',
        ]);

        // Validar que el aula no esté ocupada
        $roomConflict = Reservation::where('room_id', $data['room_id'])
            ->where(function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->whereBetween('reserved_at', [$data['reserved_at'], $data['expires_at']]);
                })->orWhere(function ($q) use ($data) {
                    $q->whereBetween('expires_at', [$data['reserved_at'], $data['expires_at']]);
                })->orWhere(function ($q) use ($data) {
                    $q->where('reserved_at', '<=', $data['reserved_at'])
                      ->where('expires_at', '>=', $data['expires_at']);
                });
            })
            ->exists();

        if ($roomConflict) {
            return response()->json([
                'message' => 'El aula ya está reservada en ese horario',
                'error' => 'room_conflict'
            ], 422);
        }

        // Validar que los recursos no estén ocupados
        if (!empty($data['resources'])) {
            $unavailableResources = [];
            
            foreach ($data['resources'] as $resourceData) {
                $resource = Resource::find($resourceData['id']);
                
                if (!$resource) {
                    continue;
                }

                if (!$resource->isAvailableAt($data['reserved_at'], $data['expires_at'])) {
                    $unavailableResources[] = [
                        'id' => $resource->id,
                        'name' => $resource->name,
                        'type' => $resource->type,
                        'status' => $resource->status
                    ];
                }
            }

            if (!empty($unavailableResources)) {
                return response()->json([
                    'message' => 'Algunos recursos no están disponibles en ese horario',
                    'error' => 'resource_conflict',
                    'unavailable_resources' => $unavailableResources
                ], 422);
            }
        }

        // Crear la reserva con transacción
        DB::beginTransaction();
        try {
            // Obtener teacher_id
            $teacherId = null;
            if ($user->hasRole('DOCENTE') || $user->hasRole('docente')) {
                $teacher = \App\Models\Teacher::where('email', $user->email)->first();
                $teacherId = $teacher ? $teacher->id : null;
            }
            
            $reservation = Reservation::create([
                'room_id' => $data['room_id'],
                'schedule_id' => $data['schedule_id'] ?? null,
                'teacher_id' => $teacherId,
                'reserved_at' => $data['reserved_at'],
                'expires_at' => $data['expires_at'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Asignar recursos a la reserva
            if (!empty($data['resources'])) {
                foreach ($data['resources'] as $resourceData) {
                    $reservation->resources()->attach($resourceData['id'], [
                        'quantity' => $resourceData['quantity'] ?? 1,
                        'notes' => $resourceData['notes'] ?? null,
                    ]);

                    // Actualizar estado del recurso a "En Uso"
                    Resource::where('id', $resourceData['id'])->update(['status' => 'En Uso']);
                }
            }

            DB::commit();

            // Cargar relaciones para la respuesta
            $reservation->load('resources', 'room', 'teacher');

            return response()->json($reservation, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/reservations",
     *     summary="Listar reservas del usuario (docente) o todas (admin)",
     *     tags={"Reservas"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Response(response=200, description="Lista de reservas")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) return response()->json(['message' => 'Forbidden'], 403);

        if ($user->hasRole('ADMIN') || $user->hasRole('administrador')) {
            $res = Reservation::with(['room','teacher'])->orderBy('created_at','desc')->get();
        } else {
            $teacher = \App\Models\Teacher::where('email',$user->email)->first();
            $res = $teacher ? Reservation::with('room')->where('teacher_id',$teacher->id)->get() : [];
        }

        return response()->json($res);
    }
}
