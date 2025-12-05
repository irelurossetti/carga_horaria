<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * Listar todos los recursos
     */
    public function index(Request $request)
    {
        $query = Resource::query();

        // Filtrar por tipo
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filtrar por estado
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar solo disponibles
        if ($request->has('available') && $request->available) {
            $query->available();
        }

        $resources = $query->orderBy('name')->get();

        return response()->json($resources);
    }

    /**
     * Crear un nuevo recurso
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'serial_number' => 'nullable|string|unique:resources,serial_number',
            'type' => 'required|in:Proyector,Laptop,Pizarra Digital,Micrófono,Parlantes,Otro',
            'status' => 'nullable|in:Disponible,En Uso,Mantenimiento,Dañado',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
        ]);

        $resource = Resource::create($data);

        return response()->json($resource, 201);
    }

    /**
     * Mostrar un recurso específico
     */
    public function show($id)
    {
        $resource = Resource::with('reservations.room', 'reservations.teacher')->findOrFail($id);

        return response()->json($resource);
    }

    /**
     * Actualizar un recurso
     */
    public function update(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'serial_number' => 'nullable|string|unique:resources,serial_number,' . $id,
            'type' => 'sometimes|in:Proyector,Laptop,Pizarra Digital,Micrófono,Parlantes,Otro',
            'status' => 'sometimes|in:Disponible,En Uso,Mantenimiento,Dañado',
            'description' => 'nullable|string',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
        ]);

        $resource->update($data);

        return response()->json($resource);
    }

    /**
     * Eliminar un recurso (soft delete)
     */
    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        $resource->delete();

        return response()->json(['message' => 'Recurso eliminado exitosamente']);
    }

    /**
     * Verificar disponibilidad de un recurso en un horario
     */
    public function checkAvailability(Request $request, $id)
    {
        $data = $request->validate([
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $resource = Resource::findOrFail($id);
        $isAvailable = $resource->isAvailableAt($data['start_time'], $data['end_time']);

        return response()->json([
            'resource_id' => $resource->id,
            'resource_name' => $resource->name,
            'is_available' => $isAvailable,
            'current_status' => $resource->status,
        ]);
    }

    /**
     * Obtener recursos disponibles en un horario específico
     */
    public function availableAt(Request $request)
    {
        $data = $request->validate([
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s',
            'type' => 'nullable|in:Proyector,Laptop,Pizarra Digital,Micrófono,Parlantes,Otro',
        ]);

        $query = Resource::available();

        if (!empty($data['type'])) {
            $query->ofType($data['type']);
        }

        $resources = $query->get()->filter(function ($resource) use ($data) {
            return $resource->isAvailableAt($data['start_time'], $data['end_time']);
        });

        return response()->json($resources->values());
    }
}
