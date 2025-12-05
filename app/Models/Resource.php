<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'serial_number',
        'type',
        'status',
        'description',
        'brand',
        'model',
        'location',
        'purchase_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    /**
     * Relación muchos a muchos con Reservation
     */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_resources')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }

    /**
     * Verificar si el recurso está disponible en un horario específico
     */
    public function isAvailableAt($startTime, $endTime)
    {
        // Verificar si el recurso está en mantenimiento o dañado
        if (in_array($this->status, ['Mantenimiento', 'Dañado'])) {
            return false;
        }

        // Verificar si hay reservas que se solapen con el horario solicitado
        $conflictingReservations = $this->reservations()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    // La reserva existente comienza durante el período solicitado
                    $q->whereBetween('reserved_at', [$startTime, $endTime]);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // La reserva existente termina durante el período solicitado
                    $q->whereBetween('expires_at', [$startTime, $endTime]);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // La reserva existente engloba completamente el período solicitado
                    $q->where('reserved_at', '<=', $startTime)
                      ->where('expires_at', '>=', $endTime);
                });
            })
            ->exists();

        return !$conflictingReservations;
    }

    /**
     * Scope para recursos disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Disponible');
    }

    /**
     * Scope para recursos por tipo
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
