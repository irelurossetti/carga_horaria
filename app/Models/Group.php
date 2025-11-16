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
}