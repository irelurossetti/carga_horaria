<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';

    protected $fillable = [
        'subject_id',
        'code',
        'name',
        'capacity',
        'schedule',
        'enrolled_students',
        'status',
        'description',
        'room_id',
    ];

    /**
     * Define la relación con la Materia (Subject).
     * Esto es necesario para que el dashboard pueda mostrar el nombre de la materia.
     */
    public function subject()
    {
        // Asegúrate que el modelo Subject también apunte a 'public.subjects'
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Calcula el porcentaje de progreso del sílabo para este grupo
     */
    public function getSyllabusProgressAttribute()
    {
        if (!$this->subject_id) {
            return 0;
        }

        $totalTopics = SyllabusTopic::where('subject_id', $this->subject_id)->count();
        
        if ($totalTopics === 0) {
            return 0;
        }

        // Obtener los temas cubiertos en las asistencias de este grupo
        $coveredTopics = SyllabusTopic::where('subject_id', $this->subject_id)
            ->whereHas('attendances', function($query) {
                $query->whereHas('schedule', function($scheduleQuery) {
                    $scheduleQuery->where('group_id', $this->id);
                });
            })
            ->distinct()
            ->count();

        return $totalTopics > 0 ? round(($coveredTopics / $totalTopics) * 100, 2) : 0;
    }
}