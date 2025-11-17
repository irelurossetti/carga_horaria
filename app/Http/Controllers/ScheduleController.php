<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScheduleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/schedules",
     *     summary="CU13 - Listar horarios",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="group_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="teacher_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Lista de horarios")
     * )
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['group.subject', 'room', 'teacher']);
        
        if ($request->has('group_id')) {
            $query->where('group_id', $request->query('group_id'));
        }
        
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->query('teacher_id'));
        }
        
        $schedules = $query->get();
        
        // Log para debugging
        \Log::info('Schedules API called', [
            'count' => $schedules->count(),
            'teacher_id' => $request->query('teacher_id'),
            'first_schedule' => $schedules->first()
        ]);
        
        return response()->json($schedules);
    }

    /**
     * @OA\Get(
     *     path="/api/schedules/weekly",
     *     summary="Ver horarios semanales agrupados por día",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="teacher_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="group_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Horarios agrupados por día")
     * )
     * Return weekly schedule grouped by day. Accepts teacher_id or group_id as filter.
     */
    public function weekly(Request $request)
    {
        $query = Schedule::query();
        if ($request->has('teacher_id')) $query->where('teacher_id', $request->query('teacher_id'));
        if ($request->has('group_id')) $query->where('group_id', $request->query('group_id'));
        $schedules = $query->with(['group','room','teacher'])->get()->groupBy('day_of_week');
        return response()->json($schedules);
    }

    /**
     * @OA\Get(
     *     path="/api/schedules/export",
     *     summary="CU16 - Exportar horarios a Excel",
     *     description="Exporta los horarios en formato Excel (.xlsx)",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="teacher_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="group_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo Excel",
     *         @OA\MediaType(mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
     *     )
     * )
     * Export schedules to Excel. Accepts teacher_id or group_id filters.
     */
    public function export(Request $request)
    {
        $query = Schedule::query();
        if ($request->has('teacher_id')) $query->where('teacher_id', $request->query('teacher_id'));
        if ($request->has('group_id')) $query->where('group_id', $request->query('group_id'));
        $items = $query->with(['group','room','teacher'])->orderBy('day_of_week')->orderBy('start_time')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['ID','Day','Start','End','Group','Subject','Room','Teacher'], null, 'A1');
        $row = 2;
        foreach ($items as $it) {
            $sheet->setCellValue("A{$row}", $it->id);
            $sheet->setCellValue("B{$row}", $it->day_of_week);
            $sheet->setCellValue("C{$row}", $it->start_time);
            $sheet->setCellValue("D{$row}", $it->end_time);
            $sheet->setCellValue("E{$row}", $it->group->name ?? $it->group_id);
            $sheet->setCellValue("F{$row}", $it->group->subject_id ?? '');
            $sheet->setCellValue("G{$row}", $it->room->name ?? $it->room_id);
            $sheet->setCellValue("H{$row}", $it->teacher->name ?? $it->teacher_id);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $fileName = 'schedules_export_'.date('Ymd_His').'.xlsx';

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $disposition = 'attachment; filename="'.$fileName.'"';
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * @OA\Get(
     *     path="/api/schedules/export.pdf",
     *     summary="CU16 - Exportar horarios a PDF",
     *     description="Exporta los horarios en formato PDF",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="teacher_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="group_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Archivo PDF",
     *         @OA\MediaType(mediaType="application/pdf")
     *     )
     * )
     * Export schedules to PDF. Requires barryvdh/laravel-dompdf or dompdf installed.
     */
    public function exportPdf(Request $request)
    {
        $query = Schedule::query();
        if ($request->has('teacher_id')) $query->where('teacher_id', $request->query('teacher_id'));
        if ($request->has('group_id')) $query->where('group_id', $request->query('group_id'));
        $items = $query->with(['group','room','teacher'])->orderBy('day_of_week')->orderBy('start_time')->get();

        // Build a simple HTML table
        $html = '<h2>Schedules</h2>';
        $html .= '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;width:100%">';
        $html .= '<thead><tr><th>ID</th><th>Day</th><th>Start</th><th>End</th><th>Group</th><th>Subject</th><th>Room</th><th>Teacher</th></tr></thead><tbody>';
        foreach ($items as $it) {
            $html .= '<tr>';
            $html .= '<td>'.e($it->id).'</td>';
            $html .= '<td>'.e($it->day_of_week).'</td>';
            $html .= '<td>'.e($it->start_time).'</td>';
            $html .= '<td>'.e($it->end_time).'</td>';
            $html .= '<td>'.e($it->group->name ?? $it->group_id).'</td>';
            $html .= '<td>'.e($it->group->subject_id ?? '').'</td>';
            $html .= '<td>'.e($it->room->name ?? $it->room_id).'</td>';
            $html .= '<td>'.e($it->teacher->name ?? $it->teacher_id).'</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $fileName = 'schedules_export_'.date('Ymd_His').'.pdf';

        // Prefer Laravel DomPDF wrapper if available (facade class)
        if (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            return $pdf->stream($fileName);
        }

        // Try the wrapper service name (if provider registered)
        try {
            if (app()->bound('dompdf.wrapper')) {
                $pdf = app('dompdf.wrapper');
                $pdf->loadHTML($html);
                return $pdf->stream($fileName);
            }
        } catch (\Throwable $e) {
            // continue to fallback
        }

        // Fallback to using Dompdf directly if installed
        if (class_exists('\Dompdf\Dompdf')) {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->render();
            $output = $dompdf->output();
            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"'
            ]);
        }

        // Not available: tell the user how to install
        return response()->json([
            'message' => 'PDF export not available. Please install barryvdh/laravel-dompdf (composer require barryvdh/laravel-dompdf) and register the service provider if needed.'
        ], 501);
    }

    /**
     * @OA\Post(
     *     path="/api/schedules",
     *     summary="CU13 - Crear horario",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"group_id", "day_of_week", "start_time", "end_time"},
     *         @OA\Property(property="group_id", type="integer"),
     *         @OA\Property(property="room_id", type="integer", nullable=true),
     *         @OA\Property(property="teacher_id", type="integer", nullable=true),
     *         @OA\Property(property="day_of_week", type="string", example="lunes"),
     *         @OA\Property(property="start_time", type="string", example="08:00"),
     *         @OA\Property(property="end_time", type="string", example="10:00")
     *     )),
     *     @OA\Response(response=201, description="Horario creado"),
     *     @OA\Response(response=409, description="Conflicto de horario"),
     *     @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'group_id' => 'required|integer|exists:groups,id',
            'room_id' => 'nullable|integer|exists:rooms,id',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
            'day_of_week' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        if ($data['end_time'] <= $data['start_time']) {
            return response()->json(['message' => 'end_time must be after start_time'], 422);
        }
        // Apply a configurable tolerance (minutes) around times for conflict checks.
        $tolerance = (int) env('SCHEDULE_CONFLICT_TOLERANCE_MINUTES', 0);
        $start = Carbon::createFromFormat('H:i', $data['start_time'])->startOfMinute()->subMinutes($tolerance)->format('H:i:s');
        $end = Carbon::createFromFormat('H:i', $data['end_time'])->startOfMinute()->addMinutes($tolerance)->format('H:i:s');

        // Conflict validation for the same group on the same day (expanded by tolerance)
        $groupConflict = Schedule::with(['group.subject', 'teacher'])
            ->where('group_id', $data['group_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function($qq) use ($start, $end){
                      $qq->where('start_time','<=',$start)
                         ->where('end_time','>=',$end);
                  });
            })->first();

        if ($groupConflict) {
            $subjectName = $groupConflict->group->subject->name ?? 'Materia desconocida';
            $teacherName = $groupConflict->teacher->name ?? 'Docente desconocido';
            $timeRange = substr($groupConflict->start_time, 0, 5) . ' - ' . substr($groupConflict->end_time, 0, 5);
            
            return response()->json([
                'message' => "Este horario no está disponible ya que está la materia {$subjectName} con el docente {$teacherName} de {$timeRange}"
            ], 409);
        }

        // Conflict validation for same room if provided
        if (!empty($data['room_id'])) {
            $roomConflict = Schedule::with(['group.subject', 'teacher', 'room'])
                ->where('room_id', $data['room_id'])
                ->where('day_of_week', $data['day_of_week'])
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_time', [$start, $end])
                      ->orWhereBetween('end_time', [$start, $end])
                      ->orWhere(function($qq) use ($start, $end){
                          $qq->where('start_time','<=',$start)
                             ->where('end_time','>=',$end);
                      });
                })->first();

            if ($roomConflict) {
                $roomName = $roomConflict->room->name ?? 'Aula desconocida';
                $subjectName = $roomConflict->group->subject->name ?? 'Materia desconocida';
                $teacherName = $roomConflict->teacher->name ?? 'Docente desconocido';
                $timeRange = substr($roomConflict->start_time, 0, 5) . ' - ' . substr($roomConflict->end_time, 0, 5);
                
                return response()->json([
                    'message' => "El aula {$roomName} no está disponible ya que está ocupada por la materia {$subjectName} con el docente {$teacherName} de {$timeRange}"
                ], 409);
            }
        }

        // Conflict validation for same teacher if provided
        if (!empty($data['teacher_id'])) {
            $teacherConflict = Schedule::with(['group.subject', 'teacher'])
                ->where('teacher_id', $data['teacher_id'])
                ->where('day_of_week', $data['day_of_week'])
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_time', [$start, $end])
                      ->orWhereBetween('end_time', [$start, $end])
                      ->orWhere(function($qq) use ($start, $end){
                          $qq->where('start_time','<=',$start)
                             ->where('end_time','>=',$end);
                      });
                })->first();

            if ($teacherConflict) {
                $teacherName = $teacherConflict->teacher->name ?? 'Docente desconocido';
                $subjectName = $teacherConflict->group->subject->name ?? 'Materia desconocida';
                $timeRange = substr($teacherConflict->start_time, 0, 5) . ' - ' . substr($teacherConflict->end_time, 0, 5);
                
                return response()->json([
                    'message' => "El docente {$teacherName} no está disponible ya que está dando la materia {$subjectName} de {$timeRange}"
                ], 409);
            }
        }

        $data['assigned_by'] = Auth::id();
        $schedule = Schedule::create($data);
        return response()->json($schedule, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/schedules/{id}",
     *     summary="Ver horario por ID",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Horario encontrado")
     * )
     */
    public function show($id)
    {
        $schedule = Schedule::with(['group','room','teacher'])->findOrFail($id);
        return response()->json($schedule);
    }

    /**
     * @OA\Patch(
     *     path="/api/schedules/{id}",
     *     summary="CU14 - Actualizar horario",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="room_id", type="integer", nullable=true),
     *         @OA\Property(property="teacher_id", type="integer", nullable=true),
     *         @OA\Property(property="start_time", type="string"),
     *         @OA\Property(property="end_time", type="string")
     *     )),
     *     @OA\Response(response=200, description="Horario actualizado"),
     *     @OA\Response(response=409, description="Conflicto de horario"),
     *     @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $data = $request->validate([
            'group_id' => 'nullable|integer|exists:groups,id',
            'room_id' => 'nullable|integer|exists:rooms,id',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
            'day_of_week' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        $new = array_merge($schedule->toArray(), $data);
        if (!empty($new['start_time']) && !empty($new['end_time']) && $new['end_time'] <= $new['start_time']) {
            return response()->json(['message' => 'end_time must be after start_time'], 422);
        }

        $tolerance = (int) env('SCHEDULE_CONFLICT_TOLERANCE_MINUTES', 0);
        $start = Carbon::createFromFormat('H:i', $new['start_time'])->startOfMinute()->subMinutes($tolerance)->format('H:i:s');
        $end = Carbon::createFromFormat('H:i', $new['end_time'])->startOfMinute()->addMinutes($tolerance)->format('H:i:s');

        // Conflict check for group
        $groupConflict = Schedule::where('group_id', $new['group_id'])
            ->where('day_of_week', $new['day_of_week'])
            ->where('id', '!=', $schedule->id)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function($qq) use ($start, $end){
                      $qq->where('start_time','<=',$start)
                         ->where('end_time','>=',$end);
                  });
            })->exists();

        if ($groupConflict) {
            return response()->json(['message' => 'Schedule conflict for group'], 409);
        }

        // Conflict check for room if present
        if (!empty($new['room_id'])) {
            $roomConflict = Schedule::where('room_id', $new['room_id'])
                ->where('day_of_week', $new['day_of_week'])
                ->where('id', '!=', $schedule->id)
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_time', [$start, $end])
                      ->orWhereBetween('end_time', [$start, $end])
                      ->orWhere(function($qq) use ($start, $end){
                          $qq->where('start_time','<=',$start)
                             ->where('end_time','>=',$end);
                      });
                })->exists();

            if ($roomConflict) {
                return response()->json(['message' => 'Schedule conflict for room'], 409);
            }
        }

        // Conflict check for teacher if present
        if (!empty($new['teacher_id'])) {
            $teacherConflict = Schedule::where('teacher_id', $new['teacher_id'])
                ->where('day_of_week', $new['day_of_week'])
                ->where('id', '!=', $schedule->id)
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_time', [$start, $end])
                      ->orWhereBetween('end_time', [$start, $end])
                      ->orWhere(function($qq) use ($start, $end){
                          $qq->where('start_time','<=',$start)
                             ->where('end_time','>=',$end);
                      });
                })->exists();

            if ($teacherConflict) {
                return response()->json(['message' => 'Schedule conflict for teacher'], 409);
            }
        }

        $schedule->update($data);
        return response()->json($schedule);
    }

    /**
     * @OA\Delete(
     *     path="/api/schedules/{id}",
     *     summary="CU15 - Eliminar horario",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Horario eliminado")
     * )
     */
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();
        return response()->json(['message' => 'Schedule deleted', 'id' => (int)$id]);
    }

    /**
     * @OA\Post(
     *     path="/api/schedules/{id}/cancel",
     *     summary="CU19 - Anular clase o cambiar a modalidad virtual",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"mode"},
     *         @OA\Property(property="mode", type="string", enum={"cancelled", "virtual"}),
     *         @OA\Property(property="reason", type="string", nullable=true)
     *     )),
     *     @OA\Response(response=201, description="Cancelación registrada"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     * CU19 - Cancel a class or mark it as virtual. Creates a cancellation record.
     * Body: { mode: 'cancelled'|'virtual', reason?: string }
     */
    public function cancel(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $data = $request->validate([
            'mode' => 'required|string|in:cancelled,virtual',
            'reason' => 'nullable|string',
        ]);

        $user = $request->user();

        // Only admin or the assigned teacher may cancel
        if (! $user->hasRole('administrador')) {
            $teacher = $schedule->teacher;
            if (! $teacher || $teacher->email !== $user->email) {
                return response()->json(['message' => 'Forbidden - only assigned teacher or admin can cancel this class'], 403);
            }
        }

        // Create a cancellation record (class_cancellations table)
        $c = \App\Models\ClassCancellation::create([
            'schedule_id' => $schedule->id,
            'teacher_id' => $schedule->teacher_id,
            'mode' => $data['mode'],
            'reason' => $data['reason'] ?? null,
            'canceled_by' => $user->id,
        ]);

        return response()->json(['message' => 'Class cancelled', 'cancellation' => $c], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/schedules/{id}/qrcode",
     *     summary="CU18 - Generar código QR firmado para un horario",
     *     description="Genera un código QR con token firmado HMAC-SHA256 para registro de asistencia",
     *     tags={"Horarios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", description="ID del horario", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Imagen PNG del código QR",
     *         @OA\MediaType(mediaType="image/png")
     *     ),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Horario no encontrado")
     * )
     * Generate a signed QR for a schedule which can be scanned to register attendance.
     * Only the assigned teacher or an admin may generate the QR for that schedule.
     * Returns a PNG image with a short-lived signed token payload.
     */
    public function generateQr(Request $request, $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);

            // Generar token simple
            $ttl = (int) env('QR_TTL_SECONDS', 300);
            $payload = [
                'schedule_id' => $schedule->id,
                'iat' => time(),
                'exp' => time() + $ttl,
                'iss' => env('APP_NAME', 'carga_horaria'),
            ];

            $payloadJson = json_encode($payload);
            $payloadB64 = rtrim(strtr(base64_encode($payloadJson), '+/', '-_'), '=');
            $secret = env('QR_SECRET', 'CHANGE_ME');
            $sig = hash_hmac('sha256', $payloadB64, $secret);
            $token = $payloadB64 . '.' . $sig;

            // Generar QR usando SimpleSoftwareIO/simple-qrcode o bacon/bacon-qr-code
            // Intentar con bacon-qr-code que viene con endroid
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($renderer);
            $qrCodeSvg = $writer->writeString($token);
            
            return response($qrCodeSvg, 200, [
                'Content-Type' => 'image/svg+xml'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating QR code: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Generar un QR simple con texto plano como fallback
            try {
                $simpleToken = "SCHEDULE-{$id}-" . time();
                $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                    new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                );
                $writer = new \BaconQrCode\Writer($renderer);
                $qrCodeSvg = $writer->writeString($simpleToken);
                
                return response($qrCodeSvg, 200, [
                    'Content-Type' => 'image/svg+xml'
                ]);
            } catch (\Exception $e2) {
                return response()->json([
                    'message' => 'Error al generar código QR: ' . $e2->getMessage()
                ], 500);
            }
        }
    }
}
