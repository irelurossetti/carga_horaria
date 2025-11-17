<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkloadController extends Controller
{
    public function byTeacher(Request $request)
    {
        $period = $request->input('period', 'week');
        $teacherId = $request->input('id');

        $query = Schedule::with(['group.subject', 'teacher', 'room']);
        
        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        $schedules = $query->get();

        // Calcular estadísticas
        $totalHours = 0;
        $distribution = [];
        $details = [];
        $teacherData = [];

        foreach ($schedules as $schedule) {
            if ($schedule->start_time && $schedule->end_time) {
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);
                $hours = abs($end->diffInHours($start, false));
                $totalHours += $hours;

                if ($schedule->teacher) {
                    $teacherName = $schedule->teacher->name;
                    if (!isset($distribution[$teacherName])) {
                        $distribution[$teacherName] = 0;
                    }
                    $distribution[$teacherName] += $hours;

                    if (!isset($teacherData[$schedule->teacher->id])) {
                        $teacherData[$schedule->teacher->id] = [
                            'name' => $teacherName,
                            'email' => $schedule->teacher->email,
                            'schedules' => 0,
                            'hours' => 0
                        ];
                    }
                    $teacherData[$schedule->teacher->id]['schedules']++;
                    $teacherData[$schedule->teacher->id]['hours'] += $hours;
                }
            }
        }

        $details = array_values($teacherData);

        $hoursPerPeriod = $totalHours;
        if ($period === 'month') {
            $hoursPerPeriod = $totalHours * 4;
        } elseif ($period === 'semester') {
            $hoursPerPeriod = $totalHours * 24;
        }

        return response()->json([
            'stats' => [
                'hours_week' => $totalHours,
                'hours_period' => $hoursPerPeriod,
                'total_classes' => $schedules->count()
            ],
            'distribution' => $distribution,
            'details' => $details
        ]);
    }

    public function bySubject(Request $request)
    {
        $period = $request->input('period', 'week');
        $subjectId = $request->input('id');

        $query = Schedule::with(['group.subject', 'teacher', 'room']);
        
        if ($subjectId) {
            $query->whereHas('group.subject', function($q) use ($subjectId) {
                $q->where('id', $subjectId);
            });
        }

        $schedules = $query->get();

        $totalHours = 0;
        $distribution = [];
        $details = [];
        $subjectData = [];

        foreach ($schedules as $schedule) {
            if ($schedule->start_time && $schedule->end_time && $schedule->group && $schedule->group->subject) {
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);
                $hours = abs($end->diffInHours($start, false));
                $totalHours += $hours;

                $subject = $schedule->group->subject;
                $subjectName = $subject->name;
                
                if (!isset($distribution[$subjectName])) {
                    $distribution[$subjectName] = 0;
                }
                $distribution[$subjectName] += $hours;

                if (!isset($subjectData[$subject->id])) {
                    $subjectData[$subject->id] = [
                        'name' => $subjectName,
                        'code' => $subject->code ?? 'N/A',
                        'groups' => 0,
                        'hours' => 0
                    ];
                }
                $subjectData[$subject->id]['groups']++;
                $subjectData[$subject->id]['hours'] += $hours;
            }
        }

        $details = array_values($subjectData);

        $hoursPerPeriod = $totalHours;
        if ($period === 'month') {
            $hoursPerPeriod = $totalHours * 4;
        } elseif ($period === 'semester') {
            $hoursPerPeriod = $totalHours * 24;
        }

        return response()->json([
            'stats' => [
                'hours_week' => $totalHours,
                'hours_period' => $hoursPerPeriod,
                'total_classes' => $schedules->count()
            ],
            'distribution' => $distribution,
            'details' => $details
        ]);
    }

    public function byGroup(Request $request)
    {
        $period = $request->input('period', 'week');
        $groupId = $request->input('id');

        $query = Schedule::with(['group.subject', 'teacher', 'room']);
        
        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        $schedules = $query->get();

        $totalHours = 0;
        $distribution = [];
        $distributionTheory = [];
        $distributionPractice = [];
        $details = [];
        $groupData = [];

        foreach ($schedules as $schedule) {
            if ($schedule->start_time && $schedule->end_time && $schedule->group) {
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);
                $hours = abs($end->diffInHours($start, false));
                $totalHours += $hours;

                $group = $schedule->group;
                $groupName = $group->name;
                
                // Determinar si es teórica o práctica (basado en el nombre del aula o tipo)
                $isPractice = false;
                if ($schedule->room) {
                    $roomName = strtolower($schedule->room->name);
                    $isPractice = (
                        strpos($roomName, 'lab') !== false || 
                        strpos($roomName, 'laboratorio') !== false ||
                        strpos($roomName, 'taller') !== false
                    );
                }
                
                if (!isset($distribution[$groupName])) {
                    $distribution[$groupName] = 0;
                    $distributionTheory[$groupName] = 0;
                    $distributionPractice[$groupName] = 0;
                }
                $distribution[$groupName] += $hours;
                
                if ($isPractice) {
                    $distributionPractice[$groupName] += $hours;
                } else {
                    $distributionTheory[$groupName] += $hours;
                }

                if (!isset($groupData[$group->id])) {
                    $groupData[$group->id] = [
                        'name' => $groupName,
                        'subject' => $group->subject ? $group->subject->name : 'N/A',
                        'schedules' => 0,
                        'hours' => 0,
                        'hours_theory' => 0,
                        'hours_practice' => 0
                    ];
                }
                $groupData[$group->id]['schedules']++;
                $groupData[$group->id]['hours'] += $hours;
                
                if ($isPractice) {
                    $groupData[$group->id]['hours_practice'] += $hours;
                } else {
                    $groupData[$group->id]['hours_theory'] += $hours;
                }
            }
        }

        $details = array_values($groupData);

        $hoursPerPeriod = $totalHours;
        if ($period === 'month') {
            $hoursPerPeriod = $totalHours * 4;
        } elseif ($period === 'semester') {
            $hoursPerPeriod = $totalHours * 24;
        }

        return response()->json([
            'stats' => [
                'hours_week' => $totalHours,
                'hours_period' => $hoursPerPeriod,
                'total_classes' => $schedules->count()
            ],
            'distribution' => $distribution,
            'distribution_theory' => $distributionTheory,
            'distribution_practice' => $distributionPractice,
            'details' => $details
        ]);
    }

    public function exportPDF(Request $request, $type)
    {
        $period = $request->input('period', 'week');
        $entityId = $request->input('id');

        // Obtener datos según el tipo
        $data = null;
        $title = '';
        
        if ($type === 'teacher') {
            $response = $this->byTeacher($request);
            $title = 'Carga Horaria por Docente';
        } elseif ($type === 'subject') {
            $response = $this->bySubject($request);
            $title = 'Carga Horaria por Materia';
        } elseif ($type === 'group') {
            $response = $this->byGroup($request);
            $title = 'Carga Horaria por Grupo';
        }

        $data = json_decode($response->getContent());

        $periodLabels = [
            'week' => 'Semanal',
            'month' => 'Mensual',
            'semester' => 'Semestral'
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.workload-general-pdf', [
            'title' => $title,
            'period' => $periodLabels[$period] ?? 'Semanal',
            'stats' => $data->stats,
            'details' => $data->details,
            'type' => $type,
            'date' => \Carbon\Carbon::now()->format('d/m/Y H:i')
        ]);

        return $pdf->download('carga_horaria_' . $type . '_' . date('Y-m-d') . '.pdf');
    }
}
