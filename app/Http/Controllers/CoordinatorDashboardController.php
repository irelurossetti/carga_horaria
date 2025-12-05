<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Room;
use App\Models\Teacher;
use App\Models\Group;
use App\Models\SyllabusTopic;
use Illuminate\Support\Facades\Auth;
// Modelos comentados - tablas no existen:
// use App\Models\Conflict;

class CoordinatorDashboardController extends Controller
{
    /**
     * Muestra el dashboard del coordinador con estadísticas.
     */
    public function index()
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();
        
        // Verificar que el usuario tenga rol de COORDINADOR
        if (!$user->hasRole('COORDINADOR')) {
            Auth::logout();
            return redirect('/login')->with('error', 'No tienes permisos de coordinador.');
        }

        // Obtener estadísticas
        $data = $this->getDashboardData();

        return view('coordinator.dashboard', $data);
    }

    /**
     * Obtiene los datos del dashboard del coordinador.
     */
    private function getDashboardData(): array
    {
        // TABLAS QUE NO EXISTEN - Retornar 0
        $conflictsCount = 0;
        $recentConflicts = collect([]);
        
        // Obtener grupos con progreso del sílabo
        $enabledGroups = Group::with('subject')->count();
        $groupsWithProgress = Group::with('subject')->get()->map(function($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'subject' => $group->subject->name ?? 'Sin materia',
                'progress' => $group->syllabus_progress
            ];
        });

        // Contar horarios sin aula asignada
        $schedulesWithoutRoom = Schedule::whereNull('room_id')->count();

        // Calcular avance de programación (% de horarios con aula asignada)
        $totalSchedules = Schedule::count();
        $schedulesWithRoom = Schedule::whereNotNull('room_id')->count();
        $programmingProgress = $totalSchedules > 0 
            ? round(($schedulesWithRoom / $totalSchedules) * 100) 
            : 0;

        // Datos adicionales
        $totalTeachers = Teacher::count();
        $totalRooms = Room::count();
        
        $now = now();
        $currentDay = $now->dayOfWeek;
        $currentTime = $now->format('H:i:s');
        
        $daysMap = ['DOMINGO', 'LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO'];
        $currentDayName = $daysMap[$currentDay] ?? null;
        
        $busyRooms = 0;
        if ($currentDayName && $totalSchedules > 0) {
            $busyRooms = Schedule::where('day_of_week', $currentDayName)
                ->where('start_time', '<=', $currentTime)
                ->where('end_time', '>=', $currentTime)
                ->whereNotNull('room_id')
                ->distinct('room_id')
                ->count('room_id');
        }
            
        $freeRoomsToday = max(0, $totalRooms - $busyRooms);

        // Horarios del día
        $upcomingSchedules = collect([]);
        $currentClass = null;
        
        if ($currentDayName && $totalSchedules > 0) {
            try {
                $upcomingSchedules = Schedule::where('day_of_week', $currentDayName)
                    ->where('start_time', '>=', $currentTime)
                    ->orderBy('start_time', 'asc')
                    ->take(3)
                    ->get();

                $currentClass = Schedule::where('day_of_week', $currentDayName)
                    ->where('start_time', '<=', $currentTime)
                    ->where('end_time', '>=', $currentTime)
                    ->first();
            } catch (\Exception $e) {
                \Log::warning('Error al obtener horarios coordinador: ' . $e->getMessage());
            }
        }

        return [
            'conflictsCount' => $conflictsCount,
            'schedulesWithoutRoom' => $schedulesWithoutRoom,
            'enabledGroups' => $enabledGroups,
            'programmingProgress' => $programmingProgress,
            'recentConflicts' => $recentConflicts,
            'totalTeachers' => $totalTeachers,
            'freeRoomsToday' => $freeRoomsToday,
            'currentClass' => $currentClass,
            'upcomingSchedules' => $upcomingSchedules,
            'groupsWithProgress' => $groupsWithProgress,
        ];
    }
}
