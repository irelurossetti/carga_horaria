<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SyllabusTopic;
use App\Models\Subject;

class SyllabusTopicController extends Controller
{
    /**
     * Listar todos los temas de una materia
     */
    public function index(Request $request)
    {
        $query = SyllabusTopic::with('subject');
        
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->query('subject_id'));
        }
        
        return response()->json($query->orderBy('order_index')->get());
    }

    /**
     * Crear un nuevo tema
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'unit_name' => 'required|string|max:255',
            'topic_description' => 'required|string',
            'order_index' => 'nullable|integer'
        ]);

        // Si no se proporciona order_index, asignar el siguiente disponible
        if (!isset($data['order_index'])) {
            $maxOrder = SyllabusTopic::where('subject_id', $data['subject_id'])->max('order_index');
            $data['order_index'] = ($maxOrder ?? 0) + 1;
        }

        $topic = SyllabusTopic::create($data);
        return response()->json($topic->load('subject'), 201);
    }

    /**
     * Ver un tema específico
     */
    public function show($id)
    {
        $topic = SyllabusTopic::with('subject')->findOrFail($id);
        return response()->json($topic);
    }

    /**
     * Actualizar un tema
     */
    public function update(Request $request, $id)
    {
        $topic = SyllabusTopic::findOrFail($id);
        
        $data = $request->validate([
            'unit_name' => 'nullable|string|max:255',
            'topic_description' => 'nullable|string',
            'order_index' => 'nullable|integer'
        ]);

        $topic->update($data);
        return response()->json($topic->load('subject'));
    }

    /**
     * Eliminar un tema
     */
    public function destroy($id)
    {
        $topic = SyllabusTopic::findOrFail($id);
        $topic->delete();
        return response()->json(['message' => 'Topic deleted', 'id' => (int)$id]);
    }

    /**
     * Vista para gestionar temas del sílabo
     */
    public function manage()
    {
        return view('admin.syllabus-topics');
    }
}
