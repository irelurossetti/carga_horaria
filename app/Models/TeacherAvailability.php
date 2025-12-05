<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAvailability extends Model
{
    protected $fillable = [
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
        'preference',
        'notes',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Relación con Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Verificar si un horario está disponible
     */
    public static function isTeacherAvailable($teacherId, $dayOfWeek, $startTime, $endTime)
    {
        return !self::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', false)
            ->where(function($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function($qq) use ($startTime, $endTime) {
                      $qq->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            })
            ->exists();
    }

    /**
     * Obtener preferencia del docente para un horario
     */
    public static function getTeacherPreference($teacherId, $dayOfWeek, $startTime, $endTime)
    {
        $availability = self::where('teacher_id', $teacherId)
            ->where('day_of_week', $dayOfWeek)
            ->where(function($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function($qq) use ($startTime, $endTime) {
                      $qq->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            })
            ->first();

        return $availability ? $availability->preference : 'neutral';
    }
}
