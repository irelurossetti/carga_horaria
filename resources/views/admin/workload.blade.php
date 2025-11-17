<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Carga Horaria - FICCT SGA</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Instrument Sans','sans-serif']},colors:{brand:{primary:'#881F34',hover:'#6d1829'}}}}}</script>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    @include('layouts.admin-sidebar')
    <main class="flex-1 ml-64 p-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Carga Horaria</h1>
            <p class="text-gray-500 mt-1">Visualiza la carga académica por docente, materia o grupo</p>
        </div>

        <!-- Pestañas -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button onclick="switchTab('teacher')" id="tab-teacher" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-brand-primary text-brand-primary">
                        Por Docente
                    </button>
                    <button onclick="switchTab('subject')" id="tab-subject" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Por Materia
                    </button>
                    <button onclick="switchTab('group')" id="tab-group" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Por Grupo
                    </button>
                </nav>
            </div>
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
                <div id="filterContainer">
                    <!-- Se llenará dinámicamente según la pestaña activa -->
                </div>
                <div class="flex items-end">
                    <button onclick="loadData()" class="w-full px-6 py-2 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium">
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

        <!-- Gráfico -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Carga</h3>
            <canvas id="chartDistribution"></canvas>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Detalle de Carga</h2>
                <button onclick="exportPDF()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
                    📄 Exportar PDF
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr id="tableHeader"></tr>
                    </thead>
                    <tbody id="tableBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
let currentTab = 'teacher';
let chart = null;
const API = '/api';

function switchTab(tab) {
    currentTab = tab;
    
    // Actualizar estilos de pestañas
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-brand-primary', 'text-brand-primary');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById(`tab-${tab}`).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById(`tab-${tab}`).classList.add('border-brand-primary', 'text-brand-primary');
    
    // Actualizar filtro
    updateFilter();
    
    // Cargar datos
    loadData();
}

