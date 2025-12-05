<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyllabusTopic extends Model
{
    protected $fillable = [
        'subject_id',
        'unit_name',
        'topic_description',
        'order_index'
    ];

    /**
     * Relación inversa con Subject (un tema pertenece a una materia)
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Relación muchos a muchos con Attendance (un tema puede estar en muchas clases)
     */
    public function attendances()
    {
        return $this->belongsToMany(Attendance::class, 'attendance_syllabus_topic');
    }
}
