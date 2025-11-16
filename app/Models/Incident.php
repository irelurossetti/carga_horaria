<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = ['teacher_id','room_id','type','priority','description','status','reported_by','resolved_by','resolved_at'];
    protected $casts = ['resolved_at' => 'datetime'];
    
    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class);
    }
    
    public function reporter()
    {
        return $this->belongsTo(\App\Models\User::class, 'reported_by');
    }
    
    protected $appends = ['room_name', 'reporter_name'];
    
    public function getRoomNameAttribute()
    {
        return $this->room ? $this->room->name : 'N/A';
    }
    
    public function getReporterNameAttribute()
    {
        return $this->reporter ? $this->reporter->name : 'N/A';
    }
}
