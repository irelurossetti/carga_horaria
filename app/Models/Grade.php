<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'grades';
    
    protected $fillable = [
        'student_id',
        'evaluation_criteria_id',
        'score',
        'comments',
        'graded_by',
        'graded_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'graded_at' => 'datetime',
    ];

    /**
     * Relación con el estudiante
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relación con el criterio de evaluación
     */
    public function evaluationCriteria()
    {
        return $this->belongsTo(EvaluationCriteria::class, 'evaluation_criteria_id');
    }

    /**
     * Relación con el docente que calificó
     */
    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Obtener el estado de la calificación (aprobado/reprobado)
     * 
     * @param float $passingGrade Nota mínima para aprobar (default: 51)
     * @return string
     */
    public function getStatus($passingGrade = 51)
    {
        if ($this->score >= $passingGrade) {
            return 'aprobado';
        }
        
        return 'reprobado';
    }

    /**
     * Obtener el color del badge según la nota
     * 
     * @return string
     */
    public function getBadgeColor()
    {
        if ($this->score >= 90) {
            return 'green'; // Excelente
        } elseif ($this->score >= 80) {
            return 'blue'; // Muy bueno
        } elseif ($this->score >= 70) {
            return 'yellow'; // Bueno
        } elseif ($this->score >= 51) {
            return 'orange'; // Aprobado
        } else {
            return 'red'; // Reprobado
        }
    }

    /**
     * Scope para filtrar por materia
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->whereHas('evaluationCriteria', function($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }

    /**
     * Scope para filtrar por periodo
     */
    public function scopeByPeriod($query, $periodId)
    {
        return $query->whereHas('evaluationCriteria', function($q) use ($periodId) {
            $q->where('period_id', $periodId);
        });
    }

    /**
     * Scope para filtrar por grupo
     */
    public function scopeByGroup($query, $groupId)
    {
        return $query->whereHas('evaluationCriteria', function($q) use ($groupId) {
            $q->where('group_id', $groupId);
        });
    }
}
