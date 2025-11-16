<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTeacherOrAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verificar si tiene rol de administrador (múltiples variantes)
        $adminRoles = ['administrador', 'ADMINISTRADOR', 'Administrador', 'admin', 'ADMIN', 'Admin', 'administrator', 'ADMINISTRATOR', 'Administrator'];
        $hasAdminRole = $user->roles()->whereIn('name', $adminRoles)->exists();
        
        if ($hasAdminRole) {
            return $next($request);
        }

        // Verificar si tiene rol de docente (múltiples variantes)
        $teacherRoles = ['docente', 'DOCENTE', 'Docente', 'teacher', 'TEACHER', 'Teacher'];
        $hasTeacherRole = $user->roles()->whereIn('name', $teacherRoles)->exists();
        
        if (! $hasTeacherRole) {
            return response()->json(['message' => 'Forbidden - teacher or admin only'], 403);
        }

        return $next($request);
    }
}
