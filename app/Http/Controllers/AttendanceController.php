<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Schedule;
use Illuminate\Support\Facades\Cache;

class AttendanceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/attendances",
     *     summary="CU17 - Listar asistencias",
     *     tags={"Asistencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="teacher_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Response(response=200, description="Lista de asistencias")
     * )
     */
    public function index(Request $request)
    {
        $query = Attendance::query();
        if ($request->has('teacher_id')) $query->where('teacher_id', $request->query('teacher_id'));
        if ($request->has('date')) $query->where('date', $request->query('date'));
        $user = $request->user();
        // If not admin and requesting all, restrict to own teacher records
        if (! $user->hasRole('administrador')) {
            if ($user->hasRole('docente')) {
                $teacher = Teacher::where('email', $user->email)->first();
                if ($teacher) {
                    $query->where('teacher_id', $teacher->id);
                }
            }
        }

        return response()->json($query->with(['teacher','schedule'])->orderBy('date','desc')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/attendances",
     *     summary="CU17 - Registrar asistencia manual",
     *     tags={"Asistencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"teacher_id", "date", "status"},
     *         @OA\Property(property="teacher_id", type="integer"),
     *         @OA\Property(property="schedule_id", type="integer", nullable=true),
     *         @OA\Property(property="date", type="string", format="date"),
     *         @OA\Property(property="time", type="string", example="08:00"),
     *         @OA\Property(property="status", type="string", enum={"present", "absent", "late"}),
     *         @OA\Property(property="notes", type="string", nullable=true)
     *     )),
     *     @OA\Response(response=201, description="Asistencia creada"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => 'required|integer|exists:teachers,id',
            'schedule_id' => 'nullable|integer|exists:schedules,id',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'status' => 'required|string|in:present,absent,late',
            'notes' => 'nullable|string',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'integer|exists:syllabus_topics,id',
        ]);

        $user = $request->user();
        $isAdmin = $user->hasRole('administrador');

        if (! $isAdmin) {
            // teacher can only record for themselves (match by email)
            $teacher = Teacher::where('email', $user->email)->first();
            if (! $teacher || $teacher->id != $data['teacher_id']) {
                return response()->json(['message' => 'Forbidden - cannot record attendance for another teacher'], 403);
            }
        }

        $data['recorded_by'] = Auth::id();

        $topicIds = $data['topic_ids'] ?? [];
        unset($data['topic_ids']);

        $attendance = Attendance::create($data);
        
        // Sincronizar los temas vistos en esta clase
        if (!empty($topicIds)) {
            $attendance->topics()->sync($topicIds);
        }

        return response()->json($attendance->load('topics'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/attendances/{id}",
     *     summary="Ver asistencia por ID",
     *     tags={"Asistencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Asistencia encontrada")
     * )
     */
    public function show($id)
    {
        $att = Attendance::with(['teacher','schedule'])->findOrFail($id);
        return response()->json($att);
    }

    /**
     * @OA\Patch(
     *     path="/api/attendances/{id}",
     *     summary="Actualizar asistencia",
     *     tags={"Asistencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="status", type="string", enum={"present", "absent", "late"}),
     *         @OA\Property(property="notes", type="string", nullable=true),
     *         @OA\Property(property="time", type="string")
     *     )),
     *     @OA\Response(response=200, description="Asistencia actualizada")
     * )
     */
    public function update(Request $request, $id)
    {
        $att = Attendance::findOrFail($id);
        $data = $request->validate([
            'status' => 'nullable|string|in:present,absent,late',
            'notes' => 'nullable|string',
            'time' => 'nullable|date_format:H:i',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'integer|exists:syllabus_topics,id',
        ]);
        $user = $request->user();
        if (! $user->hasRole('administrador')) {
            // teacher can only update own attendance
            $teacher = Teacher::where('email', $user->email)->first();
            if (! $teacher || $teacher->id != $att->teacher_id) {
                return response()->json(['message' => 'Forbidden - cannot update this record'], 403);
            }
        }

        $topicIds = $data['topic_ids'] ?? null;
        unset($data['topic_ids']);

        $att->update($data);
        
        // Sincronizar los temas si se proporcionaron
        if ($topicIds !== null) {
            $att->topics()->sync($topicIds);
        }

        return response()->json($att->load('topics'));
    }

    /**
     * @OA\Delete(
     *     path="/api/attendances/{id}",
     *     summary="Eliminar asistencia",
     *     tags={"Asistencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Asistencia eliminada")
     * )
     */
    public function destroy($id)
    {
        $att = Attendance::findOrFail($id);
        $att->delete();
        return response()->json(['message'=>'Attendance deleted','id'=>(int)$id]);
    }

    /**
     * @OA\Post(
     *     path="/api/attendances/qr",
     *     summary="CU18 - Registrar asistencia mediante código QR",
     *     description="Registra asistencia validando un token QR firmado con HMAC-SHA256",
     *     tags={"Asistencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="qr_payload", type="string", description="Token firmado (payload.signature)"),
     *         @OA\Property(property="schedule_id", type="integer", description="ID del horario (legacy)")
     *     )),
     *     @OA\Response(response=201, description="Asistencia registrada"),
     *     @OA\Response(response=422, description="QR inválido o expirado"),
     *     @OA\Response(response=409, description="QR ya utilizado")
     * )
     * CU18 - Register attendance via QR by teacher (or admin).
     * Expected payload: { schedule_id: int, qr_payload?: string }
     */
    public function registerQr(Request $request)
    {
        $data = $request->validate([
            // Either a signed qr_payload is provided OR schedule_id is provided (legacy).
            'schedule_id' => 'nullable|integer|exists:schedules,id',
            'qr_payload' => 'nullable|string',
        ]);

        $user = $request->user();
        $isAdmin = $user->hasRole('administrador');

        // If a signed QR payload is provided, prefer it and validate signature & expiry
        $scheduleIdFromToken = null;
        if (! empty($data['qr_payload'])) {
            $qr = $data['qr_payload'];
            $parts = explode('.', $qr);
            if (count($parts) !== 2) {
                return response()->json(['message' => 'Invalid QR payload format'], 422);
            }
            [$payloadB64, $sig] = $parts;
            // restore standard base64
            $payloadJson = base64_decode(strtr($payloadB64, '-_', '+/'));
            $payload = json_decode($payloadJson, true);
            if (! is_array($payload) || empty($payload['schedule_id'])) {
                return response()->json(['message' => 'Invalid QR payload content'], 422);
            }
            $expected = hash_hmac('sha256', $payloadB64, env('QR_SECRET', 'CHANGE_ME'));
            if (! hash_equals($expected, $sig)) {
                return response()->json(['message' => 'Invalid QR signature'], 422);
            }
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return response()->json(['message' => 'QR expired'], 422);
            }

            // optional replay protection: cache the signature until expiry
            if (filter_var(env('QR_PREVENT_REPLAY', true), FILTER_VALIDATE_BOOLEAN)) {
                $cacheKey = 'qr_used_'.sha1($sig);
                if (Cache::get($cacheKey)) {
                    return response()->json(['message' => 'QR already used'], 409);
                }
                $ttl = max(30, (int) (($payload['exp'] ?? (time()+300)) - time()));
                Cache::put($cacheKey, true, $ttl);
            }

            $scheduleIdFromToken = (int) $payload['schedule_id'];
        }

        $schedule_id = $scheduleIdFromToken ?? $data['schedule_id'];
        if (empty($schedule_id)) {
            return response()->json(['message' => 'schedule_id missing'], 422);
        }

        // Resolve teacher: for non-admins, match by authenticated user's email
        if (! $isAdmin) {
            $teacher = Teacher::where('email', $user->email)->first();
            if (! $teacher) {
                return response()->json(['message' => 'Teacher profile not found for authenticated user'], 404);
            }
        } else {
            // admin may optionally supply teacher via schedule assignment
            $sched = Schedule::find($schedule_id);
            $teacher = $sched ? $sched->teacher : null;
            if (! $teacher) {
                return response()->json(['message' => 'No teacher linked to schedule; admin must provide teacher_id via attendance endpoint instead'], 422);
            }
        }

        $attendance = Attendance::create([
            'teacher_id' => $teacher->id,
            'schedule_id' => $schedule_id,
            'date' => date('Y-m-d'),
            'time' => date('H:i'),
            'status' => 'present',
            'notes' => 'QR: '.($data['qr_payload'] ?? ''),
            'recorded_by' => Auth::id(),
        ]);

        return response()->json($attendance, 201);
    }
}
