<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'public.attendances';
    public $timestamps = true;


    protected $fillable = [
        'schedule_id',
        'teacher_id',
        'date',
        'time',
        'status', // 'presente', 'ausente', 'licencia'
        'notes',
        'recorded_by',
        'is_substitute',
        'original_teacher_id',
        'substitute_teacher_id',
        'external_substitute_name',
        'external_substitute_email',
        'external_substitute_phone'
    ];

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación inversa con Schedule (una asistencia pertenece a un horario)
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    /**
     * Relación inversa con Teacher (una asistencia pertenece a un docente)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Relación con el docente original (cuando hay suplencia)
     */
    public function originalTeacher()
    {
        return $this->belongsTo(Teacher::class, 'original_teacher_id');
    }

    /**
     * Relación con el docente suplente
     */
    public function substituteTeacher()
    {
        return $this->belongsTo(Teacher::class, 'substitute_teacher_id');
    }

    /**
     * Relación muchos a muchos con SyllabusTopic (una asistencia puede tener muchos temas)
     */
    public function topics()
    {
        return $this->belongsToMany(SyllabusTopic::class, 'attendance_syllabus_topic');
    }
}