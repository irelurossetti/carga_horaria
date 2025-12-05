<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FICCT SGA') - Panel Administrativo</title>
    
    <!-- Fuentes y estilos -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 
                        sans: ['Instrument Sans', 'sans-serif'] 
                    },
                    colors: {
                        brand: { 
                            primary: '#881F34', 
                            hover: '#6d1829',
                            light: '#FDF2F4'
                        },
                        neutral: { 
                            50: '#FAFAFA', 100: '#F5F5F5', 200: '#E5E5E5', 
                            300: '#D4D4D4', 400: '#A3A3A3', 500: '#737373', 
                            600: '#525252', 700: '#404040', 800: '#262626', 
                            900: '#171717' 
                        }
                    },
                    boxShadow: {
                        'card': '0 0 0 1px rgba(0,0,0,0.03), 0 2px 8px -2px rgba(0,0,0,0.05)',
                    }
                }
            }
        }
    </script>
    
    <style>
        .sidebar-item { 
            transition: all 0.2s ease; 
            border-radius: 8px; 
            margin-bottom: 4px; 
            color: rgba(191, 219, 254, 0.9); 
        }
        .sidebar-item:hover { 
            background-color: rgba(37, 99, 235, 0.5); 
            color: #FFFFFF; 
        }
        .sidebar-item.active { 
            background-color: #881F34; 
            color: #FFFFFF; 
            font-weight: 600; 
        }
        /* Scrollbar personalizado */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #D4D4D4; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #A3A3A3; }
    </style>
</head>
@stack('scripts')
<body class="h-full font-sans antialiased text-neutral-900 bg-neutral-100">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Overlay para móvil -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>
        
        <!-- ===== SIDEBAR ===== -->
        <div id="sidebar" class="w-72 bg-[#2c4a6b] flex flex-col text-white flex-shrink-0 relative z-50 fixed lg:relative h-full transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
            <!-- Logo -->
            <div class="h-16 flex items-center px-6 border-b border-blue-900">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-brand-primary flex items-center justify-center shadow-lg shadow-brand-primary/20">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-base leading-none tracking-wide">FICCT SGA</h1>
                        <span class="text-[11px] font-medium text-blue-200 uppercase tracking-wider">Gestión Académica</span>
                    </div>
                </div>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                <div class="text-xs font-semibold text-blue-200 uppercase tracking-wider mb-3 px-2">Principal</div>
                
                <a href="{{ route('dashboard') }}" class="sidebar-item @if(request()->routeIs('dashboard')) active @endif flex items-center px-3 py-2.5 gap-3">
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>

                <div class="text-xs font-semibold text-blue-200 uppercase tracking-wider mt-8 mb-3 px-2">Académico</div>

                <a href="{{ route('periods.index') }}" class="sidebar-item @if(request()->routeIs('periods.index')) active @endif flex items-center px-3 py-2.5 gap-3">
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-medium">Periodos</span>
                </a>
                
                <a href="#" class="sidebar-item flex items-center px-3 py-2.5 gap-3">
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <span class="text-sm font-medium">Docentes</span>
                </a>
                
                <a href="#" class="sidebar-item flex items-center px-3 py-2.5 gap-3">
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="text-sm font-medium">Estudiantes</span>
                </a>
                
                <a href="#" class="sidebar-item flex items-center px-3 py-2.5 gap-3">
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-sm font-medium">Aulas</span>
                </a>

                <div class="text-xs font-semibold text-blue-200 uppercase tracking-wider mt-8 mb-3 px-2">Sistema</div>
                
                <a href="#" class="sidebar-item flex items-center px-3 py-2.5 gap-3">
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm font-medium">Reportes</span>
                </a>
                
                <a href="#" class="sidebar-item flex items-center px-3 py-2.5 gap-3">
                    <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-sm font-medium">Configuración</span>
                </a>
            </nav>

            <!-- Footer del sidebar -->
            <div class="p-4 border-t border-blue-900">
                <div class="flex items-center gap-3 px-2 py-2">
                    <div class="w-8 h-8 rounded-full bg-brand-primary flex items-center justify-center text-sm font-bold border-2 border-[#2c4a6b]">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-medium truncate text-white">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs text-blue-200 hover:text-white truncate text-left w-full">Cerrar Sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== CONTENIDO PRINCIPAL ===== -->
        <div class="flex-1 flex flex-col overflow-hidden bg-neutral-50 w-full">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-neutral-200/80 flex items-center justify-between px-4 sm:px-6 lg:px-8 flex-shrink-0">
                <!-- Botón menú móvil -->
                <button id="menuButton" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                
                <h2 class="text-base sm:text-lg font-semibold text-neutral-900 truncate">@yield('header', 'Panel Administrativo')</h2>
                
                <div class="flex items-center gap-2 sm:gap-4">
                    <span class="text-xs sm:text-sm text-neutral-500 font-medium hidden sm:inline">
                        {{ date('d') }} de {{ ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'][date('n')-1] }}, {{ date('Y') }}
                    </span>
                </div>
            </header>

            <!-- Contenido de la página -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
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