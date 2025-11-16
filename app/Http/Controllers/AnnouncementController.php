<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/announcements",
     *     summary="CU30 - Listar anuncios",
     *     tags={"Anuncios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="active", in="query", @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Lista de anuncios")
     * )
     */
    public function index(Request $request)
    {
        $query = Announcement::query();
        if ($request->has('active')) {
            $active = filter_var($request->query('active'), FILTER_VALIDATE_BOOLEAN);
            if ($active) {
                $query->where(function($q){
                    $now = now();
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
                })->whereNotNull('published_at');
            }
        }
        return response()->json($query->orderBy('pinned','desc')->orderBy('published_at','desc')->get());
    }

    /**
     * @OA\Post(
     *     path="/api/announcements",
     *     summary="CU30 - Publicar anuncio (Admin/Coord)",
     *     tags={"Anuncios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         required={"title","body"},
     *         @OA\Property(property="title", type="string"),
     *         @OA\Property(property="body", type="string"),
     *         @OA\Property(property="expires_at", type="string", format="date-time"),
     *         @OA\Property(property="pinned", type="boolean")
     *     )),
     *     @OA\Response(response=201, description="Anuncio creado")
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'priority' => 'nullable|string|in:low,normal,high',
            'target' => 'nullable|string|in:all,teachers,students',
            'active' => 'nullable|boolean',
            'expires_at' => 'nullable|date',
            'pinned' => 'nullable|boolean',
        ]);
        
        // Mapear 'content' a 'body'
        $data['body'] = $data['content'];
        unset($data['content']);
        
        $data['published_by'] = Auth::id();
        $data['published_at'] = now();
        $data['views'] = 0;
        
        $ann = Announcement::create($data);
        return response()->json($ann, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/announcements/{id}",
     *     summary="Ver anuncio",
     *     tags={"Anuncios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Anuncio")
     * )
     */
    public function show($id)
    {
        $a = Announcement::findOrFail($id);
        return response()->json($a);
    }

    /**
     * @OA\Patch(
     *     path="/api/announcements/{id}",
     *     summary="Actualizar anuncio",
     *     tags={"Anuncios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\RequestBody(@OA\JsonContent(
     *         @OA\Property(property="title", type="string"),
     *         @OA\Property(property="body", type="string"),
     *         @OA\Property(property="expires_at", type="string", format="date-time"),
     *         @OA\Property(property="pinned", type="boolean")
     *     )),
     *     @OA\Response(response=200, description="Anuncio actualizado")
     * )
     */
    public function update(Request $request, $id)
    {
        $a = Announcement::findOrFail($id);
        $data = $request->validate([
            'title' => 'nullable|string',
            'content' => 'nullable|string',
            'priority' => 'nullable|string|in:low,normal,high',
            'target' => 'nullable|string|in:all,teachers,students',
            'active' => 'nullable|boolean',
            'expires_at' => 'nullable|date',
            'pinned' => 'nullable|boolean',
        ]);
        
        // Mapear 'content' a 'body' si existe
        if (isset($data['content'])) {
            $data['body'] = $data['content'];
            unset($data['content']);
        }
        
        $a->update($data);
        return response()->json($a);
    }

    /**
     * @OA\Delete(
     *     path="/api/announcements/{id}",
     *     summary="Eliminar anuncio",
     *     tags={"Anuncios"},
     *     security={{"cookieAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Anuncio eliminado")
     * )
     */
    public function destroy($id)
    {
        $a = Announcement::findOrFail($id);
        $a->delete();
        return response()->json(['message'=>'Deleted','id'=>$id]);
    }
}
