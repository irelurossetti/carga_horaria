<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi Carga Horaria - FICCT SGA</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Instrument Sans','sans-serif']},colors:{brand:{primary:'#881F34',hover:'#6d1829'}}}}}</script>
</head>
<body class="bg-gray-50">
    <!-- Barra superior roja -->
    <nav class="bg-brand-primary text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">FICCT SGA</h1>
                        <p class="text-xs text-white/80">GESTIÓN ACADÉMICA</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('docente.dashboard') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition-colors">
                        ← Volver al Dashboard
                    </a>
                    <span class="text-sm hidden sm:block">
                        {{ Auth::user()->name ?? 'Docente' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 bg-white/10 rounded-full hover:bg-white/20 transition-colors" title="Cerrar Sesión">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Mi Carga Horaria</h1>
            <p class="text-gray-500 mt-1">Visualiza tu carga académica por periodo</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Periodo</label>
                    <select id="periodFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="week">Semanal</option>
                        <option value="month">Mensual</option>
                        <option value="semester">Semestral</option>
                    </select>
                </div>
                @if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('coordinador'))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Docente</label>
                    <select id="teacherFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    </select>
                </div>
                @endif
                <div class="flex items-end">
                    <button onclick="loadWorkload()" class="w-full px-6 py-2 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium">
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Horas Semanales</p>
                        <p id="hoursWeek" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Horas Periodo</p>
                        <p id="hoursPeriod" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Clases</p>
                        <p id="totalClasses" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Periodo</p>
                        <p id="periodLabel" class="text-lg font-bold text-gray-900">Semanal</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución por Día</h3>
                <canvas id="chartByDay"></canvas>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución por Materia</h3>
                <canvas id="chartBySubject"></canvas>
            </div>
        </div>

        <!-- Tabla de Horarios -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Horarios Detallados</h2>
                <div class="flex gap-2">
                    <button onclick="exportPDF()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                        📄 PDF
                    </button>
                    <button onclick="exportExcel()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                        📊 Excel
                    </button>
                </div>
            </div>            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Día</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aula</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Horas</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleTable" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
let chartByDay, chartBySubject;
const API = '/api';

async function loadWorkload() {
    const period = document.getElementById('periodFilter').value;
    const teacherId = document.getElementById('teacherFilter')?.value || {{ $teacher->id }};
    
    try {
        const response = await fetch(`/api/docente/workload?period=${period}&teacher_id=${teacherId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar datos');
        
        const data = await response.json();
        updateStats(data.stats);
        updateCharts(data.stats);
        updateTable(data.schedules);
    } catch (error) {
        console.error(error);
        alert('Error al cargar la carga horaria');
    }
}

function updateStats(stats) {
    document.getElementById('hoursWeek').textContent = stats.total_hours_week;
    document.getElementById('hoursPeriod').textContent = stats.total_hours_period;
    document.getElementById('totalClasses').textContent = stats.total_classes;
    
    const periodLabels = {
        'week': 'Semanal',
        'month': 'Mensual',
        'semester': 'Semestral'
    };
    document.getElementById('periodLabel').textContent = periodLabels[stats.period] || 'Semanal';
}

function updateCharts(stats) {
    // Gráfico por día
    const ctxDay = document.getElementById('chartByDay').getContext('2d');
    if (chartByDay) chartByDay.destroy();
    
    const dayOrder = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    const dayLabels = Object.keys(stats.schedules_by_day).sort((a, b) => dayOrder.indexOf(a) - dayOrder.indexOf(b));
    const dayData = dayLabels.map(day => stats.schedules_by_day[day]);
    
    chartByDay = new Chart(ctxDay, {
        type: 'bar',
        data: {
            labels: dayLabels,
            datasets: [{
                label: 'Horas',
                data: dayData,
                backgroundColor: '#881F34'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    
    // Gráfico por materia
    const ctxSubject = document.getElementById('chartBySubject').getContext('2d');
    if (chartBySubject) chartBySubject.destroy();
    
    const subjectLabels = Object.keys(stats.schedules_by_subject);
    const subjectData = Object.values(stats.schedules_by_subject);
    
    chartBySubject = new Chart(ctxSubject, {
        type: 'pie',
        data: {
            labels: subjectLabels,
            datasets: [{
                data: subjectData,
                backgroundColor: ['#881F34', '#6d1829', '#4a1120', '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
}

function updateTable(schedules) {
    const tbody = document.getElementById('scheduleTable');
    
    if (!schedules.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay horarios</td></tr>';
        return;
    }
    
    tbody.innerHTML = schedules.map(s => {
        const start = s.start_time ? s.start_time.substring(0, 5) : '';
        const end = s.end_time ? s.end_time.substring(0, 5) : '';
        const hours = calculateHours(s.start_time, s.end_time);
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm">${s.day_of_week || 'N/A'}</td>
                <td class="px-6 py-4 text-sm">${start} - ${end}</td>
                <td class="px-6 py-4 text-sm font-medium">${s.subject?.name || 'N/A'}</td>
                <td class="px-6 py-4 text-sm">${s.group?.name || 'N/A'}</td>
                <td class="px-6 py-4 text-sm">${s.room?.name || 'N/A'}</td>
                <td class="px-6 py-4 text-sm text-center">${hours}h</td>
            </tr>
        `;
    }).join('');
}

function calculateHours(start, end) {
    if (!start || !end) return 0;
    const startDate = new Date(`2000-01-01 ${start}`);
    const endDate = new Date(`2000-01-01 ${end}`);
    return Math.round((endDate - startDate) / (1000 * 60 * 60) * 10) / 10;
}

async function exportPDF() {
    const period = document.getElementById('periodFilter').value;
    const teacherId = document.getElementById('teacherFilter')?.value || {{ $teacher->id }};
    window.open(`/api/docente/workload/export-pdf?period=${period}&teacher_id=${teacherId}`, '_blank');
}

async function exportExcel() {
    const period = document.getElementById('periodFilter').value;
    const teacherId = document.getElementById('teacherFilter')?.value || {{ $teacher->id }};
    window.open(`/api/docente/workload/export-excel?period=${period}&teacher_id=${teacherId}`, '_blank');
}

// Cargar al inicio
loadWorkload();

// Recargar al cambiar filtros
document.getElementById('periodFilter').addEventListener('change', loadWorkload);
if (document.getElementById('teacherFilter')) {
    document.getElementById('teacherFilter').addEventListener('change', loadWorkload);
}

// Cargar lista de docentes si es admin/coordinador
@if(auth()->user()->hasRole('administrador') || auth()->user()->hasRole('coordinador'))
async function loadTeachers() {
    try {
        const response = await fetch('/api/teachers', {
            headers: { 'Accept': 'application/json' }
        });
        if (response.ok) {
            const teachers = await response.json();
            const select = document.getElementById('teacherFilter');
            select.innerHTML = teachers.map(t => 
                `<option value="${t.id}" ${t.id == {{ $teacher->id }} ? 'selected' : ''}>${t.name}</option>`
            ).join('');
        }
    } catch (error) {
        console.error('Error al cargar docentes:', error);
    }
}
loadTeachers();
@endif
</script>
</body>
</html>
