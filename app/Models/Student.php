<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Los estudiantes están en la tabla users con rol ESTUDIANTE
    // Este modelo es un wrapper para facilitar las consultas
    
    protected $table = 'users';
    
    protected $fillable = [
        'name',
        'email',
        'registration_number',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relación con las calificaciones
     */
    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    /**
     * Relación con los roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Calcular la nota final de una materia
     * 
     * @param int $subjectId ID de la materia
     * @param int|null $periodId ID del periodo (opcional)
     * @return float|null Nota final ponderada o null si no hay calificaciones
     */
    public function calculateFinalGrade($subjectId, $periodId = null)
    {
        // Obtener los criterios de evaluación de la materia
        $criteriaQuery = EvaluationCriteria::where('subject_id', $subjectId)
            ->where('is_active', true);
        
        if ($periodId) {
            $criteriaQuery->where('period_id', $periodId);
        }
        
        $criteria = $criteriaQuery->get();
        
        if ($criteria->isEmpty()) {
            return null;
        }

        // Obtener las calificaciones del estudiante para estos criterios
        $criteriaIds = $criteria->pluck('id');
        $grades = $this->grades()
            ->whereIn('evaluation_criteria_id', $criteriaIds)
            ->get()
            ->keyBy('evaluation_criteria_id');

        // Calcular la nota ponderada
        $totalWeight = 0;
        $weightedSum = 0;

        foreach ($criteria as $criterion) {
            $grade = $grades->get($criterion->id);
            
            if ($grade) {
                $weightedSum += ($grade->score * $criterion->weight) / 100;
                $totalWeight += $criterion->weight;
            }
        }

        // Si no hay calificaciones, retornar null
        if ($totalWeight == 0) {
            return null;
        }

        // Retornar la nota final (ajustada si no se completaron todos los criterios)
        return round($weightedSum, 2);
    }

    /**
     * Obtener todas las notas finales del estudiante por materia
     * 
     * @param int|null $periodId ID del periodo (opcional)
     * @return array Array con subject_id => final_grade
     */
    public function getAllFinalGrades($periodId = null)
    {
        $gradesQuery = $this->grades()
            ->with('evaluationCriteria.subject');
        
        if ($periodId) {
            $gradesQuery->whereHas('evaluationCriteria', function($q) use ($periodId) {
                $q->where('period_id', $periodId);
            });
        }
        
        $grades = $gradesQuery->get();
        
        // Agrupar por materia
        $subjectIds = $grades->pluck('evaluationCriteria.subject_id')->unique();
        
        $finalGrades = [];
        foreach ($subjectIds as $subjectId) {
            $finalGrades[$subjectId] = $this->calculateFinalGrade($subjectId, $periodId);
        }
        
        return $finalGrades;
    }

    /**
     * Verificar si el estudiante está aprobado en una materia
     * 
     * @param int $subjectId ID de la materia
     * @param float $passingGrade Nota mínima para aprobar (default: 51)
     * @param int|null $periodId ID del periodo (opcional)
     * @return bool
     */
    public function isPassing($subjectId, $passingGrade = 51, $periodId = null)
    {
        $finalGrade = $this->calculateFinalGrade($subjectId, $periodId);
        
        if ($finalGrade === null) {
            return false;
        }
        
        return $finalGrade >= $passingGrade;
    }

    /**
     * Scope para filtrar solo estudiantes
     */
    public function scopeStudents($query)
    {
        return $query->whereHas('roles', function($q) {
            $q->where('name', 'ESTUDIANTE');
        });
    }
}
