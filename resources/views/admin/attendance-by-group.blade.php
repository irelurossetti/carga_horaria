@extends('layouts.admin')

@section('title', 'Asistencia por Grupo')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Asistencia por Grupo</h1>
            <p class="text-gray-600 mt-1">Visualiza y analiza la asistencia de cada grupo</p>
        </div>
        <button onclick="exportReport()" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
            <i class="fas fa-file-export mr-2"></i>Exportar Reporte
        </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Grupo</label>
                <select id="groupId" onchange="loadGroupAttendance()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="">Seleccionar grupo...</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                <select id="subjectId" onchange="loadGroupAttendance()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="">Todas las materias</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date" id="dateFrom" onchange="loadGroupAttendance()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date" id="dateTo" onchange="loadGroupAttendance()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>
        </div>
    </div>

    <!-- Información del Grupo -->
    <div id="groupInfo" class="hidden bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start gap-6">
            <div class="w-20 h-20 bg-brand-primary rounded-full flex items-center justify-center text-white text-2xl font-bold">
                <i class="fas fa-users"></i>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900" id="groupName"></h2>
                <p class="text-gray-600" id="groupDetails"></p>
                <div class="grid grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Estudiantes</p>
                        <p class="text-2xl font-bold text-gray-900" id="totalStudents">0</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Promedio Asistencia</p>
                        <p class="text-2xl font-bold text-green-600" id="avgAttendance">0%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Clases Realizadas</p>
                        <p class="text-2xl font-bold text-blue-600" id="totalClasses">0</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Estudiantes en Riesgo</p>
                        <p class="text-2xl font-bold text-red-600" id="atRiskCount">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div id="chartsSection" class="hidden grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tendencia de Asistencia</h3>
            <canvas id="attendanceTrendChart" height="200"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribución de Asistencia</h3>
            <canvas id="attendanceDistChart" height="200"></canvas>
        </div>
    </div>

    <!-- Asistencia por Estudiante -->
    <div id="studentsSection" class="hidden bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Asistencia por Estudiante</h3>
            <div class="flex gap-2">
                <button onclick="filterByStatus('all')" class="px-3 py-1 text-sm rounded bg-gray-100 text-gray-700 hover:bg-gray-200">
                    Todos
                </button>
                <button onclick="filterByStatus('good')" class="px-3 py-1 text-sm rounded bg-green-100 text-green-700 hover:bg-green-200">
                    Buenos
                </button>
                <button onclick="filterByStatus('warning')" class="px-3 py-1 text-sm rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                    Alerta
                </button>
                <button onclick="filterByStatus('risk')" class="px-3 py-1 text-sm rounded bg-red-100 text-red-700 hover:bg-red-200">
                    Riesgo
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudiante</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Clases</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Asistencias</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Faltas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tardanzas</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">% Asistencia</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody id="studentsTable" class="divide-y divide-gray-200">
                </tbody>
            </table>
        </div>
    </div>

    <!-- Progreso del Sílabo -->
    <div id="syllabusSection" class="hidden bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Progreso del Sílabo</h3>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Temas Cubiertos</span>
                    <span id="syllabusProgress">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div id="syllabusBar" class="bg-green-600 h-4 rounded-full transition-all" style="width: 0%"></div>
                </div>
            </div>
            <div id="topicsList" class="space-y-2 mt-4">
            </div>
        </div>
    </div>

    <!-- Asistencia por Materia -->
    <div id="subjectsSection" class="hidden bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Asistencia por Materia</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Clases</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Promedio Asistencia</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody id="subjectsTable" class="divide-y divide-gray-200">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let groups = [];
let subjects = [];
let trendChart = null;
let distChart = null;
let allStudents = [];

document.addEventListener('DOMContentLoaded', function() {
    loadGroups();
    loadSubjects();
});

async function loadGroups() {
    try {
        const response = await fetch('/api/groups');
        const data = await response.json();
        groups = data.data || [];
        
        const select = document.getElementById('groupId');
        select.innerHTML = '<option value="">Seleccionar grupo...</option>' +
            groups.map(g => `<option value="${g.id}">${g.name}</option>`).join('');
    } catch (error) {
        console.error('Error:', error);
    }
}

async function loadSubjects() {
    try {
        const response = await fetch('/api/subjects');
        const data = await response.json();
        subjects = data.data || [];
        
        const select = document.getElementById('subjectId');
        select.innerHTML = '<option value="">Todas las materias</option>' +
            subjects.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
    } catch (error) {
        console.error('Error:', error);
    }
}

