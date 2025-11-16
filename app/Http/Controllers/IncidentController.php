<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/incidents",
     *     summary="CU31 - Listar incidencias (admin ve todas, docente las suyas)",
     *     tags={"Incidencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Response(response=200, description="Lista de incidencias")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Incident::query();
        if (! $user->hasRole('administrador')) {
            // docente: only their reported incidents
            $query->where('reported_by', $user->id);
        }
        return response()->json($query->orderBy('created_at','desc')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/incidents",
     *     summary="CU31 - Reportar incidencia en aula",
     *     tags={"Incidencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         required={"description"},
     *         @OA\Property(property="room_id", type="integer"),
     *         @OA\Property(property="description", type="string")
     *     )),
     *     @OA\Response(response=201, description="Incidencia creada")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|integer|exists:rooms,id',
            'type' => 'required|string|in:equipment,infrastructure,cleaning,other',
            'priority' => 'required|string|in:low,medium,high,urgent',
            'description' => 'required|string',
            'status' => 'nullable|string|in:pending,in_progress,resolved',
        ]);
        $data['reported_by'] = Auth::id();
        $data['status'] = $data['status'] ?? 'pending';
        
        $inc = Incident::create($data);
        return response()->json($inc, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/incidents/{id}",
     *     summary="Ver incidencia",
     *     tags={"Incidencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Incidencia encontrada")
     * )
     */
    public function show(Request $request, $id)
    {
        $inc = Incident::findOrFail($id);
        $user = $request->user();
        if (! $user->hasRole('administrador') && $inc->reported_by !== $user->id) {
            return response()->json(['message'=>'Forbidden'], 403);
        }
        return response()->json($inc);
    }

    /**
     * @OA\Patch(
     *     path="/api/incidents/{id}",
     *     summary="Actualizar estado/incidencia (admin/docente que reportó)",
     *     tags={"Incidencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="status", type="string", example="in_progress"),
     *         @OA\Property(property="resolved_by", type="integer"),
     *         @OA\Property(property="resolved_at", type="string", format="date-time")
     *     )),
     *     @OA\Response(response=200, description="Incidencia actualizada")
     * )
     */
    public function update(Request $request, $id)
    {
        $inc = Incident::findOrFail($id);
        $user = $request->user();
        if (! $user->hasRole('administrador') && $inc->reported_by !== $user->id) {
            return response()->json(['message'=>'Forbidden'], 403);
        }
        $data = $request->validate([
            'room_id' => 'nullable|integer|exists:rooms,id',
            'type' => 'nullable|string|in:equipment,infrastructure,cleaning,other',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:pending,in_progress,resolved',
            'resolved_by' => 'nullable|integer',
            'resolved_at' => 'nullable|date',
        ]);
        $inc->update($data);
        return response()->json($inc);
    }

    /**
     * @OA\Delete(
     *     path="/api/incidents/{id}",
     *     summary="Eliminar incidencia",
     *     tags={"Incidencias"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Incidencia eliminada")
     * )
     */
    public function destroy(Request $request, $id)
    {
        $inc = Incident::findOrFail($id);
        $user = $request->user();
        if (! $user->hasRole('administrador') && $inc->reported_by !== $user->id) {
            return response()->json(['message'=>'Forbidden'], 403);
        }
        $inc->delete();
        return response()->json(['message' => 'Incidencia eliminada correctamente']);
    }
}