async function updateFilter() {
    const container = document.getElementById('filterContainer');
    
    if (currentTab === 'teacher') {
        container.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Docente</label>
                <select id="entityFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todos los docentes</option>
                </select>
            </div>
        `;
        await loadTeachers();
    } else if (currentTab === 'subject') {
        container.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                <select id="entityFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todas las materias</option>
                </select>
            </div>
        `;
        await loadSubjects();
    } else if (currentTab === 'group') {
        container.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Grupo</label>
                <select id="entityFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">Todos los grupos</option>
                </select>
            </div>
        `;
        await loadGroups();
    }
}

async function loadTeachers() {
    try {
        const response = await fetch(`${API}/teachers`, { headers: { 'Accept': 'application/json' } });
        if (response.ok) {
            const teachers = await response.json();
            const select = document.getElementById('entityFilter');
            teachers.forEach(t => {
                const option = document.createElement('option');
                option.value = t.id;
                option.textContent = t.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar docentes:', error);
    }
}

async function loadSubjects() {
    try {
        const response = await fetch(`${API}/subjects`, { headers: { 'Accept': 'application/json' } });
        if (response.ok) {
            const subjects = await response.json();
            const select = document.getElementById('entityFilter');
            subjects.forEach(s => {
                const option = document.createElement('option');
                option.value = s.id;
                option.textContent = s.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar materias:', error);
    }
}

async function loadGroups() {
    try {
        const response = await fetch(`${API}/groups`, { headers: { 'Accept': 'application/json' } });
        if (response.ok) {
            const groups = await response.json();
            const select = document.getElementById('entityFilter');
            groups.forEach(g => {
                const option = document.createElement('option');
                option.value = g.id;
                option.textContent = g.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error al cargar grupos:', error);
    }
}

async function loadData() {
    const period = document.getElementById('periodFilter').value;
    const entityId = document.getElementById('entityFilter')?.value || '';
    
    try {
        let url = `${API}/workload/${currentTab}?period=${period}`;
        if (entityId) url += `&id=${entityId}`;
        
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        
        if (!response.ok) throw new Error('Error al cargar datos');
        
        const data = await response.json();
        updateStats(data.stats);
        updateChart(data.distribution, data.distribution_theory, data.distribution_practice);
        updateTable(data.details);
    } catch (error) {
        console.error(error);
        alert('Error al cargar la carga horaria');
    }
}

function updateStats(stats) {
    document.getElementById('hoursWeek').textContent = stats.hours_week || 0;
    document.getElementById('hoursPeriod').textContent = stats.hours_period || 0;
    document.getElementById('totalClasses').textContent = stats.total_classes || 0;
    
    const periodLabels = { 'week': 'Semanal', 'month': 'Mensual', 'semester': 'Semestral' };
    document.getElementById('periodLabel').textContent = periodLabels[document.getElementById('periodFilter').value] || 'Semanal';
}

function updateChart(distribution, distributionTheory = null, distributionPractice = null) {
    const ctx = document.getElementById('chartDistribution').getContext('2d');
    if (chart) chart.destroy();
    
    // Si es por grupo y tenemos datos de teoría/práctica, mostrar gráfico apilado
    if (currentTab === 'group' && distributionTheory && distributionPractice) {
        const labels = Object.keys(distribution);
        
        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Horas Teóricas',
                        data: labels.map(l => distributionTheory[l] || 0),
                        backgroundColor: '#881F34'
                    },
                    {
                        label: 'Horas Prácticas',
                        data: labels.map(l => distributionPractice[l] || 0),
                        backgroundColor: '#4BC0C0'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    } else {
        // Gráfico simple para docente y materia
        chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(distribution),
                datasets: [{
                    label: 'Horas',
                    data: Object.values(distribution),
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
    }
}

function updateTable(details) {
    const thead = document.getElementById('tableHeader');
    const tbody = document.getElementById('tableBody');
    
    if (!details || !details.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">No hay datos</td></tr>';
        return;
    }
    
    // Encabezados según la pestaña
    if (currentTab === 'teacher') {
        thead.innerHTML = `
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Horarios</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Horas</th>
        `;
    } else if (currentTab === 'subject') {
        thead.innerHTML = `
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Grupos</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Horas</th>
        `;
    } else if (currentTab === 'group') {
        thead.innerHTML = `
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grupo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Horarios</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">H. Teóricas</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">H. Prácticas</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
        `;
    }
    
    tbody.innerHTML = details.map(item => {
        if (currentTab === 'teacher') {
            return `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium">${item.name}</td>
                    <td class="px-6 py-4 text-sm">${item.email || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-center">${item.schedules}</td>
                    <td class="px-6 py-4 text-sm text-center font-bold">${item.hours}h</td>
                </tr>
            `;
        } else if (currentTab === 'subject') {
            return `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium">${item.name}</td>
                    <td class="px-6 py-4 text-sm">${item.code || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-center">${item.groups}</td>
                    <td class="px-6 py-4 text-sm text-center font-bold">${item.hours}h</td>
                </tr>
            `;
        } else if (currentTab === 'group') {
            return `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium">${item.name}</td>
                    <td class="px-6 py-4 text-sm">${item.subject || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-center">${item.schedules}</td>
                    <td class="px-6 py-4 text-sm text-center"><span class="px-2 py-1 bg-red-100 text-red-800 rounded">${item.hours_theory || 0}h</span></td>
                    <td class="px-6 py-4 text-sm text-center"><span class="px-2 py-1 bg-teal-100 text-teal-800 rounded">${item.hours_practice || 0}h</span></td>
                    <td class="px-6 py-4 text-sm text-center font-bold">${item.hours}h</td>
                </tr>
            `;
        }
    }).join('');
}

function exportPDF() {
    const period = document.getElementById('periodFilter').value;
    const entityId = document.getElementById('entityFilter')?.value || '';
    let url = `/api/workload/${currentTab}/export-pdf?period=${period}`;
    if (entityId) url += `&id=${entityId}`;
    window.open(url, '_blank');
}

// Inicializar
updateFilter();
loadData();
</script>
</body>
</html>
