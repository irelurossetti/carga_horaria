<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Forbidden - admin only'], 403);
        }
        
        // Verificar si tiene rol de administrador (múltiples variantes)
        $adminRoles = ['administrador', 'ADMINISTRADOR', 'Administrador', 'admin', 'ADMIN', 'Admin', 'administrator', 'ADMINISTRATOR', 'Administrator'];
        $hasAdminRole = $user->roles()->whereIn('name', $adminRoles)->exists();
        
        if (! $hasAdminRole) {
            return response()->json(['message' => 'Forbidden - admin only'], 403);
        }
        
        return $next($request);
    }
}
