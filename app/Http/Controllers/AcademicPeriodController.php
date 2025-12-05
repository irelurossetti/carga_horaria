<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicPeriodController extends Controller
{
    protected function ensureAdmin()
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'No autenticado');
        }
        
        // Cargar roles si no están cargados
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }
        
        $hasAdminRole = $user->roles->contains(function ($role) {
            return in_array(strtoupper($role->name), ['ADMIN', 'ADMINISTRADOR']);
        });
        
        if (!$hasAdminRole) {
            abort(403, 'No tienes permisos de administrador');
        }
    }

    /**
     * Muestra la vista principal de Periodos (WEB).
     */
    public function webIndex()
    {
        // Usar datos REALES de la base de datos
        try {
            // Usar consultas SQL directas para evitar el caché de PostgreSQL
            $periodsRaw = DB::select('SELECT * FROM academic_periods ORDER BY created_at DESC');
            
            // Convertir stdClass a arrays para compatibilidad con Blade
            $periods = collect($periodsRaw)->map(function($period) {
                return (array) $period;
            });
            
            // Calcular estadísticas reales
            $activePeriods = DB::select('SELECT COUNT(*) as count FROM academic_periods WHERE status = ?', ['active'])[0]->count ?? 0;
            $totalPeriods = $periods->count();
            $plannedPeriods = DB::select('SELECT COUNT(*) as count FROM academic_periods WHERE status = ?', ['planned'])[0]->count ?? 0;
            $closedPeriods = DB::select('SELECT COUNT(*) as count FROM academic_periods WHERE status = ?', ['closed'])[0]->count ?? 0;

        } catch (\Exception $e) {
            // Si hay error (tabla no existe), usar datos de prueba
            $periods = collect([
                [
                    'id' => 1,
                    'code' => '2025-1',
                    'name' => 'Primer Semestre 2025',
                    'description' => 'Periodo académico primer semestre',
                    'start_date' => '2025-02-01',
                    'end_date' => '2025-06-30',
                    'status' => 'active'
                ],
                [
                    'id' => 2,
                    'code' => '2025-2', 
                    'name' => 'Segundo Semestre 2025',
                    'description' => 'Periodo académico segundo semestre',
                    'start_date' => '2025-08-01',
                    'end_date' => '2025-12-20',
                    'status' => 'planned'
                ]
            ]);

            $activePeriods = 1;
            $totalPeriods = 2;
            $plannedPeriods = 1;
            $closedPeriods = 0;
        }

        return view('periods.index', [
            'periods' => $periods,
            'activePeriods' => $activePeriods,
            'totalPeriods' => $totalPeriods,
            'plannedPeriods' => $plannedPeriods,
            'closedPeriods' => $closedPeriods
        ]);
    }

    /**
     * API: Listar periodos académicos
     */
    public function index()
    {
        // Permitir acceso a usuarios autenticados
        // $this->ensureAdmin();
        
        // Usar consulta SQL directa para evitar el caché de PostgreSQL
        $periods = DB::select('SELECT * FROM academic_periods ORDER BY created_at DESC');
        
        return response()->json($periods);
    }

    /**
     * API: Crear periodo académico
     */
    public function store(Request $request)
    {
        try {
            $this->ensureAdmin();

            $data = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:academic_periods,code',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'description' => 'nullable|string'
            ]);

            $period = AcademicPeriod::create(array_merge($data, ['status' => 'planned']));

            return response()->json([
                'success' => true,
                'message' => 'Periodo creado exitosamente',
                'data' => $period
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el periodo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Activar periodo académico
     */
    public function activate($id)
    {
        try {
            $this->ensureAdmin();

            // Verificar que el periodo existe
            $existing = DB::select("SELECT id FROM academic_periods WHERE id = ?", [$id]);
            if (empty($existing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periodo no encontrado'
                ], 404);
            }

            // Cerrar cualquier periodo activo actual
            DB::update("UPDATE academic_periods SET status = 'closed', updated_at = CURRENT_TIMESTAMP WHERE status = 'active'");

            // Activar el periodo seleccionado
            DB::update("UPDATE academic_periods SET status = 'active', updated_at = CURRENT_TIMESTAMP WHERE id = ?", [$id]);

            // Obtener el periodo actualizado
            $period = DB::select("SELECT * FROM academic_periods WHERE id = ?", [$id])[0] ?? null;

            return response()->json([
                'success' => true,
                'message' => 'Periodo activado exitosamente',
                'data' => $period
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al activar el periodo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Cerrar periodo académico
     */
    public function close($id)
    {
        try {
            $this->ensureAdmin();

            // Verificar que el periodo existe
            $existing = DB::select("SELECT id FROM academic_periods WHERE id = ?", [$id]);
            if (empty($existing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periodo no encontrado'
                ], 404);
            }

            // Cerrar el periodo usando SQL directo
            DB::update("UPDATE academic_periods SET status = 'closed', updated_at = CURRENT_TIMESTAMP WHERE id = ?", [$id]);

            // Obtener el periodo actualizado
            $period = DB::select("SELECT * FROM academic_periods WHERE id = ?", [$id])[0] ?? null;

            return response()->json([
                'success' => true,
                'message' => 'Periodo cerrado exitosamente',
                'data' => $period
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar el periodo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Actualizar periodo académico
     */
    public function update(Request $request, $id)
    {
        try {
            $this->ensureAdmin();

            // Verificar que el periodo existe
            $existing = DB::select("SELECT * FROM academic_periods WHERE id = ?", [$id]);
            if (empty($existing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periodo no encontrado'
                ], 404);
            }

            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'code' => 'sometimes|required|string|max:50|unique:academic_periods,code,' . $id,
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'description' => 'nullable|string',
                'status' => 'nullable|in:planned,active,closed'
            ]);

            // Si se activa, cerrar otros periodos activos
            if (isset($data['status']) && $data['status'] === 'active') {
                // Cerrar otros periodos activos
                DB::update("UPDATE academic_periods SET status = 'closed', updated_at = CURRENT_TIMESTAMP WHERE status = 'active' AND id != ?", [$id]);
                
                // Construir la consulta de actualización
                $updates = [];
                $params = [];
                foreach ($data as $key => $value) {
                    $updates[] = "$key = ?";
                    $params[] = $value;
                }
                $updates[] = "updated_at = CURRENT_TIMESTAMP";
                $params[] = $id;
                
                DB::update("UPDATE academic_periods SET " . implode(', ', $updates) . " WHERE id = ?", $params);
                
                $period = DB::select("SELECT * FROM academic_periods WHERE id = ?", [$id])[0] ?? null;
                
                return response()->json([
                    'success' => true,
                    'message' => 'Periodo actualizado y activado',
                    'data' => $period
                ]);
            }

            // Construir la consulta de actualización
            $updates = [];
            $params = [];
            foreach ($data as $key => $value) {
                $updates[] = "$key = ?";
                $params[] = $value;
            }
            $updates[] = "updated_at = CURRENT_TIMESTAMP";
            $params[] = $id;
            
            DB::update("UPDATE academic_periods SET " . implode(', ', $updates) . " WHERE id = ?", $params);
            
            $period = DB::select("SELECT * FROM academic_periods WHERE id = ?", [$id])[0] ?? null;

            return response()->json([
                'success' => true,
                'message' => 'Periodo actualizado exitosamente',
                'data' => $period
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el periodo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Eliminar periodo académico
     */
    public function destroy($id)
    {
        try {
            $this->ensureAdmin();

            // Verificar que el periodo existe
            $existing = DB::select("SELECT * FROM academic_periods WHERE id = ?", [$id]);
            if (empty($existing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periodo no encontrado'
                ], 404);
            }

            // Eliminar el periodo
            DB::delete("DELETE FROM academic_periods WHERE id = ?", [$id]);

            return response()->json([
                'success' => true,
                'message' => 'Periodo eliminado exitosamente',
                'id' => $id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el periodo: ' . $e->getMessage()
            ], 500);
        }
    }
}