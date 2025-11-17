<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reportes del Sistema - FICCT SGA</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Instrument Sans', 'sans-serif'] },
                    colors: { brand: { primary: '#881F34', hover: '#6d1829' } }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50">
<div class="flex min-h-screen">
    @include('layouts.admin-sidebar')

    <main class="flex-1 ml-64 p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Reportes del Sistema</h1>
            <p class="text-gray-500 mt-1">Genera y visualiza reportes académicos</p>
        </div>

        <!-- Report Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Carga Horaria -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showReport('teacher-workload')">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Carga Horaria</h3>
                        <p class="text-sm text-gray-500">Por docente</p>
                    </div>
                </div>
            </div>

            <!-- Asistencia Docente -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showReport('teacher-attendance')">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Asistencia Docente</h3>
                        <p class="text-sm text-gray-500">Control de asistencia</p>
                    </div>
                </div>
            </div>

            <!-- Horarios Semanales -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showReport('weekly-schedule')">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Horarios Semanales</h3>
                        <p class="text-sm text-gray-500">Vista semanal</p>
                    </div>
                </div>
            </div>

            <!-- Aulas Disponibles -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showReport('available-rooms')">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Aulas Disponibles</h3>
                        <p class="text-sm text-gray-500">Disponibilidad</p>
                    </div>
                </div>
            </div>

            <!-- Asistencia por Materia -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showReport('subject-attendance')">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Asistencia por Materia</h3>
                        <p class="text-sm text-gray-500">Por materia</p>
                    </div>
                </div>
            </div>

            <!-- Asistencia por Grupo -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showReport('group-attendance')">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Asistencia por Grupo</h3>
                        <p class="text-sm text-gray-500">Por grupo</p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Generales -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showReport('general-stats')">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Estadísticas Generales</h3>
                        <p class="text-sm text-gray-500">Resumen del sistema</p>
                    </div>
                </div>
            </div>

            <!-- NUEVO: Reporte de Ausencias -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showReport('absences')">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Reporte de Ausencias</h3>
                        <p class="text-sm text-gray-500">Faltas de docentes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Content Area -->
        <div id="reportContent" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hidden">
            <div class="flex items-center justify-between mb-6">
                <h2 id="reportTitle" class="text-xl font-bold text-gray-900"></h2>
                <div class="flex gap-2">
                    <button onclick="exportReport('pdf')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        PDF
                    </button>
                    <button onclick="exportReport('excel')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel
                    </button>
                    <button onclick="closeReport()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
            
            <!-- Filtros Dinámicos -->
            <div id="reportFilters" class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">🔍 Filtros Dinámicos</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Período Académico -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Período Académico</label>
                        <select id="filterPeriod" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="">Todos los períodos</option>
                        </select>
                    </div>
                    
                    <!-- Rango de Fechas -->
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha Inicio</label>
                        <input type="date" id="filterDateStart" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha Fin</label>
                        <input type="date" id="filterDateEnd" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                    </div>
                    
                    <!-- Filtro específico según tipo de reporte -->
                    <div id="specificFilter"></div>
                </div>
                
                <div class="flex gap-2 mt-4">
                    <button onclick="applyFilters()" class="px-4 py-2 bg-brand-primary hover:bg-brand-hover text-white rounded-lg text-sm font-medium transition-colors">
                        Aplicar Filtros
                    </button>
                    <button onclick="clearFilters()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors">
                        Limpiar
                    </button>
                </div>
            </div>
            
            <!-- Data Display -->
            <div id="reportData"></div>
        </div>
    </main>
</div>


<script>
const API_BASE = '/api';
let currentReport = null;

