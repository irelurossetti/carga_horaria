<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'schedule_id',
        'teacher_id',
        'reserved_at',
        'expires_at',
        'notes',
    ];

    protected $dates = ['reserved_at','expires_at'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function teacher()
    {
        return $this->belongsTo(\App\Models\Teacher::class, 'teacher_id');
    }

    /**
     * Relación muchos a muchos con Resource
     */
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'reservation_resources')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }
}
