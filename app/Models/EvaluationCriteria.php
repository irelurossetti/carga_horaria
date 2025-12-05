<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationCriteria extends Model
{
    protected $table = 'evaluation_criteria';
    
    protected $fillable = [
        'name',
        'weight',
        'period_id',
        'subject_id',
        'group_id',
        'description',
        'evaluation_date',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'evaluation_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relación con el periodo académico
     */
    public function period()
    {
        return $this->belongsTo(AcademicPeriod::class, 'period_id');
    }

    /**
     * Relación con la materia
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Relación con el grupo
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Relación con las calificaciones
     */
    public function grades()
    {
        return $this->hasMany(Grade::class, 'evaluation_criteria_id');
    }

    /**
     * Validar que el peso total de criterios no exceda 100%
     * 
     * @param int $subjectId
     * @param int $periodId
     * @param int|null $excludeId ID del criterio a excluir (para edición)
     * @return bool
     */
    public static function validateTotalWeight($subjectId, $periodId, $excludeId = null)
    {
        $query = self::where('subject_id', $subjectId)
            ->where('period_id', $periodId)
            ->where('is_active', true);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        $totalWeight = $query->sum('weight');
        
        return $totalWeight <= 100;
    }

    /**
     * Obtener el peso disponible para nuevos criterios
     * 
     * @param int $subjectId
     * @param int $periodId
     * @param int|null $excludeId
     * @return float
     */
    public static function getAvailableWeight($subjectId, $periodId, $excludeId = null)
    {
        $query = self::where('subject_id', $subjectId)
            ->where('period_id', $periodId)
            ->where('is_active', true);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        $totalWeight = $query->sum('weight');
        
        return 100 - $totalWeight;
    }
}