function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg border ${alertClass} z-50 shadow-lg`;
    notification.innerHTML = `<span class="font-medium">${message}</span>`;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

async function showReport(reportType) {
    currentReport = reportType;
    const content = document.getElementById('reportContent');
    const title = document.getElementById('reportTitle');
    const data = document.getElementById('reportData');
    
    content.classList.remove('hidden');
    
    const titles = {
        'teacher-workload': 'Reporte de Carga Horaria por Docente',
        'teacher-attendance': 'Reporte de Asistencia Docente',
        'weekly-schedule': 'Horarios Semanales',
        'available-rooms': 'Aulas Disponibles',
        'subject-attendance': 'Asistencia por Materia',
        'group-attendance': 'Asistencia por Grupo',
        'general-stats': 'Estadísticas Generales del Sistema',
        'absences': 'Reporte de Ausencias'
    };
    
    title.textContent = titles[reportType] || 'Reporte';
    
    // Cargar períodos académicos
    await loadAcademicPeriods();
    
    // Configurar filtro específico según tipo de reporte
    setupSpecificFilter(reportType);
    
    // Show loading
    data.innerHTML = '<div class="text-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-primary mx-auto"></div><p class="mt-4 text-gray-500">Cargando datos...</p></div>';
    
    // Cargar datos iniciales
    await loadReportData();
}

async function loadAcademicPeriods() {
    try {
        const response = await fetch(`${API_BASE}/academic-periods`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (response.ok) {
            const periods = await response.json();
            const select = document.getElementById('filterPeriod');
            select.innerHTML = '<option value="">Todos los períodos</option>';
            
            periods.forEach(period => {
                const option = document.createElement('option');
                option.value = period.id;
                option.textContent = `${period.name} (${period.code || ''})`;
                if (period.is_active) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar períodos:', error);
    }
}

function setupSpecificFilter(reportType) {
    const container = document.getElementById('specificFilter');
    
    if (reportType === 'teacher-workload' || reportType === 'teacher-attendance' || reportType === 'absences') {
        container.innerHTML = `
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Docente</label>
                <select id="filterTeacher" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                    <option value="">Todos los docentes</option>
                </select>
            </div>
        `;
        loadTeachersForFilter();
    } else if (reportType === 'subject-attendance') {
        container.innerHTML = `
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Materia</label>
                <select id="filterSubject" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                    <option value="">Todas las materias</option>
                </select>
            </div>
        `;
        loadSubjectsForFilter();
    } else if (reportType === 'group-attendance') {
        container.innerHTML = `
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Grupo</label>
                <select id="filterGroup" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                    <option value="">Todos los grupos</option>
                </select>
            </div>
        `;
        loadGroupsForFilter();
    } else {
        container.innerHTML = '';
    }
}

async function loadTeachersForFilter() {
    try {
        const response = await fetch(`${API_BASE}/teachers`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (response.ok) {
            const teachers = await response.json();
            const select = document.getElementById('filterTeacher');
            
            teachers.forEach(teacher => {
                const option = document.createElement('option');
                option.value = teacher.id;
                option.textContent = teacher.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar docentes:', error);
    }
}

async function loadSubjectsForFilter() {
    try {
        const response = await fetch(`${API_BASE}/subjects`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (response.ok) {
            const subjects = await response.json();
            const select = document.getElementById('filterSubject');
            
            subjects.forEach(subject => {
                const option = document.createElement('option');
                option.value = subject.id;
                option.textContent = `${subject.name} (${subject.code || ''})`;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar materias:', error);
    }
}

async function loadGroupsForFilter() {
    try {
        const response = await fetch(`${API_BASE}/groups`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (response.ok) {
            const groups = await response.json();
            const select = document.getElementById('filterGroup');
            
            groups.forEach(group => {
                const option = document.createElement('option');
                option.value = group.id;
                option.textContent = group.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar grupos:', error);
    }
}

async function applyFilters() {
    await loadReportData();
}

function clearFilters() {
    document.getElementById('filterPeriod').value = '';
    document.getElementById('filterDateStart').value = '';
    document.getElementById('filterDateEnd').value = '';
    
    const teacherFilter = document.getElementById('filterTeacher');
    const subjectFilter = document.getElementById('filterSubject');
    const groupFilter = document.getElementById('filterGroup');
    
    if (teacherFilter) teacherFilter.value = '';
    if (subjectFilter) subjectFilter.value = '';
    if (groupFilter) groupFilter.value = '';
    
    loadReportData();
}

async function loadReportData() {
    const data = document.getElementById('reportData');
    data.innerHTML = '<div class="text-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-primary mx-auto"></div><p class="mt-4 text-gray-500">Cargando datos...</p></div>';
    
    try {
        // Construir URL con filtros
        let url = `${API_BASE}/reports/${currentReport}?`;
        
        const period = document.getElementById('filterPeriod').value;
        const dateStart = document.getElementById('filterDateStart').value;
        const dateEnd = document.getElementById('filterDateEnd').value;
        
        if (period) url += `period_id=${period}&`;
        if (dateStart) url += `date_start=${dateStart}&`;
        if (dateEnd) url += `date_end=${dateEnd}&`;
        
        // Filtros específicos
        const teacherFilter = document.getElementById('filterTeacher');
        const subjectFilter = document.getElementById('filterSubject');
        const groupFilter = document.getElementById('filterGroup');
        
        if (teacherFilter && teacherFilter.value) url += `teacher_id=${teacherFilter.value}&`;
        if (subjectFilter && subjectFilter.value) url += `subject_id=${subjectFilter.value}&`;
        if (groupFilter && groupFilter.value) url += `group_id=${groupFilter.value}&`;
        
        const response = await fetch(url, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            throw new Error('Error al cargar el reporte');
        }
        
        const result = await response.json();
        console.log('Report data:', result);
        renderReport(currentReport, result.data);
    } catch (error) {
        console.error('Error completo:', error);
        data.innerHTML = `<div class="text-center py-12 text-red-600">Error al cargar el reporte: ${error.message}<br><small>Revisa la consola para más detalles</small></div>`;
    }
}

function renderReport(type, data) {
    const dataDiv = document.getElementById('reportData');
    
    if (!data || (Array.isArray(data) && data.length === 0)) {
        dataDiv.innerHTML = '<div class="text-center py-12 text-gray-500">No hay datos disponibles</div>';
        return;
    }
    
    let html = '';
    
    switch(type) {
        case 'teacher-workload':
            html = renderWorkloadReport(data);
            break;
        case 'teacher-attendance':
            html = renderTeacherAttendanceReport(data);
            break;
        case 'weekly-schedule':
            html = renderWeeklyScheduleReport(data);
            break;
        case 'available-rooms':
            html = renderAvailableRoomsReport(data);
            break;
        case 'subject-attendance':
            html = renderSubjectAttendanceReport(data);
            break;
        case 'group-attendance':
            html = renderGroupAttendanceReport(data);
            break;
        case 'general-stats':
            html = renderGeneralStatsReport(data);
            break;
        case 'absences':
            html = renderAbsencesReport(data);
            break;
    }
    
    dataDiv.innerHTML = html;
    
    // Renderizar gráficos después de insertar el HTML
    setTimeout(() => {
        if (type === 'teacher-workload') {
            renderWorkloadChart(data);
        } else if (type === 'teacher-attendance') {
            renderAttendanceChart(data);
        } else if (type === 'subject-attendance') {
            renderSubjectAttendanceChart(data);
        } else if (type === 'group-attendance') {
            renderGroupAttendanceChart(data);
        } else if (type === 'general-stats') {
            renderGeneralStatsCharts(data);
        } else if (type === 'absences') {
            renderAbsencesChart(data);
        }
    }, 100);
}


function renderWorkloadReport(data) {
    return `
        <!-- Gráfico de Carga Horaria -->
        <div class="mb-8 bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Distribución de Carga Horaria</h3>
            <div class="bg-white p-4 rounded-lg">
                <canvas id="workloadChart" height="80"></canvas>
            </div>
        </div>

        <!-- Tabla de Datos -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Asignaciones</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Horarios</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Horas Totales</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    ${data.map(row => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">${row.teacher_name}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${row.email}</td>
                            <td class="px-6 py-4 text-center">${row.total_assignments || 0}</td>
                            <td class="px-6 py-4 text-center">${row.total_schedules || 0}</td>
                            <td class="px-6 py-4 text-center font-bold text-brand-primary">${parseFloat(row.total_hours || 0).toFixed(2)} hrs</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Función para renderizar el gráfico de carga horaria
function renderWorkloadChart(data) {
    const ctx = document.getElementById('workloadChart');
    if (!ctx) return;
    
    // Destruir gráfico anterior si existe
    if (window.workloadChartInstance) {
        window.workloadChartInstance.destroy();
    }
    
    const labels = data.map(d => d.teacher_name);
    const hours = data.map(d => parseFloat(d.total_hours || 0));
    
    window.workloadChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Horas Totales',
                data: hours,
                backgroundColor: '#881F34',
                borderColor: '#6d1829',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' horas';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Horas'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Docentes'
                    }
                }
            }
        }
    });
}

