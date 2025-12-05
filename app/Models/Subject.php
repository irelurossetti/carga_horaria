<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'public.subjects';
    protected $fillable = [
        'name', 
        'code', 
        'credits', 
        'description', 
        'semester', 
        'theoretical_hours', 
        'practical_hours', 
        'prerequisites', 
        'status'
    ];
    public $timestamps = false;

    /**
     * Relación uno a muchos con Group (una materia tiene muchos grupos)
     */
    public function groups()
    {
        return $this->hasMany(Group::class, 'subject_id');
    }

    /**
     * Relación uno a muchos con SyllabusTopic (una materia tiene muchos temas)
     */
    public function syllabusTopics()
    {
        return $this->hasMany(SyllabusTopic::class, 'subject_id')->orderBy('order_index');
    }
}