async function loadGroupAttendance() {
    const groupId = document.getElementById('groupId').value;
    
    if (!groupId) {
        hideAllSections();
        return;
    }
    
    const group = groups.find(g => g.id == groupId);
    if (!group) return;
    
    showGroupInfo(group);
    
    // Datos simulados
    const attendanceData = generateMockGroupData();
    
    displayStats(attendanceData);
    displayCharts(attendanceData);
    displayStudents(attendanceData);
    displaySubjectsSummary(attendanceData);
    
    // Cargar progreso del sílabo
    if (group.subject_id) {
        loadSyllabusProgress(group.subject_id, groupId);
    }
}

async function loadSyllabusProgress(subjectId, groupId) {
    try {
        const response = await fetch(`/api/syllabus-topics?subject_id=${subjectId}`);
        const topics = await response.json();
        
        if (topics.length > 0) {
            document.getElementById('syllabusSection').classList.remove('hidden');
            
            // Simular progreso (en producción, esto vendría del backend)
            const coveredCount = Math.floor(topics.length * 0.6);
            const progress = Math.round((coveredCount / topics.length) * 100);
            
            document.getElementById('syllabusProgress').textContent = `${progress}% (${coveredCount}/${topics.length})`;
            document.getElementById('syllabusBar').style.width = `${progress}%`;
            
            // Mostrar lista de temas
            const topicsList = document.getElementById('topicsList');
            topicsList.innerHTML = topics.map((topic, index) => {
                const isCovered = index < coveredCount;
                return `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 flex items-center justify-center rounded-full ${isCovered ? 'bg-green-100 text-green-600' : 'bg-gray-200 text-gray-500'} text-sm font-medium">
                                ${topic.order_index}
                            </span>
                            <div>
                                <p class="font-medium text-gray-900">${topic.unit_name}</p>
                                <p class="text-sm text-gray-600">${topic.topic_description}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded ${isCovered ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">
                            ${isCovered ? 'Cubierto' : 'Pendiente'}
                        </span>
                    </div>
                `;
            }).join('');
        }
    } catch (error) {
        console.error('Error loading syllabus:', error);
    }
}

function showGroupInfo(group) {
    document.getElementById('groupInfo').classList.remove('hidden');
    document.getElementById('groupName').textContent = group.name;
    document.getElementById('groupDetails').textContent = `Carrera: ${group.career || 'N/A'} | Semestre: ${group.semester || 'N/A'}`;
}

function displayStats(data) {
    document.getElementById('totalStudents').textContent = data.students.length;
    document.getElementById('avgAttendance').textContent = data.avgAttendance + '%';
    document.getElementById('totalClasses').textContent = data.totalClasses;
    document.getElementById('atRiskCount').textContent = data.atRisk;
}

