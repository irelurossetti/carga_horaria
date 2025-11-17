<aside class="w-64 bg-[#2c4a6b] text-white flex flex-col fixed h-full">
    <div class="p-6 border-b border-blue-900">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-brand-primary rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>
            </div>
            <div><h1 class="font-bold text-sm">FICCT SGA</h1><p class="text-xs text-blue-200">GESTIÓN ACADÉMICA</p></div>
        </div>
    </div>
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mb-2">PRINCIPAL</h3>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            Dashboard
        </a>
        <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mt-6 mb-2">ACADÉMICO</h3>
        <a href="{{ route('periods.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('periods.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Periodos
        </a>
        <a href="{{ route('teachers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('teachers.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Docentes
        </a>
        <a href="{{ route('students.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('students.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Estudiantes
        </a>
        <a href="{{ route('subjects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('subjects.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Materias
        </a>
        <a href="{{ route('groups.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('groups.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Grupos
        </a>
        <a href="{{ route('rooms.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('rooms.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Aulas
        </a>
        <a href="{{ route('imports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('imports.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
            Importar Datos
        </a>
        <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mt-6 mb-2">HORARIOS</h3>
        <a href="{{ route('assignments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('assignments.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            Asignaciones
        </a>
        <a href="{{ route('schedules.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('schedules.index') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Gestión Horarios
        </a>
        <a href="{{ route('weekly-schedule.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('weekly-schedule.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Horario Semanal
        </a>
        @if(auth()->check() && (auth()->user()->hasRole('docente') || auth()->user()->hasRole('administrador') || auth()->user()->hasRole('coordinador')))
        <a href="{{ route('docente.workload') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('docente.workload') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Carga Horaria
        </a>
        @endif
        <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('attendance.index') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Asistencia
        </a>
        <a href="{{ route('attendance-qr.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('attendance-qr.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            Asistencia QR
        </a>
        <a href="{{ route('cancellations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('cancellations.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Anular Clases
        </a>
        <a href="{{ route('conflicts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('conflicts.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Conflictos
        </a>
        <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mt-6 mb-2">AULAS</h3>
        <a href="{{ route('available-rooms.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('available-rooms.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Consultar Disponibles
        </a>
        <a href="{{ route('reservations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('reservations.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Reservar Aulas
        </a>
        <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mt-6 mb-2">REPORTES</h3>
        <a href="{{ route('attendance-teacher.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('attendance-teacher.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Asistencia Docente
        </a>
        <a href="{{ route('attendance-group.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('attendance-group.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Asistencia Grupo
        </a>
        <a href="{{ route('workload.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('workload.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Carga Horaria
        </a>
        <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mt-6 mb-2">COMUNICACIÓN</h3>
        <a href="{{ route('announcements.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('announcements.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
            Anuncios
        </a>
        <a href="{{ route('incidents.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('incidents.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            Incidencias
        </a>
        <h3 class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider mt-6 mb-2">SISTEMA</h3>
        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Reportes
        </a>
        <a href="{{ route('activity-log.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('activity-log.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Bitácora
        </a>
        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('settings.*') ? 'bg-brand-primary text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }} transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Configuración
        </a>
    </nav>
    <div class="p-4 border-t border-blue-900">
        <div class="flex items-center gap-3 px-3 py-2">
            <div class="w-8 h-8 bg-brand-primary rounded-full flex items-center justify-center">
                <span class="text-sm font-bold">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ Auth::user()->name ?? 'Administrador' }}</p>
                <p class="text-xs text-blue-200 truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="w-full text-left px-3 py-2 text-sm text-blue-100 hover:bg-blue-700 hover:text-white rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Cerrar Sesión
            </button>
        </form>
    </div>
</aside>
