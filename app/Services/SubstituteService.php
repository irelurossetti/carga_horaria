<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class SubstituteService
{
    /**
     * Buscar docentes disponibles para suplencia en un horario específico
     * 
     * @param string $date Fecha en formato Y-m-d
     * @param string $startTime Hora inicio en formato H:i
     * @param string $endTime Hora fin en formato H:i
     * @param int|null $excludeTeacherId ID del docente a excluir (el ausente)
     * @return \Illuminate\Support\Collection
     */
    public function findAvailableSubstitutes($date, $startTime, $endTime, $excludeTeacherId = null)
    {
        // Obtener el día de la semana (0=Domingo, 1=Lunes, ..., 6=Sábado)
        $dayOfWeek = date('w', strtotime($date));
        
        // Buscar todos los horarios que se solapan con el horario solicitado
        $busyTeacherIds = Schedule::where('day_of_week', $dayOfWeek)
            ->where(function ($query) use ($startTime, $endTime) {
                // Horarios que se solapan
                $query->where(function ($q) use ($startTime, $endTime) {
                    // El horario existente comienza durante el período solicitado
                    $q->where('start_time', '>=', $startTime)
                      ->where('start_time', '<', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // El horario existente termina durante el período solicitado
                    $q->where('end_time', '>', $startTime)
                      ->where('end_time', '<=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // El horario existente engloba completamente el período solicitado
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>=', $endTime);
                });
            })
            ->pluck('teacher_id')
            ->unique()
            ->toArray();
        
        // Buscar docentes que NO estén ocupados
        $query = Teacher::whereNotIn('id', $busyTeacherIds);
        
        // Excluir el docente ausente
        if ($excludeTeacherId) {
            $query->where('id', '!=', $excludeTeacherId);
        }
        
        // Obtener docentes disponibles con información adicional
        $availableTeachers = $query->select([
                'teachers.id',
                'teachers.name',
                'teachers.email',
                'teachers.phone',
                'teachers.department'
            ])
            ->get()
            ->map(function ($teacher) use ($date, $dayOfWeek) {
                // Calcular carga horaria del día
                $dailyHours = Schedule::where('teacher_id', $teacher->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->get()
                    ->sum(function ($schedule) {
                        $start = strtotime($schedule->start_time);
                        $end = strtotime($schedule->end_time);
                        return ($end - $start) / 3600; // Convertir a horas
                    });
                
                $teacher->daily_hours = $dailyHours;
                $teacher->is_available = true;
                
                return $teacher;
            })
            ->sortBy('daily_hours'); // Ordenar por carga horaria (menos ocupados primero)
        
        return $availableTeachers;
    }

    /**
     * Asignar un suplente a una clase
     * 
     * @param int $scheduleId ID del horario
     * @param int|null $substituteTeacherId ID del docente suplente (null si es externo)
     * @param int $originalTeacherId ID del docente original
     * @param string|null $reason Motivo de la suplencia
     * @param array|null $externalData Datos del suplente externo ['name', 'email', 'phone']
     * @return \App\Models\Attendance
     */
    public function assignSubstitute($scheduleId, $substituteTeacherId, $originalTeacherId, $reason = null, $externalData = null)
    {
        $data = [
            'schedule_id' => $scheduleId,
            'original_teacher_id' => $originalTeacherId,
            'is_substitute' => true,
            'status' => 'presente',
            'date' => now()->format('Y-m-d'),
            'time' => now()->format('H:i:s'),
        ];
        
        // Si es un docente registrado
        if ($substituteTeacherId) {
            $data['teacher_id'] = $substituteTeacherId;
            $data['substitute_teacher_id'] = $substituteTeacherId;
        }
        
        // Si es un suplente externo
        if ($externalData) {
            $data['external_substitute_name'] = $externalData['name'] ?? null;
            $data['external_substitute_email'] = $externalData['email'] ?? null;
            $data['external_substitute_phone'] = $externalData['phone'] ?? null;
        }
        
        $attendance = \App\Models\Attendance::create($data);
        
        // Registrar en la bitácora si existe
        if (class_exists('\App\Models\ActivityLog')) {
            $substituteName = $substituteTeacherId 
                ? ($attendance->substituteTeacher->name ?? 'Docente registrado')
                : ($externalData['name'] ?? 'Suplente externo');
                
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'substitute_assigned',
                'description' => "Suplente asignado: " . $substituteName . 
                                " reemplaza a " . ($attendance->originalTeacher->name ?? 'Docente') .
                                ($reason ? " - Motivo: $reason" : ""),
                'ip_address' => request()->ip(),
            ]);
        }
        
        return $attendance->load(['substituteTeacher', 'originalTeacher', 'schedule']);
    }

    /**
     * Obtener estadísticas de suplencias
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getSubstituteStats($startDate = null, $endDate = null)
    {
        $query = \App\Models\Attendance::whereRaw('is_substitute = true');
        
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        
        $total = $query->count();
        
        $byTeacher = $query->with('substituteTeacher')
            ->get()
            ->groupBy('substitute_teacher_id')
            ->map(function ($group) {
                return [
                    'teacher' => $group->first()->substituteTeacher->name ?? 'N/A',
                    'count' => $group->count()
                ];
            })
            ->sortByDesc('count')
            ->values();
        
        return [
            'total' => $total,
            'by_teacher' => $byTeacher,
        ];
    }
}