function displayCharts(data) {
    document.getElementById('chartsSection').classList.remove('hidden');
    
    // Gráfico de tendencia
    const trendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
    if (trendChart) trendChart.destroy();
    
    trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ['Sem 1', 'Sem 2', 'Sem 3', 'Sem 4', 'Sem 5', 'Sem 6', 'Sem 7', 'Sem 8'],
            datasets: [{
                label: '% Asistencia Promedio',
                data: [88, 85, 90, 87, 92, 89, 91, 88],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
    
    // Gráfico de distribución
    const distCtx = document.getElementById('attendanceDistChart').getContext('2d');
    if (distChart) distChart.destroy();
    
    distChart = new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Excelente (>90%)', 'Bueno (80-90%)', 'Regular (70-80%)', 'Bajo (<70%)'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: [
                    '#10b981',
                    '#3b82f6',
                    '#f59e0b',
                    '#ef4444'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function displayStudents(data) {
    document.getElementById('studentsSection').classList.remove('hidden');
    allStudents = data.students;
    renderStudentsTable(allStudents);
}

function renderStudentsTable(students) {
    const tbody = document.getElementById('studentsTable');
    
    tbody.innerHTML = students.map(student => {
        const percent = Math.round((student.present / student.total) * 100);
        const status = getAttendanceStatus(percent);
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${student.name}</div>
                    <div class="text-sm text-gray-500">${student.code}</div>
                </td>
                <td class="px-6 py-4 text-center text-sm text-gray-600">${student.total}</td>
                <td class="px-6 py-4 text-center text-sm text-green-600 font-medium">${student.present}</td>
                <td class="px-6 py-4 text-center text-sm text-red-600 font-medium">${student.absent}</td>
                <td class="px-6 py-4 text-center text-sm text-yellow-600 font-medium">${student.late}</td>
                <td class="px-6 py-4 text-center">
                    <span class="text-sm font-semibold ${status.color}">${percent}%</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 ${status.bgClass} ${status.textClass} text-xs font-medium rounded">
                        ${status.label}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

function displaySubjectsSummary(data) {
    document.getElementById('subjectsSection').classList.remove('hidden');
    
    const tbody = document.getElementById('subjectsTable');
    tbody.innerHTML = `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-900">Programación I</td>
            <td class="px-6 py-4 text-sm text-gray-600">Dr. Juan Pérez</td>
            <td class="px-6 py-4 text-center text-sm text-gray-600">32</td>
            <td class="px-6 py-4 text-center text-sm font-semibold text-green-600">92%</td>
            <td class="px-6 py-4 text-center">
                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Excelente</span>
            </td>
        </tr>
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-900">Base de Datos</td>
            <td class="px-6 py-4 text-sm text-gray-600">Dra. María García</td>
            <td class="px-6 py-4 text-center text-sm text-gray-600">28</td>
            <td class="px-6 py-4 text-center text-sm font-semibold text-blue-600">85%</td>
            <td class="px-6 py-4 text-center">
                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">Bueno</span>
            </td>
        </tr>
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 font-medium text-gray-900">Algoritmos</td>
            <td class="px-6 py-4 text-sm text-gray-600">Dr. Carlos López</td>
            <td class="px-6 py-4 text-center text-sm text-gray-600">30</td>
            <td class="px-6 py-4 text-center text-sm font-semibold text-yellow-600">75%</td>
            <td class="px-6 py-4 text-center">
                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded">Regular</span>
            </td>
        </tr>
    `;
}

function getAttendanceStatus(percent) {
    if (percent >= 90) {
        return {
            label: 'Excelente',
            color: 'text-green-600',
            bgClass: 'bg-green-100',
            textClass: 'text-green-700',
            status: 'good'
        };
    } else if (percent >= 80) {
        return {
            label: 'Bueno',
            color: 'text-blue-600',
            bgClass: 'bg-blue-100',
            textClass: 'text-blue-700',
            status: 'good'
        };
    } else if (percent >= 70) {
        return {
            label: 'Regular',
            color: 'text-yellow-600',
            bgClass: 'bg-yellow-100',
            textClass: 'text-yellow-700',
            status: 'warning'
        };
    } else {
        return {
            label: 'Riesgo',
            color: 'text-red-600',
            bgClass: 'bg-red-100',
            textClass: 'text-red-700',
            status: 'risk'
        };
    }
}

function filterByStatus(status) {
    if (status === 'all') {
        renderStudentsTable(allStudents);
        return;
    }
    
    const filtered = allStudents.filter(student => {
        const percent = Math.round((student.present / student.total) * 100);
        const studentStatus = getAttendanceStatus(percent).status;
        return studentStatus === status;
    });
    
    renderStudentsTable(filtered);
}

function generateMockGroupData() {
    return {
        totalClasses: 32,
        avgAttendance: 88,
        atRisk: 3,
        students: [
            { name: 'Ana García', code: '2021001', total: 32, present: 30, absent: 2, late: 1 },
            { name: 'Carlos Pérez', code: '2021002', total: 32, present: 28, absent: 4, late: 2 },
            { name: 'María López', code: '2021003', total: 32, present: 31, absent: 1, late: 0 },
            { name: 'Juan Rodríguez', code: '2021004', total: 32, present: 25, absent: 7, late: 3 },
            { name: 'Laura Martínez', code: '2021005', total: 32, present: 29, absent: 3, late: 1 }
        ]
    };
}

function hideAllSections() {
    document.getElementById('groupInfo').classList.add('hidden');
    document.getElementById('chartsSection').classList.add('hidden');
    document.getElementById('studentsSection').classList.add('hidden');
    document.getElementById('subjectsSection').classList.add('hidden');
    document.getElementById('syllabusSection').classList.add('hidden');
}

function exportReport() {
    const groupId = document.getElementById('groupId').value;
    if (!groupId) {
        alert('Selecciona un grupo primero');
        return;
    }
    alert('Exportando reporte...');
}
</script>
@endsection
