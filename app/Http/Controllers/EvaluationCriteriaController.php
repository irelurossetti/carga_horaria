<?php

namespace App\Http\Controllers;

use App\Models\EvaluationCriteria;
use Illuminate\Http\Request;

class EvaluationCriteriaController extends Controller
{
    /**
     * Listar criterios de evaluación
     */
    public function index(Request $request)
    {
        $query = EvaluationCriteria::with(['period', 'subject', 'group']);
        
        if ($request->has('period_id')) {
            $query->where('period_id', $request->period_id);
        }
        
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->has('group_id')) {
            $query->where('group_id', $request->group_id);
        }
        
        $criteria = $query->orderBy('evaluation_date')->get();
        
        return response()->json([
            'success' => true,
            'data' => $criteria,
        ]);
    }

    /**
     * Crear criterio de evaluación
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'weight' => 'required|numeric|min:0|max:100',
                'period_id' => 'required|exists:academic_periods,id',
                'subject_id' => 'nullable|exists:subjects,id',
                'group_id' => 'nullable|exists:groups,id',
                'description' => 'nullable|string',
                'evaluation_date' => 'nullable|date',
            ]);

            // Validar que el peso total no exceda 100%
            if ($validated['subject_id']) {
                $availableWeight = EvaluationCriteria::getAvailableWeight(
                    $validated['subject_id'],
                    $validated['period_id']
                );
                
                if ($validated['weight'] > $availableWeight) {
                    return response()->json([
                        'success' => false,
                        'message' => "El peso excede el disponible. Solo quedan {$availableWeight}% disponibles.",
                    ], 422);
                }
            }

            $criteria = EvaluationCriteria::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Criterio de evaluación creado exitosamente',
                'data' => $criteria->load(['period', 'subject', 'group']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el criterio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar criterio de evaluación
     */
    public function update(Request $request, $id)
    {
        try {
            $criteria = EvaluationCriteria::findOrFail($id);
            
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'weight' => 'sometimes|required|numeric|min:0|max:100',
                'description' => 'nullable|string',
                'evaluation_date' => 'nullable|date',
                'is_active' => 'sometimes|boolean',
            ]);

            // Validar peso si se está actualizando
            if (isset($validated['weight']) && $criteria->subject_id) {
                $availableWeight = EvaluationCriteria::getAvailableWeight(
                    $criteria->subject_id,
                    $criteria->period_id,
                    $criteria->id
                );
                
                if ($validated['weight'] > $availableWeight) {
                    return response()->json([
                        'success' => false,
                        'message' => "El peso excede el disponible. Solo quedan {$availableWeight}% disponibles.",
                    ], 422);
                }
            }

            $criteria->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Criterio actualizado exitosamente',
                'data' => $criteria->load(['period', 'subject', 'group']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el criterio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar criterio de evaluación
     */
    public function destroy($id)
    {
        try {
            $criteria = EvaluationCriteria::findOrFail($id);
            
            // Verificar si tiene calificaciones asociadas
            if ($criteria->grades()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el criterio porque tiene calificaciones asociadas.',
                ], 422);
            }
            
            $criteria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Criterio eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el criterio: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener peso disponible para un periodo/materia
     */
    public function getAvailableWeight(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'period_id' => 'required|exists:academic_periods,id',
            'exclude_id' => 'nullable|exists:evaluation_criteria,id',
        ]);

        $availableWeight = EvaluationCriteria::getAvailableWeight(
            $validated['subject_id'],
            $validated['period_id'],
            $validated['exclude_id'] ?? null
        );

        return response()->json([
            'success' => true,
            'available_weight' => $availableWeight,
        ]);
    }
}
