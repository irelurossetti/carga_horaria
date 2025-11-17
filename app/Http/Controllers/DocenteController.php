<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\ClassCancellation;
use Illuminate\Support\Facades\Log; // Importamos Log para depurar si sigue fallando

class DocenteController extends Controller
{
    public function dashboard()
    {
        try {
            $user = Auth::user();
            
            // 1. Verificar la relación con el docente
            $teacher = $user->teacher; // Usamos la relación definida en User.php

            if (!$teacher) {
                // Si el usuario no tiene un perfil de docente, lo sacamos.
                if ($user->hasRole('ADMIN')) {
                    return redirect()->route('dashboard')->with('status', 'Logueado como Admin (sin perfil docente).');
                }
                
                Auth::logout();
                return redirect('/login')->with('error', 'No se encontró un perfil de docente asociado a este usuario.');
            }

            // Por ahora, sin horarios, mostramos una vista vacía
            $schedulesToday = collect([]);
            $currentClass = null;
            $upcomingClasses = collect([]);
            
            // 5. ¡Finalmente, cargar la vista!
            return view('docente.dashboard', compact('schedulesToday', 'currentClass', 'upcomingClasses'));
        
        } catch (\Exception $e) {
            // Si algo MÁS falla (ej. la consulta 'assignment.group.subject'),
            // lo veremos en el log.
            Log::error("Error en DocenteController@dashboard: " . $e->getMessage());
            // Mostramos el error en pantalla
            return response("Error 500 en el controlador de docente: " . $e->getMessage(), 500);
        }
    }

    /**
     * Acción para el botón "Marcar Asistencia"
     */
    public function marcarAsistencia(Request $request, Schedule $schedule)
    {
        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return back()->with('error', 'No eres un docente válido.');
        }

        $exists = Attendance::where('schedule_id', $schedule->id)
            ->where('teacher_id', $teacher->id)
            ->whereDate('attendance_time', Carbon::today())
            ->exists();

        if (!$exists) {
            Attendance::create([
                'schedule_id' => $schedule->id,
                'teacher_id' => $teacher->id,
                'status' => 'presente',
                'attendance_time' => now(),
            ]);
        }

