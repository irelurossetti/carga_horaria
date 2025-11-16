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
}