function renderTeacherAttendanceReport(data) {
    return `
        <!-- Gráfico de Asistencia -->
        <div class="mb-8 bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Tendencia de Asistencia</h3>
            <div class="bg-white p-4 rounded-lg">
                <canvas id="attendanceChart" height="80"></canvas>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Registros</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Presentes</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ausentes</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tardanzas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">% Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    ${data.map(row => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">${row.teacher_name}</td>
                            <td class="px-6 py-4 text-center">${row.total_records || 0}</td>
                            <td class="px-6 py-4 text-center text-green-600">${row.present_count || 0}</td>
                            <td class="px-6 py-4 text-center text-red-600">${row.absent_count || 0}</td>
                            <td class="px-6 py-4 text-center text-orange-600">${row.late_count || 0}</td>
                            <td class="px-6 py-4 text-center font-bold">${row.attendance_percentage || 0}%</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function renderWeeklyScheduleReport(data) {
    return `
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Día</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aula</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    ${data.map(row => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">${row.day}</td>
                            <td class="px-4 py-3">${row.start_time} - ${row.end_time}</td>
                            <td class="px-4 py-3">${row.room_name} (${row.location})</td>
                            <td class="px-4 py-3">${row.teacher_name || '-'}</td>
                            <td class="px-4 py-3">${row.subject_name || '-'}</td>
                            <td class="px-4 py-3">${row.group_name || '-'}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}


function renderAvailableRoomsReport(data) {
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            ${data.map(room => `
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <h3 class="font-bold text-lg">${room.name}</h3>
                    <p class="text-sm text-gray-600">${room.location}</p>
                    <p class="text-sm mt-2">Capacidad: <span class="font-semibold">${room.capacity} personas</span></p>
                    <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">Disponible</span>
                </div>
            `).join('')}
        </div>
    `;
}

function renderSubjectAttendanceReport(data) {
    return `
        <!-- Gráfico de Asistencia por Materia -->
        <div class="mb-8 bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Asistencia por Materia</h3>
            <div class="bg-white p-4 rounded-lg">
                <canvas id="subjectAttendanceChart" height="80"></canvas>
            </div>
        </div>

        <!-- Tabla de Asistencia por Materia -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Clases</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Presentes</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ausentes</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">% Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    ${data.map(row => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">${row.subject_name}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">${row.subject_code || 'N/A'}</td>
                            <td class="px-6 py-4 text-center">${row.total_classes || 0}</td>
                            <td class="px-6 py-4 text-center text-green-600">${row.present_count || 0}</td>
                            <td class="px-6 py-4 text-center text-red-600">${row.absent_count || 0}</td>
                            <td class="px-6 py-4 text-center font-bold">${row.attendance_percentage || 0}%</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function renderGroupAttendanceReport(data) {
    return `
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Días Registrados</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Registros</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Presentes</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">% Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    ${data.map(row => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">${row.group_name}</td>
                            <td class="px-6 py-4 text-center">${row.total_days || 0}</td>
                            <td class="px-6 py-4 text-center">${row.total_records || 0}</td>
                            <td class="px-6 py-4 text-center text-green-600">${row.present_count || 0}</td>
                            <td class="px-6 py-4 text-center font-bold">${row.attendance_percentage || 0}%</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
}

function renderGeneralStatsReport(data) {
    return `
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                <div class="text-3xl font-bold text-blue-600">${data.total_teachers || 0}</div>
                <div class="text-sm text-blue-800 mt-1">Total Docentes</div>
            </div>
            <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                <div class="text-3xl font-bold text-green-600">${data.total_students || 0}</div>
                <div class="text-sm text-green-800 mt-1">Total Estudiantes</div>
            </div>
            <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
                <div class="text-3xl font-bold text-purple-600">${data.total_rooms || 0}</div>
                <div class="text-sm text-purple-800 mt-1">Total Aulas</div>
            </div>
            <div class="bg-orange-50 rounded-lg p-6 border border-orange-200">
                <div class="text-3xl font-bold text-orange-600">${data.total_subjects || 0}</div>
                <div class="text-sm text-orange-800 mt-1">Total Materias</div>
            </div>
            <div class="bg-red-50 rounded-lg p-6 border border-red-200">
                <div class="text-3xl font-bold text-red-600">${data.total_groups || 0}</div>
                <div class="text-sm text-red-800 mt-1">Total Grupos</div>
            </div>
            ${data.period_assignments ? `
            <div class="bg-indigo-50 rounded-lg p-6 border border-indigo-200">
                <div class="text-3xl font-bold text-indigo-600">${data.period_assignments}</div>
                <div class="text-sm text-indigo-800 mt-1">Asignaciones del Período</div>
            </div>
            ` : ''}
        </div>
    `;
}

// Función para renderizar reporte de ausencias
function renderAbsencesReport(data) {
    return `
        <!-- Gráfico de Ausencias -->
        <div class="mb-8 bg-gray-50 p-6 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Ausencias por Docente</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-4 rounded-lg">
                    <canvas id="absencesBarChart" height="200"></canvas>
                </div>
                <div class="bg-white p-4 rounded-lg">
                    <canvas id="absencesPieChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="text-sm text-red-600 font-medium">Total Ausencias</div>
                <div class="text-2xl font-bold text-red-700">${data.reduce((sum, d) => sum + (d.absences || 0), 0)}</div>
            </div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="text-sm text-yellow-600 font-medium">Docentes con Ausencias</div>
                <div class="text-2xl font-bold text-yellow-700">${data.filter(d => d.absences > 0).length}</div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="text-sm text-green-600 font-medium">Asistencias Totales</div>
                <div class="text-2xl font-bold text-green-700">${data.reduce((sum, d) => sum + (d.attendances || 0), 0)}</div>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="text-sm text-blue-600 font-medium">Promedio Asistencia</div>
                <div class="text-2xl font-bold text-blue-700">${(data.reduce((sum, d) => sum + (d.attendance_rate || 0), 0) / data.length).toFixed(1)}%</div>
            </div>
        </div>

        <!-- Tabla de Ausencias -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Asistencias</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ausencias</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Clases</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">% Asistencia</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    ${data.map(row => {
                        const rate = row.attendance_rate || 0;
                        const statusColor = rate >= 90 ? 'green' : rate >= 75 ? 'yellow' : 'red';
                        const statusText = rate >= 90 ? 'Excelente' : rate >= 75 ? 'Bueno' : 'Crítico';
                        return `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">${row.teacher_name}</td>
                            <td class="px-6 py-4 text-center text-green-600 font-semibold">${row.attendances || 0}</td>
                            <td class="px-6 py-4 text-center text-red-600 font-semibold">${row.absences || 0}</td>
                            <td class="px-6 py-4 text-center">${row.total_classes || 0}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-${statusColor}-600 h-2 rounded-full" style="width: ${rate}%"></div>
                                    </div>
                                    <span class="font-semibold">${rate.toFixed(1)}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 bg-${statusColor}-100 text-${statusColor}-800 rounded-full text-xs font-medium">
                                    ${statusText}
                                </span>
                            </td>
                        </tr>
                    `}).join('')}
                </tbody>
            </table>
        </div>
    `;
}

// Función para renderizar gráficos de ausencias
function renderAbsencesChart(data) {
    // Gráfico de barras
    const barCtx = document.getElementById('absencesBarChart');
    if (barCtx) {
        if (window.absencesBarChartInstance) {
            window.absencesBarChartInstance.destroy();
        }
        
        window.absencesBarChartInstance = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.teacher_name),
                datasets: [
                    {
                        label: 'Asistencias',
                        data: data.map(d => d.attendances || 0),
                        backgroundColor: '#10b981',
                        borderColor: '#059669',
                        borderWidth: 1
                    },
                    {
                        label: 'Ausencias',
                        data: data.map(d => d.absences || 0),
                        backgroundColor: '#ef4444',
                        borderColor: '#dc2626',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Asistencias vs Ausencias'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        }
                    }
                }
            }
        });
    }
    
    // Gráfico de pastel
    const pieCtx = document.getElementById('absencesPieChart');
    if (pieCtx) {
        if (window.absencesPieChartInstance) {
            window.absencesPieChartInstance.destroy();
        }
        
        const totalAttendances = data.reduce((sum, d) => sum + (d.attendances || 0), 0);
        const totalAbsences = data.reduce((sum, d) => sum + (d.absences || 0), 0);
        
        window.absencesPieChartInstance = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Asistencias', 'Ausencias'],
                datasets: [{
                    data: [totalAttendances, totalAbsences],
                    backgroundColor: ['#10b981', '#ef4444'],
                    borderColor: ['#059669', '#dc2626'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Distribución Total'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = totalAttendances + totalAbsences;
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
}

// Función para renderizar gráfico de asistencia de docentes
function renderAttendanceChart(data) {
    const ctx = document.getElementById('attendanceChart');
    if (!ctx) return;
    
    if (window.attendanceChartInstance) {
        window.attendanceChartInstance.destroy();
    }
    
    window.attendanceChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => d.teacher_name),
            datasets: [{
                label: '% Asistencia',
                data: data.map(d => d.attendance_rate || 0),
                borderColor: '#881F34',
                backgroundColor: 'rgba(136, 31, 52, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Porcentaje (%)'
                    }
                }
            }
        }
    });
}

// Función para renderizar gráfico de asistencia por materia
function renderSubjectAttendanceChart(data) {
    const ctx = document.getElementById('subjectAttendanceChart');
    if (!ctx) return;
    
    if (window.subjectAttendanceChartInstance) {
        window.subjectAttendanceChartInstance.destroy();
    }
    
    window.subjectAttendanceChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.subject_name),
            datasets: [
                {
                    label: 'Presentes',
                    data: data.map(d => d.present_count || 0),
                    backgroundColor: '#10b981',
                    borderColor: '#059669',
                    borderWidth: 1
                },
                {
                    label: 'Ausentes',
                    data: data.map(d => d.absent_count || 0),
                    backgroundColor: '#ef4444',
                    borderColor: '#dc2626',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Materias'
                    }
                }
            }
        }
    });
}

// Función para renderizar gráfico de asistencia por grupo
function renderGroupAttendanceChart(data) {
    const ctx = document.getElementById('groupAttendanceChart');
    if (!ctx) return;
    
    if (window.groupAttendanceChartInstance) {
        window.groupAttendanceChartInstance.destroy();
    }
    
    window.groupAttendanceChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.group_name),
            datasets: [{
                label: 'Estudiantes Presentes',
                data: data.map(d => d.present_count || 0),
                backgroundColor: '#10b981',
                borderColor: '#059669',
                borderWidth: 1
            }, {
                label: 'Estudiantes Ausentes',
                data: data.map(d => d.absent_count || 0),
                backgroundColor: '#ef4444',
                borderColor: '#dc2626',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de Estudiantes'
                    }
                }
            }
        }
    });
}

// Función para renderizar gráficos de estadísticas generales
function renderGeneralStatsCharts(data) {
    // Gráfico de distribución de recursos
    const ctx1 = document.getElementById('resourcesChart');
    if (ctx1) {
        if (window.resourcesChartInstance) {
            window.resourcesChartInstance.destroy();
        }
        
        window.resourcesChartInstance = new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Docentes', 'Estudiantes', 'Materias', 'Aulas', 'Grupos'],
                datasets: [{
                    data: [
                        data.total_teachers || 0,
                        data.total_students || 0,
                        data.total_subjects || 0,
                        data.total_rooms || 0,
                        data.total_groups || 0
                    ],
                    backgroundColor: [
                        '#881F34',
                        '#10b981',
                        '#3b82f6',
                        '#f59e0b',
                        '#8b5cf6'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Distribución de Recursos'
                    }
                }
            }
        });
    }
}

function closeReport() {
    document.getElementById('reportContent').classList.add('hidden');
    currentReport = null;
    
    // Destruir todos los gráficos
    if (window.workloadChartInstance) window.workloadChartInstance.destroy();
    if (window.attendanceChartInstance) window.attendanceChartInstance.destroy();
    if (window.subjectAttendanceChartInstance) window.subjectAttendanceChartInstance.destroy();
    if (window.groupAttendanceChartInstance) window.groupAttendanceChartInstance.destroy();
    if (window.resourcesChartInstance) window.resourcesChartInstance.destroy();
    if (window.absencesBarChartInstance) window.absencesBarChartInstance.destroy();
    if (window.absencesPieChartInstance) window.absencesPieChartInstance.destroy();
}

function exportReport(format) {
    if (!currentReport) {
        showNotification('No hay reporte activo para exportar', 'error');
        return;
    }
    
    showNotification(`Descargando reporte en formato ${format.toUpperCase()}...`);
    
    // Crear formulario para enviar POST y descargar archivo
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `${API_BASE}/reports/export-${format}`;
    form.target = '_blank';
    
    // Agregar CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
    form.appendChild(csrfInput);
    
    // Agregar tipo de reporte
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = currentReport;
    form.appendChild(typeInput);
    
    // Agregar filtros
    const period = document.getElementById('filterPeriod').value;
    const dateStart = document.getElementById('filterDateStart').value;
    const dateEnd = document.getElementById('filterDateEnd').value;
    
    if (period) {
        const periodInput = document.createElement('input');
        periodInput.type = 'hidden';
        periodInput.name = 'period_id';
        periodInput.value = period;
        form.appendChild(periodInput);
    }
    
    if (dateStart) {
        const dateStartInput = document.createElement('input');
        dateStartInput.type = 'hidden';
        dateStartInput.name = 'date_start';
        dateStartInput.value = dateStart;
        form.appendChild(dateStartInput);
    }
    
    if (dateEnd) {
        const dateEndInput = document.createElement('input');
        dateEndInput.type = 'hidden';
        dateEndInput.name = 'date_end';
        dateEndInput.value = dateEnd;
        form.appendChild(dateEndInput);
    }
    
    // Filtros específicos
    const teacherFilter = document.getElementById('filterTeacher');
    const subjectFilter = document.getElementById('filterSubject');
    const groupFilter = document.getElementById('filterGroup');
    
    if (teacherFilter && teacherFilter.value) {
        const teacherInput = document.createElement('input');
        teacherInput.type = 'hidden';
        teacherInput.name = 'teacher_id';
        teacherInput.value = teacherFilter.value;
        form.appendChild(teacherInput);
    }
    
    if (subjectFilter && subjectFilter.value) {
        const subjectInput = document.createElement('input');
        subjectInput.type = 'hidden';
        subjectInput.name = 'subject_id';
        subjectInput.value = subjectFilter.value;
        form.appendChild(subjectInput);
    }
    
    if (groupFilter && groupFilter.value) {
        const groupInput = document.createElement('input');
        groupInput.type = 'hidden';
        groupInput.name = 'group_id';
        groupInput.value = groupFilter.value;
        form.appendChild(groupInput);
    }
    
    // Agregar al body, enviar y eliminar
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    setTimeout(() => {
        showNotification(`✅ Reporte descargado en ${format.toUpperCase()}`);
    }, 1000);
}
</script>

</body>
</html>