        return back()->with('status', '¡Asistencia marcada correctamente!');
    }

    /**
     * Acción para el botón "Cambiar a Virtual"
     */
    public function cambiarVirtual(Request $request, Schedule $schedule)
    {
        $exists = ClassCancellation::where('schedule_id', $schedule->id)
            ->whereDate('cancelled_at', Carbon::today())
            ->exists();

        if (!$exists) {
            ClassCancellation::create([
                'schedule_id' => $schedule->id,
                'cancellation_type' => 'virtual',
                'reason' => 'Solicitud del docente (desde dashboard)',
                'cancelled_at' => now(),
            ]);
        }

        return back()->with('status', 'La clase se ha cambiado a modalidad virtual.');
    }

    /**
     * Vista de carga horaria con filtros (semanal, mensual, semestral)
     */
    public function cargaHoraria(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('dashboard')->with('error', 'No se encontró un perfil de docente.');
        }

        return view('docente.workload', compact('teacher'));
    }

    /**
     * API para obtener la carga horaria filtrada
     */
    public function getCargaHoraria(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['error' => 'No se encontró un perfil de docente'], 404);
        }

        $period = $request->input('period', 'week'); // week, month, semester
        $teacherId = $request->input('teacher_id', $teacher->id);

        // Si es admin o coordinador, puede ver otros docentes
        if ($request->has('teacher_id') && $teacherId != $teacher->id) {
            if (!$user->hasRole('administrador') && !$user->hasRole('coordinador')) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
        }

        $query = Schedule::where('teacher_id', $teacherId)
            ->with(['group.subject', 'group', 'room']);

        // Filtrar por periodo
        $now = Carbon::now();
        switch ($period) {
            case 'week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            case 'semester':
                // Asumimos semestre de 6 meses
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->addMonths(6)->endOfMonth();
                break;
            default:
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
        }

        $schedules = $query->get();

        // Calcular estadísticas
        $totalHours = 0;
        $schedulesByDay = [];
        $schedulesBySubject = [];

        foreach ($schedules as $schedule) {
            if ($schedule->start_time && $schedule->end_time) {
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);
                // Usar abs() para evitar valores negativos
                $hours = abs($end->diffInHours($start, false));
                $totalHours += $hours;

                // Agrupar por día
                $day = $schedule->day_of_week ?? 'Sin día';
                if (!isset($schedulesByDay[$day])) {
                    $schedulesByDay[$day] = 0;
                }
                $schedulesByDay[$day] += $hours;

                // Agrupar por materia
                $subject = ($schedule->group && $schedule->group->subject) ? $schedule->group->subject->name : 'Sin materia';
                if (!isset($schedulesBySubject[$subject])) {
                    $schedulesBySubject[$subject] = 0;
                }
                $schedulesBySubject[$subject] += $hours;
            }
        }

        // Calcular horas por semana/mes/semestre
        $hoursPerPeriod = $totalHours;
        if ($period === 'month') {
            $hoursPerPeriod = $totalHours * 4; // Aproximado 4 semanas
        } elseif ($period === 'semester') {
            $hoursPerPeriod = $totalHours * 24; // Aproximado 24 semanas
        }

        return response()->json([
            'schedules' => $schedules,
            'stats' => [
                'total_hours_week' => $totalHours,
                'total_hours_period' => $hoursPerPeriod,
                'total_classes' => $schedules->count(),
                'schedules_by_day' => $schedulesByDay,
                'schedules_by_subject' => $schedulesBySubject,
                'period' => $period,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Exportar carga horaria a PDF
     */
    public function exportWorkloadPDF(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['error' => 'No se encontró un perfil de docente'], 404);
        }

        $period = $request->input('period', 'week');
        $teacherId = $request->input('teacher_id', $teacher->id);

        // Si es admin o coordinador, puede ver otros docentes
        if ($request->has('teacher_id') && $teacherId != $teacher->id) {
            if (!$user->hasRole('administrador') && !$user->hasRole('coordinador')) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            $teacher = \App\Models\Teacher::find($teacherId);
        }

        $schedules = Schedule::where('teacher_id', $teacherId)
            ->with(['group.subject', 'group', 'room'])
            ->get();

        $totalHours = 0;
        $schedulesByDay = [];
        $schedulesBySubject = [];
        
        foreach ($schedules as $schedule) {
            if ($schedule->start_time && $schedule->end_time) {
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);
                $hours = abs($end->diffInHours($start, false));
                $totalHours += $hours;
                
                // Agrupar por día
                $day = $schedule->day_of_week ?? 'Sin día';
                if (!isset($schedulesByDay[$day])) {
                    $schedulesByDay[$day] = 0;
                }
                $schedulesByDay[$day] += $hours;
                
                // Agrupar por materia
                $subject = ($schedule->group && $schedule->group->subject) ? $schedule->group->subject->name : 'Sin materia';
                if (!isset($schedulesBySubject[$subject])) {
                    $schedulesBySubject[$subject] = 0;
                }
                $schedulesBySubject[$subject] += $hours;
            }
        }

        $periodLabels = [
            'week' => 'Semanal',
            'month' => 'Mensual',
            'semester' => 'Semestral'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.workload-pdf', [
            'teacher' => $teacher,
            'schedules' => $schedules,
            'period' => $periodLabels[$period] ?? 'Semanal',
            'totalHours' => $totalHours,
            'schedulesByDay' => $schedulesByDay,
            'schedulesBySubject' => $schedulesBySubject,
            'date' => Carbon::now()->format('d/m/Y H:i')
        ]);

        return $pdf->download('carga_horaria_' . str_replace(' ', '_', $teacher->name) . '_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Exportar carga horaria a Excel
     */
    public function exportWorkloadExcel(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json(['error' => 'No se encontró un perfil de docente'], 404);
        }

        $period = $request->input('period', 'week');
        $teacherId = $request->input('teacher_id', $teacher->id);

        // Si es admin o coordinador, puede ver otros docentes
        if ($request->has('teacher_id') && $teacherId != $teacher->id) {
            if (!$user->hasRole('administrador') && !$user->hasRole('coordinador')) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
            $teacher = \App\Models\Teacher::find($teacherId);
        }

        $schedules = Schedule::where('teacher_id', $teacherId)
            ->with(['group.subject', 'group', 'room'])
            ->get();

        $totalHours = 0;
        foreach ($schedules as $schedule) {
            if ($schedule->start_time && $schedule->end_time) {
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);
                $totalHours += abs($end->diffInHours($start, false));
            }
        }

        $periodLabels = [
            'week' => 'Semanal',
            'month' => 'Mensual',
            'semester' => 'Semestral'
        ];

        // Crear Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Título
        $sheet->setCellValue('A1', 'Carga Horaria - ' . $teacher->name);
        $sheet->setCellValue('A2', 'Período: ' . ($periodLabels[$period] ?? 'Semanal'));
        $sheet->setCellValue('A3', 'Fecha: ' . Carbon::now()->format('d/m/Y H:i'));
        $sheet->setCellValue('A4', 'Total Horas: ' . $totalHours);
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:A4')->getFont()->setItalic(true);
        
        // Encabezados de tabla
        $sheet->setCellValue('A6', 'Día');
        $sheet->setCellValue('B6', 'Hora Inicio');
        $sheet->setCellValue('C6', 'Hora Fin');
        $sheet->setCellValue('D6', 'Materia');
        $sheet->setCellValue('E6', 'Grupo');
        $sheet->setCellValue('F6', 'Aula');
        $sheet->setCellValue('G6', 'Horas');
        
        $sheet->getStyle('A6:G6')->getFont()->setBold(true);
        $sheet->getStyle('A6:G6')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FF881F34');
        $sheet->getStyle('A6:G6')->getFont()->getColor()->setARGB('FFFFFFFF');
        
        // Datos
        $row = 7;
        foreach ($schedules as $schedule) {
            $start = $schedule->start_time ? substr($schedule->start_time, 0, 5) : '';
            $end = $schedule->end_time ? substr($schedule->end_time, 0, 5) : '';
            $hours = 0;
            
            if ($schedule->start_time && $schedule->end_time) {
                $startTime = Carbon::parse($schedule->start_time);
                $endTime = Carbon::parse($schedule->end_time);
                $hours = abs($endTime->diffInHours($startTime, false));
            }
            
            $sheet->setCellValue('A' . $row, $schedule->day_of_week ?? 'N/A');
            $sheet->setCellValue('B' . $row, $start);
            $sheet->setCellValue('C' . $row, $end);
            $sheet->setCellValue('D' . $row, ($schedule->group && $schedule->group->subject) ? $schedule->group->subject->name : 'N/A');
            $sheet->setCellValue('E' . $row, $schedule->group ? $schedule->group->name : 'N/A');
            $sheet->setCellValue('F' . $row, $schedule->room ? $schedule->room->name : 'N/A');
            $sheet->setCellValue('G' . $row, $hours . 'h');
            
            $row++;
        }
        
        // Ajustar anchos de columna
        foreach(range('A','G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'carga_horaria_' . str_replace(' ', '_', $teacher->name) . '_' . date('Y-m-d') . '.xlsx';
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Vista de asistencia con QR
     */
    public function attendanceQR()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('dashboard')->with('error', 'No se encontró un perfil de docente.');
        }

        // Obtener los horarios del docente con las relaciones correctas
        $schedules = Schedule::with(['group.subject', 'group', 'room'])
            ->where('teacher_id', $teacher->id)
            ->get()
            ->map(function($schedule) {
                $subject = $schedule->group && $schedule->group->subject ? $schedule->group->subject->name : 'Sin materia';
                
                return [
                    'id' => $schedule->id,
                    'subject_name' => $subject,
                    'group_name' => $schedule->group ? $schedule->group->name : 'Sin grupo',
                    'day_of_week' => $schedule->day_of_week,
                    'start_time' => $schedule->start_time ? substr($schedule->start_time, 0, 5) : '00:00',
                    'end_time' => $schedule->end_time ? substr($schedule->end_time, 0, 5) : '00:00',
                    'room_name' => $schedule->room ? $schedule->room->name : 'Sin aula'
                ];
            });

        return view('docente.attendance-qr', compact('schedules'));
    }
}