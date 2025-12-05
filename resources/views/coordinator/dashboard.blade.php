<!DOCTYPE html>
<html lang="es" class="h-full bg-[#F5F5F5]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - FICCT SGA</title>

    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Instrument Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            primary: '#881F34',
                            hover: '#6d1829'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="h-full font-sans antialiased text-neutral-900 bg-[#F5F5F5]">

    <div class="flex h-screen overflow-hidden">
        <!-- Overlay para móvil -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
        
        <!-- Sidebar -->
        <div id="sidebar" class="w-64 bg-brand-primary flex flex-col text-white flex-shrink-0 fixed lg:relative h-full transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50">
            <div class="h-16 flex items-center px-6 border-b border-white/10">
                <span class="font-bold text-lg tracking-tight">FICCT <span class="opacity-80 font-normal">SGA</span></span>
            </div>
            <nav class="flex-1 py-6 px-3 space-y-1 overflow-y-auto">
                <a href="#" class="flex items-center px-3 py-2.5 gap-3 bg-white/10 rounded-lg text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    <span class="text-sm font-medium">Panel Control</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 gap-3 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <span class="text-sm font-medium">Programación</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 gap-3 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    <span class="text-sm font-medium">Gestión Aulas</span>
                </a>
                <a href="#" class="flex items-center px-3 py-2.5 gap-3 text-white/70 hover:text-white hover:bg-white/5 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <span class="text-sm font-medium">Conflictos</span>
                    @if(($conflictsCount ?? 0) > 0)
                        <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $conflictsCount }}</span>
                    @endif
                </a>
            </nav>
            <div class="p-4 border-t border-white/10 mt-auto">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-sm font-bold">C</div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-medium truncate">Coordinador</p>
                    </div>
                     <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-white/50 hover:text-white p-1.5">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <main class="flex-1 overflow-y-auto w-full">
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-10">
                <!-- Botón menú móvil -->
                <button id="menuButton" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <h1 class="text-base sm:text-xl font-semibold text-gray-800">Panel de Coordinación</h1>
                
                <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 bg-gray-50 px-2 sm:px-3 py-1.5 rounded-full">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="hidden sm:inline">Periodo II-2025 Activo</span>
                    <span class="sm:hidden">Activo</span>
                </div>
            </header>

            <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6 sm:space-y-8">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Choques de Horario</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $conflictsCount ?? 0 }}</p>
                            </div>
                            <div class="p-2 sm:p-3 bg-red-50 rounded-lg">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Sin Aula Asignada</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $schedulesWithoutRoom ?? 0 }}</p>
                            </div>
                            <div class="p-2 sm:p-3 bg-amber-50 rounded-lg">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Aulas Libres Hoy</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $freeRoomsToday ?? 0 }}</p>
                            </div>
                            <div class="p-2 sm:p-3 bg-blue-50 rounded-lg">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-xs sm:text-sm font-medium text-gray-500">Avance Programación</p>
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1 sm:mt-2">{{ $programmingProgress ?? 0 }}%</p>
                            </div>
                            <div class="p-2 sm:p-3 bg-green-50 rounded-lg">
                               <svg class="w-6 h-6 sm:w-8 sm:h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-card border border-gray-200 overflow-hidden">
                        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between bg-red-50/30">
                            <h3 class="text-sm sm:text-base font-semibold text-red-900 flex items-center gap-2">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <span class="hidden sm:inline">Conflictos que requieren atención</span>
                                <span class="sm:hidden">Conflictos</span>
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div class="p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50">
                                <div class="flex-1">
                                    <p class="text-sm sm:text-base font-medium text-gray-900">Choque de Aula: 236-21</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                        <span class="font-semibold">INF110-SC</span> (Gomez) vs <span class="font-semibold">MAT101-SA</span> (Perez)
                                    </p>
                                    <p class="text-xs text-red-500 mt-1">Lun 10:00 - 11:30</p>
                                </div>
                                <button class="px-3 py-1.5 bg-white border border-gray-300 text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors self-start sm:self-auto">
                                    Resolver
                                </button>
                            </div>
                             <div class="p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-gray-50">
                                <div class="flex-1">
                                    <p class="text-sm sm:text-base font-medium text-gray-900">Docente con cruce de horario</p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                        Ing. Martinez tiene 2 grupos asignados al mismo tiempo.
                                    </p>
                                    <p class="text-xs text-red-500 mt-1">Mie 18:15 - 19:45</p>
                                </div>
                                <button class="px-3 py-1.5 bg-white border border-gray-300 text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors self-start sm:self-auto">
                                    Resolver
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 sm:space-y-6">
                        <!-- Progreso del Sílabo -->
                        <div class="bg-white rounded-xl shadow-card border border-gray-200 p-4 sm:p-6">
                            <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Progreso del Sílabo
                            </h3>
                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                @if(isset($groupsWithProgress) && count($groupsWithProgress) > 0)
                                    @foreach($groupsWithProgress as $group)
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900">{{ $group['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $group['subject'] }}</p>
                                                </div>
                                                <span class="text-xs font-semibold {{ $group['progress'] >= 70 ? 'text-green-600' : ($group['progress'] >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                                    {{ $group['progress'] }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all {{ $group['progress'] >= 70 ? 'bg-green-600' : ($group['progress'] >= 40 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                                     style="width: {{ $group['progress'] }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-500 text-center py-4">No hay grupos con progreso registrado</p>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-card border border-gray-200 p-4 sm:p-6">
                            <h3 class="text-sm sm:text-base font-semibold text-gray-900 mb-3 sm:mb-4">Gestión Rápida</h3>
                            <div class="space-y-3">
                                <button class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-primary/5 hover:text-brand-primary transition-colors border border-transparent hover:border-brand-primary/20 group">
                                    <span>Asignar Aula Manualmente</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                                <button class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-primary/5 hover:text-brand-primary transition-colors border border-transparent hover:border-brand-primary/20 group">
                                    <span>Generar Reporte de Carga</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                                <button class="w-full flex items-center justify-between p-3 bg-gray-50 rounded-lg text-sm font-medium text-gray-700 hover:bg-brand-primary/5 hover:text-brand-primary transition-colors border border-transparent hover:border-brand-primary/20 group">
                                    <span>Habilitar Nuevo Grupo</span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
    
    <!-- Script para menú móvil -->
    <script>
        const menuButton = document.getElementById('menuButton');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        
        menuButton?.addEventListener('click', toggleSidebar);
        overlay?.addEventListener('click', toggleSidebar);
        
        // Cerrar sidebar al hacer clic en un enlace (solo móvil)
        if (window.innerWidth < 1024) {
            document.querySelectorAll('#sidebar a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) {
                        toggleSidebar();
                    }
                });
            });
        }
    </script>
</body>
</html>
