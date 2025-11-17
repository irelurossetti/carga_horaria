<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;

// Controladores de Autenticación y Vistas
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\CoordinatorDashboardController;
use App\Http\Controllers\StudentDashboardController;

// Controladores API (Casos de Uso)
use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\TeacherAssignmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScheduleGeneratorController;
use App\Http\Controllers\ClassCancellationController;
use App\Http\Controllers\ConflictController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SystemParameterController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\IncidentController;

// ============================================================
// RUTAS PÚBLICAS
// ============================================================

Route::redirect('/', '/login');

Route::get('/welcome', function () {
    return view('welcome');
})->name('home');

// Rutas de autenticación (Públicas / Guest)
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [LoginController::class, 'authenticate'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Password Reset Routes
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request')->middleware('guest');

Route::get('/reset-password', function () {
    return view('auth.reset-password');
})->name('password.reset')->middleware('guest');

// ============================================================
// RUTAS SWAGGER Y OPENAPI (Públicas)
// ============================================================
Route::get('/swagger', function () {
    return redirect('/api/documentation');
})->name('swagger.ui');

Route::get('/openapi.yaml', function () {
    return response()->file(base_path('docs/openapi.yaml'));
});


// ============================================================
// RUTAS PROTEGIDAS (Requieren autenticación)
// ============================================================
Route::middleware(['auth'])->group(function () {

    // --- Configuración de usuario (Volt) - Disponible para todos los usuarios autenticados ---
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    // --- Dashboard principal con redirección según rol ---
    Route::get('/dashboard', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            Auth::logout();
            return redirect('/login')->with('error', 'Sesión inválida.');
        }

        // CARGAR EXPLÍCITAMENTE LOS ROLES
        $user->load('roles');
        
        // Verificar roles con carga explícita
        $roles = $user->roles->pluck('name')->toArray();
        
        if (in_array('ADMIN', $roles)) {
            return app(AdminDashboardController::class)->index();
        } elseif (in_array('COORDINADOR', $roles)) {
            return app(CoordinatorDashboardController::class)->index();
        } elseif (in_array('DOCENTE', $roles)) {
            return redirect()->route('docente.dashboard');
        } elseif (in_array('ESTUDIANTE', $roles)) {
            return app(StudentDashboardController::class)->index();
        } else {
            Auth::logout();
            return redirect('/login')->with('error', 'No tienes un rol asignado.');
        }
    })->name('dashboard');

    // ============================================================
    // RUTAS ADMINISTRADOR - Acceso completo a toda la gestión
    // ============================================================
    Route::middleware(['role:ADMIN'])->group(function () {
        // Vistas web de gestión
        Route::get('/periodos', [AcademicPeriodController::class, 'webIndex'])->name('periods.index');
        Route::get('/docentes', function() { return view('admin.teachers'); })->name('teachers.index');
        Route::get('/estudiantes', function() { return view('admin.students'); })->name('students.index');
        Route::get('/materias', function() { return view('admin.subjects'); })->name('subjects.index');
        Route::get('/grupos', function() { return view('admin.groups'); })->name('groups.index');
        Route::get('/aulas', function() { return view('admin.rooms'); })->name('rooms.index');
        Route::get('/importar', function() { return view('admin.imports'); })->name('imports.index');
        Route::get('/asignaciones', function() { return view('admin.assignments'); })->name('assignments.index');
        Route::get('/horarios', function() { return view('admin.schedules'); })->name('schedules.index');
        Route::get('/horario-semanal', function() { return view('admin.weekly-schedule'); })->name('weekly-schedule.index');
        Route::get('/asistencia', function() { return view('admin.attendance'); })->name('attendance.index');
        Route::get('/asistencia-qr', function() { return view('admin.attendance-qr'); })->name('attendance-qr.index');
        Route::get('/anulaciones', function() { return view('admin.cancellations'); })->name('cancellations.index');
        Route::get('/conflictos', function() { return view('admin.conflicts'); })->name('conflicts.index');
        Route::get('/aulas-disponibles', function() { return view('admin.available-rooms'); })->name('available-rooms.index');
        Route::get('/reservas', function() { return view('admin.room-reservations'); })->name('reservations.index');
        Route::get('/asistencia-docente', function() { return view('admin.attendance-by-teacher'); })->name('attendance-teacher.index');
        Route::get('/asistencia-grupo', function() { return view('admin.attendance-by-group'); })->name('attendance-group.index');
        Route::get('/carga-horaria', function() { return view('admin.workload'); })->name('workload.index');
        Route::get('/anuncios', function() { return view('admin.announcements'); })->name('announcements.index');
        Route::get('/incidencias', function() { return view('admin.incidents'); })->name('incidents.index');
        Route::get('/reportes', function() { return view('admin.reports'); })->name('reports.index');
        Route::get('/bitacora', function() { return view('admin.activity-log'); })->name('activity-log.index');
        Route::get('/configuracion', function() { return view('admin.settings'); })->name('settings.index');
    });

    // ============================================================
    // RUTAS COORDINADOR - Gestión limitada (docentes, aulas, materias)
    // ============================================================
    Route::middleware(['role:COORDINADOR'])->prefix('coordinador')->name('coordinator.')->group(function () {
        // Validaciones
        Route::get('/validar-carga', function() { return view('coordinator.workload-validation'); })->name('workload-validation');
        Route::get('/validar-horarios', function() { return view('coordinator.schedule-validation'); })->name('schedule-validation');
        
        // Reportes
        Route::get('/reportes-asistencia', function() { return view('coordinator.attendance-reports'); })->name('attendance-reports');
    });

    // ============================================================
    // RUTAS DOCENTE - Asistencia y clases virtuales
    // ============================================================
    Route::middleware(['role:DOCENTE'])->prefix('docente')->name('docente.')->group(function () {
        Route::get('/dashboard', [DocenteController::class, 'dashboard'])->name('dashboard');
        Route::post('/asistencia/{schedule}', [DocenteController::class, 'marcarAsistencia'])->name('asistencia.store');
        Route::post('/clase/{schedule}/virtual', [DocenteController::class, 'cambiarVirtual'])->name('clase.virtual');
        
        // Nuevas funcionalidades
        Route::get('/reportar-incidencia', function() { return view('docente.report-incident'); })->name('report-incident');
        Route::get('/justificaciones', function() { return view('docente.justifications'); })->name('justifications');
        Route::get('/horario-semanal', function() { return view('docente.weekly-schedule'); })->name('weekly-schedule');
        Route::get('/historial-asistencias', function() { return view('docente.attendance-history'); })->name('attendance-history');
        Route::get('/asistencia-qr', function() { 
            $teacher = auth()->user()->teacher;
            $schedules = [];
            
            if ($teacher) {
                $schedules = \App\Models\Schedule::with(['group.subject', 'room', 'teacher'])
                    ->where('teacher_id', $teacher->id)
                    ->get()
                    ->map(function($schedule) {
                        return [
                            'id' => $schedule->id,
                            'subject_name' => $schedule->group->subject->name ?? 'Sin materia',
                            'group_name' => $schedule->group->name ?? 'Sin grupo',
                            'day_of_week' => $schedule->day_of_week,
                            'start_time' => substr($schedule->start_time, 0, 5),
                            'end_time' => substr($schedule->end_time, 0, 5),
                            'room_name' => $schedule->room->name ?? 'Sin aula'
                        ];
                    });
            }
            
            return view('docente.attendance-qr', compact('schedules')); 
        })->name('attendance-qr');
        
        // Carga horaria con filtros
        Route::get('/carga-horaria', [DocenteController::class, 'cargaHoraria'])->name('workload');
        
        // Asistencia con QR
        Route::get('/asistencia-qr-nuevo', [DocenteController::class, 'attendanceQR'])->name('attendance-qr-new');
    });

    // ============================================================
    // RUTAS ESTUDIANTE - Solo visualización
    // ============================================================
    Route::middleware(['role:ESTUDIANTE'])->prefix('estudiante')->name('estudiante.')->group(function () {
        // Por ahora solo dashboard, en el futuro: ver horarios, materias inscritas, etc.
    });

    // ============================================================
    // API ENDPOINTS (CU04 - CU31) - AHORA PROTEGIDAS
    // ============================================================
    Route::prefix('api')->group(function () {

        // CU04 - Gestión de Periodo Académico
        Route::get('periods', [AcademicPeriodController::class, 'index']);
        Route::post('periods', [AcademicPeriodController::class, 'store']);
        Route::post('periods/{id}/activate', [AcademicPeriodController::class, 'activate']);
        Route::post('periods/{id}/close', [AcademicPeriodController::class, 'close']);
        Route::patch('periods/{id}', [AcademicPeriodController::class, 'update']);
        Route::delete('periods/{id}', [AcademicPeriodController::class, 'destroy']);

        // CU05 - Roles
        Route::get('roles', [RoleController::class, 'index']);
        Route::post('roles', [RoleController::class, 'store']);
        Route::patch('roles/{id}', [RoleController::class, 'update']);
        Route::delete('roles/{id}', [RoleController::class, 'destroy']);
        Route::get('users/{id}/roles', [RoleController::class, 'getUserRoles']);
        Route::post('users/{id}/roles', [RoleController::class, 'assignToUser']);

        // CU06 - Teachers CRUD
        Route::get('teachers', [TeacherController::class, 'index']);
        Route::post('teachers', [TeacherController::class, 'store']);
        Route::get('teachers/{id}', [TeacherController::class, 'show']);
        Route::patch('teachers/{id}', [TeacherController::class, 'update']);
        Route::delete('teachers/{id}', [TeacherController::class, 'destroy']);

        // CU07 - Docente visualiza/edita su propio perfil
        Route::get('teachers/me', [TeacherController::class, 'me']);
        Route::patch('teachers/me', [TeacherController::class, 'updateMe']);

        // Students CRUD
        Route::get('students', [\App\Http\Controllers\StudentController::class, 'index']);
        Route::post('students', [\App\Http\Controllers\StudentController::class, 'store']);
        Route::patch('students/{id}', [\App\Http\Controllers\StudentController::class, 'update']);
        Route::delete('students/{id}', [\App\Http\Controllers\StudentController::class, 'destroy']);

        // CU08 - Materias (Subjects) CRUD
        Route::get('subjects', [SubjectController::class, 'index']);
        Route::post('subjects', [SubjectController::class, 'store']);
        Route::get('subjects/{id}', [SubjectController::class, 'show']);
        Route::patch('subjects/{id}', [SubjectController::class, 'update']);
        Route::delete('subjects/{id}', [SubjectController::class, 'destroy']);

        // CU09 - Grupos (Groups) CRUD
        Route::get('groups', [GroupController::class, 'index']);
        Route::post('groups', [GroupController::class, 'store']);
        Route::get('groups/{id}', [GroupController::class, 'show']);
        Route::patch('groups/{id}', [GroupController::class, 'update']);
        Route::delete('groups/{id}', [GroupController::class, 'destroy']);

        // CU10 - Aulas (Rooms) CRUD
        Route::get('rooms', [RoomController::class, 'index']);
        Route::post('rooms', [RoomController::class, 'store']);
        Route::get('rooms/{id}', [RoomController::class, 'show']);
        Route::patch('rooms/{id}', [RoomController::class, 'update']);
        Route::delete('rooms/{id}', [RoomController::class, 'destroy']);

        // CU11 - Equipamiento de aulas
        Route::get('rooms/{id}/equipment', [RoomController::class, 'equipment']);
        Route::put('rooms/{id}/equipment', [RoomController::class, 'updateEquipment']);

        // CU21 - Consultar aulas disponibles
        Route::get('rooms/available', [RoomController::class, 'available'])->middleware('ensure.teacher_or_admin');

        // CU12 - Importar datos masivos (CSV)
        Route::post('imports', [ImportController::class, 'import']);

        // CU13 - Asignar carga horaria a docente
        Route::get('teachers/{id}/assignments', [TeacherAssignmentController::class, 'index']);
        Route::post('teachers/{id}/assignments', [TeacherAssignmentController::class, 'store']);
        Route::get('assignments', [TeacherAssignmentController::class, 'index']);
        Route::post('assignments', [TeacherAssignmentController::class, 'store']);
        Route::get('assignments/{id}', [TeacherAssignmentController::class, 'show']);
        Route::patch('assignments/{id}', [TeacherAssignmentController::class, 'update']);
        Route::delete('assignments/{id}', [TeacherAssignmentController::class, 'destroy']);

        // CU14 - Asignar horarios manual
        Route::get('schedules', [ScheduleController::class, 'index']);
        Route::post('schedules', [ScheduleController::class, 'store']);
        Route::get('schedules/{id}', [ScheduleController::class, 'show']);
        Route::patch('schedules/{id}', [ScheduleController::class, 'update']);
        Route::delete('schedules/{id}', [ScheduleController::class, 'destroy']);

        // CU15 - Generar horario automáticamente
        Route::post('schedules/generate', [ScheduleGeneratorController::class, 'generate'])->middleware('ensure.admin');

        // CU16 - Visualizar horario semanal
        Route::get('schedules/weekly', [ScheduleController::class, 'weekly'])->middleware('ensure.teacher_or_admin');
        Route::get('schedules/export', [ScheduleController::class, 'export'])->middleware('ensure.teacher_or_admin');
        Route::get('schedules/export.pdf', [ScheduleController::class, 'exportPdf'])->middleware('ensure.teacher_or_admin');
        Route::get('schedules/{id}/qrcode', [ScheduleController::class, 'generateQr'])->middleware('ensure.teacher_or_admin');

        // CU19 - Anular clase / cambiar a virtual
        Route::post('schedules/{id}/cancel', [ScheduleController::class, 'cancel'])->middleware('ensure.teacher_or_admin');
        Route::get('cancellations', [ClassCancellationController::class, 'index'])->middleware('ensure.teacher_or_admin');
        Route::get('cancellations/{id}', [ClassCancellationController::class, 'show'])->middleware('ensure.teacher_or_admin');
        Route::delete('cancellations/{id}', [ClassCancellationController::class, 'destroy'])->middleware('ensure.admin');

        // CU20 - Panel de conflictos horarios
        Route::get('conflicts', [ConflictController::class, 'index'])->middleware('ensure.admin');
        Route::post('conflicts', [ConflictController::class, 'store'])->middleware('ensure.admin');

        // CU22 - Consultar y reservar aulas liberadas
        Route::get('reservations/available', [ReservationController::class, 'available'])->middleware('ensure.teacher_or_admin');
        Route::post('reservations', [ReservationController::class, 'store'])->middleware('ensure.teacher_or_admin');
        Route::get('reservations', [ReservationController::class, 'index'])->middleware('ensure.teacher_or_admin');

        // CU23 - Panel admin
        Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])->middleware('ensure.admin');

        // CU24 - Asistencia por docente
        Route::get('reports/attendances/teacher/{id}', [AttendanceReportController::class, 'byTeacher'])->middleware('ensure.admin');

        // CU25 - Asistencia por grupo
        Route::get('reports/attendances/group/{id}', [AttendanceReportController::class, 'byGroup'])->middleware('ensure.teacher_or_admin');

        // CU26 - Reporte de horarios
        Route::get('reports/schedules', [ReportController::class, 'schedules'])->middleware('ensure.teacher_or_admin');

        // CU27 - Reporte de asistencia
        Route::get('reports/attendances', [ReportController::class, 'attendances'])->middleware('ensure.teacher_or_admin');

        // CU28 - Reporte de carga horaria
        Route::get('reports/workload', [ReportController::class, 'workload'])->middleware('ensure.teacher_or_admin');
        
        // Reportes adicionales
        Route::get('reports/teacher-workload', [ReportController::class, 'teacherWorkload']);
        Route::get('reports/teacher-attendance', [ReportController::class, 'teacherAttendance']);
        Route::get('reports/weekly-schedule', [ReportController::class, 'weeklySchedule']);
        Route::get('reports/available-rooms', [ReportController::class, 'availableRooms']);
        Route::get('reports/subject-attendance', [ReportController::class, 'subjectAttendance']);
        Route::get('reports/group-attendance', [ReportController::class, 'groupAttendance']);
        Route::get('reports/general-stats', [ReportController::class, 'generalStats']);
        Route::get('reports/absences', [ReportController::class, 'absences']);
        Route::post('reports/export-pdf', [ReportController::class, 'exportPdf']);
        Route::post('reports/export-excel', [ReportController::class, 'exportExcel']);

        // CU17 - Registrar asistencia docente
        Route::get('attendances', [AttendanceController::class, 'index'])->middleware('ensure.teacher_or_admin');
        Route::post('attendances', [AttendanceController::class, 'store'])->middleware('ensure.teacher_or_admin');
        Route::post('attendances/qr', [AttendanceController::class, 'registerQr'])->middleware('ensure.teacher_or_admin');
        Route::get('attendances/{id}', [AttendanceController::class, 'show'])->middleware('ensure.teacher_or_admin');
        Route::patch('attendances/{id}', [AttendanceController::class, 'update'])->middleware('ensure.teacher_or_admin');
        Route::delete('attendances/{id}', [AttendanceController::class, 'destroy'])->middleware('ensure.admin');

        // CU29 - Configurar parámetros del sistema
        Route::get('system-parameters', [SystemParameterController::class, 'index'])->middleware('ensure.admin');
        Route::get('system-parameters/{key}', [SystemParameterController::class, 'show'])->middleware('ensure.admin');
        Route::post('system-parameters', [SystemParameterController::class, 'store'])->middleware('ensure.admin');

        // CU30 - Anuncios generales
        Route::get('announcements', [AnnouncementController::class, 'index'])->middleware('ensure.teacher_or_admin');
        Route::post('announcements', [AnnouncementController::class, 'store'])->middleware('ensure.admin');
        Route::get('announcements/{id}', [AnnouncementController::class, 'show'])->middleware('ensure.teacher_or_admin');
        Route::patch('announcements/{id}', [AnnouncementController::class, 'update'])->middleware('ensure.admin');
        Route::delete('announcements/{id}', [AnnouncementController::class, 'destroy'])->middleware('ensure.admin');

        // CU31 - Incidencias en aulas
        Route::get('incidents', [IncidentController::class, 'index'])->middleware('ensure.teacher_or_admin');
        Route::post('incidents', [IncidentController::class, 'store'])->middleware('ensure.teacher_or_admin');
        Route::get('incidents/{id}', [IncidentController::class, 'show'])->middleware('ensure.teacher_or_admin');
        Route::patch('incidents/{id}', [IncidentController::class, 'update'])->middleware('ensure.teacher_or_admin');
        Route::delete('incidents/{id}', [IncidentController::class, 'destroy'])->middleware('ensure.teacher_or_admin');

        // Bitácora del Sistema
        Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->middleware('ensure.admin');
        Route::get('activity-logs/stats', [\App\Http\Controllers\ActivityLogController::class, 'stats'])->middleware('ensure.admin');
        Route::get('activity-logs/export-excel', [\App\Http\Controllers\ActivityLogController::class, 'exportExcel'])->middleware('ensure.admin');
        Route::get('activity-logs/export-pdf', [\App\Http\Controllers\ActivityLogController::class, 'exportPdf'])->middleware('ensure.admin');
        Route::delete('activity-logs/clear-old', [\App\Http\Controllers\ActivityLogController::class, 'clearOld'])->middleware('ensure.admin');
        
        // Carga horaria docente
        Route::get('docente/workload', [DocenteController::class, 'getCargaHoraria'])->middleware('ensure.teacher_or_admin');
        Route::get('docente/workload/export-pdf', [DocenteController::class, 'exportWorkloadPDF'])->middleware('ensure.teacher_or_admin');
        Route::get('docente/workload/export-excel', [DocenteController::class, 'exportWorkloadExcel'])->middleware('ensure.teacher_or_admin');
        
        // Carga horaria general (por docente, materia, grupo)
        Route::get('workload/teacher', [\App\Http\Controllers\WorkloadController::class, 'byTeacher']);
        Route::get('workload/subject', [\App\Http\Controllers\WorkloadController::class, 'bySubject']);
        Route::get('workload/group', [\App\Http\Controllers\WorkloadController::class, 'byGroup']);
        Route::get('workload/{type}/export-pdf', [\App\Http\Controllers\WorkloadController::class, 'exportPDF']);
    }); // <-- FIN DEL PREFIJO API

}); // <-- ESTA ES LA LLAVE DE CIERRE PRINCIPAL DEL MIDDLEWARE 'AUTH'