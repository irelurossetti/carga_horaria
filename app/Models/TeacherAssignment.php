<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model
{
    protected $table = 'teacher_assignments';
    
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'group_id',
        'period_id',
        'assigned_by',
        'horas_semanales',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
        'tipo_asignacion',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'horas_semanales' => 'integer',
    ];

    /**
     * Relación inversa con Teacher (una asignación pertenece a un Docente)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Relación inversa con Subject (una asignación pertenece a una Materia)
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Relación inversa con Group (una asignación pertenece a un Grupo)
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Relación inversa con AcademicPeriod (una asignación pertenece a un Periodo)
     */
    public function period()
    {
        return $this->belongsTo(AcademicPeriod::class, 'period_id');
    }

    /**
     * Relación con el usuario que asignó
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Relación uno a muchos con Schedule (una asignación tiene muchos horarios)
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'assignment_id');
    }
}