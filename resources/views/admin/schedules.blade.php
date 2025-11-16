<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Horarios - FICCT SGA</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Horarios</h1>
                <p class="text-gray-500 mt-1">Asigna horarios manualmente y visualiza conflictos</p>
            </div>
            <div class="flex gap-3">
                <button onclick="generateSchedules()" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors shadow-sm">
                    🤖 Generar Automático
                </button>
                <button onclick="openScheduleModal()" class="px-6 py-3 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium transition-colors shadow-sm">
                    + Nuevo Horario
                </button>
            </div>
        </div>

        <!-- View Toggle -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-semibold text-gray-900">Vista de Horarios</h3>
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button onclick="switchView('grid')" id="gridViewBtn" class="px-4 py-2 rounded-md text-sm font-medium transition-colors bg-brand-primary text-white">
                            📅 Grilla Semanal
                        </button>
                        <button onclick="switchView('list')" id="listViewBtn" class="px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-gray-900">
                            📋 Lista
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <select id="groupFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Todos los grupos</option>
                    </select>
                    <button onclick="exportSchedule()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                        📊 Exportar
                    </button>
                </div>
            </div>
        </div>

        <!-- Grid View -->
        <div id="gridView" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Horario Semanal</h2>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-4 h-4 bg-green-100 border border-green-300 rounded"></div>
                            <span>Disponible</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-4 h-4 bg-blue-100 border border-blue-300 rounded"></div>
                            <span>Ocupado</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-4 h-4 bg-red-100 border border-red-300 rounded"></div>
                            <span>Conflicto</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="border border-gray-300 px-4 py-3 text-left text-sm font-medium text-gray-700 w-32">Hora</th>
                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">Lunes</th>
                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">Martes</th>
                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">Miércoles</th>
                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">Jueves</th>
                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">Viernes</th>
                            <th class="border border-gray-300 px-4 py-3 text-center text-sm font-medium text-gray-700">Sábado</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleGrid">
                        <!-- Se llenará con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- List View -->
        <div id="listView" class="bg-white rounded-xl shadow-sm border border-gray-200 hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Lista de Horarios</h2>
                    <div class="flex gap-2">
                        <input type="text" id="searchSchedules" placeholder="Buscar..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <select id="conflictFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="conflicts">Solo Conflictos</option>
                            <option value="no-conflicts">Sin Conflictos</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docente</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aula</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="schedulesList" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-primary mx-auto"></div>
                                <p class="mt-4 text-gray-500">Cargando horarios...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal para Horario -->
<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="scheduleModalTitle" class="text-xl font-bold text-gray-900">Nuevo Horario</h3>
                <button onclick="closeScheduleModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form id="scheduleForm" class="p-6 space-y-6">
            <input type="hidden" id="scheduleId">
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asignación *</label>
                    <select id="assignmentSelect" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Seleccionar asignación</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Docente - Materia - Grupo</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aula *</label>
                    <select id="roomSelect" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Seleccionar aula</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Día *</label>
                    <select id="daySelect" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Seleccionar día</option>
                        <option value="monday">Lunes</option>
                        <option value="tuesday">Martes</option>
                        <option value="wednesday">Miércoles</option>
                        <option value="thursday">Jueves</option>
                        <option value="friday">Viernes</option>
                        <option value="saturday">Sábado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora Inicio *</label>
                    <select id="startTimeSelect" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Seleccionar hora</option>
                        <option value="07:00">07:00</option>
                        <option value="08:00">08:00</option>
                        <option value="09:00">09:00</option>
                        <option value="10:00">10:00</option>
                        <option value="11:00">11:00</option>
                        <option value="12:00">12:00</option>
                        <option value="13:00">13:00</option>
                        <option value="14:00">14:00</option>
                        <option value="15:00">15:00</option>
                        <option value="16:00">16:00</option>
                        <option value="17:00">17:00</option>
                        <option value="18:00">18:00</option>
                        <option value="19:00">19:00</option>
                        <option value="20:00">20:00</option>
                        <option value="21:00">21:00</option>
                        <option value="22:00">22:00</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hora Fin *</label>
                    <select id="endTimeSelect" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Seleccionar hora</option>
                        <option value="08:00">08:00</option>
                        <option value="09:00">09:00</option>
                        <option value="10:00">10:00</option>
                        <option value="11:00">11:00</option>
                        <option value="12:00">12:00</option>
                        <option value="13:00">13:00</option>
                        <option value="14:00">14:00</option>
                        <option value="15:00">15:00</option>
                        <option value="16:00">16:00</option>
                        <option value="17:00">17:00</option>
                        <option value="18:00">18:00</option>
                        <option value="19:00">19:00</option>
                        <option value="20:00">20:00</option>
                        <option value="21:00">21:00</option>
                        <option value="22:00">22:00</option>
                        <option value="23:00">23:00</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                <textarea id="scheduleNotes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Observaciones adicionales..."></textarea>
            </div>
            
            <!-- Conflict Detection -->
            <div id="conflictDetection" class="hidden">
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="font-medium text-red-900 mb-2">⚠️ Conflictos Detectados</h4>
                    <ul id="conflictsList" class="text-sm text-red-800 space-y-1">
                        <!-- Se llenará dinámicamente -->
                    </ul>
                </div>
            </div>
            
            <!-- Available Rooms -->
            <div id="availableRooms" class="hidden">
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <h4 class="font-medium text-green-900 mb-2">✅ Aulas Disponibles</h4>
                    <div id="availableRoomsList" class="text-sm text-green-800">
                        <!-- Se llenará dinámicamente -->
                    </div>
                </div>
            </div>
        </form>

        <div class="p-6 pt-0">
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeScheduleModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="checkConflicts()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    Verificar Conflictos
                </button>
                <button type="button" onclick="document.getElementById('scheduleForm').requestSubmit()" class="px-6 py-2 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium transition-colors">
                    Guardar Horario
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '/api';
let allSchedules = [];
let filteredSchedules = [];
let assignments = [];
let rooms = [];
let subjects = [];
let currentView = 'grid';

const timeSlots = ['07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00'];
const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
const dayNames = {
    monday: 'Lunes',
    tuesday: 'Martes',
    wednesday: 'Miércoles',
    thursday: 'Jueves',
    friday: 'Viernes',
    saturday: 'Sábado'
};

function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg border ${alertClass} z-50 shadow-lg`;
    notification.innerHTML = `<span class="font-medium">${message}</span>`;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

async function loadSchedules() {
    try {
        console.log('Loading schedules...');
        const response = await fetch(`${API_BASE}/schedules`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Schedule API error:', errorText);
            throw new Error('Error al cargar horarios: ' + response.status);
        }
        
        const rawSchedules = await response.json();
        console.log('Raw schedules from API:', rawSchedules);
        
        // Mapear los datos de la API al formato esperado
        allSchedules = rawSchedules.map(schedule => {
            console.log('Processing schedule:', schedule);
            
            // Buscar la asignación correspondiente si existe
            const assignment = assignments.find(a => a.group_id === schedule.group_id);
            
            // Obtener el nombre de la materia
            let subjectName = 'Sin materia';
            if (schedule.group?.subject) {
                // Si el grupo tiene la relación subject cargada
                subjectName = schedule.group.subject.name;
            } else if (assignment?.subject_name) {
                // Si encontramos la asignación
                subjectName = assignment.subject_name;
            }
            
            // Mapear el día al formato correcto
            const dayMapping = {
                'lunes': 'monday',
                'martes': 'tuesday',
                'miércoles': 'wednesday',
                'jueves': 'thursday',
                'viernes': 'friday',
                'sábado': 'saturday'
            };
            
            const dayLower = schedule.day_of_week?.toLowerCase() || '';
            const mappedDay = dayMapping[dayLower] || dayLower || 'monday';
            
            return {
                id: schedule.id,
                assignment_id: schedule.assignment_id || null,
                group_id: schedule.group_id,
                room_id: schedule.room_id,
                teacher_id: schedule.teacher_id,
                teacher_name: schedule.teacher?.name || assignment?.teacher_name || 'Sin docente',
                subject_name: subjectName,
                group_name: schedule.group?.name || assignment?.group_name || 'Sin grupo',
                room_name: schedule.room?.name || 'Sin aula',
                day: mappedDay,
                start_time: schedule.start_time?.substring(0, 5) || '08:00',
                end_time: schedule.end_time?.substring(0, 5) || '10:00',
                has_conflicts: false,
                status: 'active'
            };
        });
        
        console.log('Mapped schedules:', allSchedules);
        filteredSchedules = [...allSchedules];
        renderCurrentView();
    } catch (error) {
        console.error('Error loading schedules:', error);
        showNotification('❌ Error al cargar horarios: ' + error.message, 'error');
        allSchedules = [];
        filteredSchedules = [];
        renderCurrentView();
    }
}

async function loadAssignments() {
    try {
        const response = await fetch(`${API_BASE}/assignments`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (response.ok) {
            const rawAssignments = await response.json();
            
            // Mapear los datos de la API al formato esperado
            assignments = rawAssignments.map(assignment => ({
                id: assignment.id,
                teacher_id: assignment.teacher_id,
                subject_id: assignment.subject_id,
                group_id: assignment.group_id,
                teacher_name: assignment.teacher?.name || 'Sin docente',
                subject_name: assignment.subject?.name || 'Sin materia',
                subject_code: assignment.subject?.code || '',
                group_name: assignment.group?.name || 'Sin grupo',
                weekly_hours: assignment.horas_semanales || 0
            }));
        } else {
            assignments = [
                { id: 1, teacher_name: 'Dr. Juan Pérez', subject_name: 'Introducción a la Programación', group_name: 'Grupo A' },
                { id: 2, teacher_name: 'Ing. María García', subject_name: 'Base de Datos', group_name: 'Grupo B' },
            ];
        }
        
        populateAssignmentSelect();
    } catch (error) {
        console.error('Error loading assignments:', error);
        assignments = [
            { id: 1, teacher_name: 'Dr. Juan Pérez', subject_name: 'Introducción a la Programación', group_name: 'Grupo A' },
            { id: 2, teacher_name: 'Ing. María García', subject_name: 'Base de Datos', group_name: 'Grupo B' },
        ];
        populateAssignmentSelect();
    }
}

async function loadRooms() {
    try {
        const response = await fetch(`${API_BASE}/rooms`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (response.ok) {
            rooms = await response.json();
        } else {
            rooms = [
                { id: 1, name: 'Aula 101', capacity: 30, floor: 1 },
                { id: 2, name: 'Aula 102', capacity: 30, floor: 1 },
                { id: 3, name: 'Aula 201', capacity: 35, floor: 2 },
            ];
        }
        
        populateRoomSelect();
    } catch (error) {
        console.error('Error loading rooms:', error);
        rooms = [
            { id: 1, name: 'Aula 101', capacity: 30, floor: 1 },
            { id: 2, name: 'Aula 102', capacity: 30, floor: 1 },
            { id: 3, name: 'Aula 201', capacity: 35, floor: 2 },
        ];
        populateRoomSelect();
    }
}

function populateAssignmentSelect() {
    const assignmentSelect = document.getElementById('assignmentSelect');
    const groupFilter = document.getElementById('groupFilter');
    
    const assignmentOptions = assignments.map(assignment => 
        `<option value="${assignment.id}">${assignment.teacher_name} - ${assignment.subject_name} - ${assignment.group_name}</option>`
    ).join('');
    
    assignmentSelect.innerHTML = '<option value="">Seleccionar asignación</option>' + assignmentOptions;
    
    const groupOptions = [...new Set(assignments.map(a => a.group_name))].map(group => 
        `<option value="${group}">${group}</option>`
    ).join('');
    
    groupFilter.innerHTML = '<option value="">Todos los grupos</option>' + groupOptions;
}

function populateRoomSelect() {
    const roomSelect = document.getElementById('roomSelect');
    
    const roomOptions = rooms.map(room => 
        `<option value="${room.id}">${room.name} (Cap: ${room.capacity})</option>`
    ).join('');
    
    roomSelect.innerHTML = '<option value="">Seleccionar aula</option>' + roomOptions;
}

function switchView(view) {
    currentView = view;
    
    // Update buttons
    document.getElementById('gridViewBtn').className = view === 'grid' 
        ? 'px-4 py-2 rounded-md text-sm font-medium transition-colors bg-brand-primary text-white'
        : 'px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-gray-900';
    
    document.getElementById('listViewBtn').className = view === 'list' 
        ? 'px-4 py-2 rounded-md text-sm font-medium transition-colors bg-brand-primary text-white'
        : 'px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-gray-900';
    
    // Show/hide views
    document.getElementById('gridView').classList.toggle('hidden', view !== 'grid');
    document.getElementById('listView').classList.toggle('hidden', view !== 'list');
    
    renderCurrentView();
}

function renderCurrentView() {
    if (currentView === 'grid') {
        renderScheduleGrid();
    } else {
        renderSchedulesList();
    }
}

function renderScheduleGrid() {
    const tbody = document.getElementById('scheduleGrid');
    
    tbody.innerHTML = timeSlots.map(time => {
        const nextTime = timeSlots[timeSlots.indexOf(time) + 1] || '23:00';
        
        return `
            <tr>
                <td class="border border-gray-300 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-50">
                    ${time} - ${nextTime}
                </td>
                ${days.map(day => {
                    const schedule = filteredSchedules.find(s => 
                        s.day === day && s.start_time <= time && s.end_time > time
                    );
                    
                    if (schedule) {
                        const bgColor = schedule.has_conflicts ? 'bg-red-100 border-red-300' : 'bg-blue-100 border-blue-300';
                        const textColor = schedule.has_conflicts ? 'text-red-800' : 'text-blue-800';
                        
                        return `
                            <td class="border border-gray-300 px-2 py-2 ${bgColor} cursor-pointer hover:opacity-80" onclick="editSchedule(${schedule.id})">
                                <div class="text-xs ${textColor}">
                                    <div class="font-bold">${schedule.subject_name}</div>
                                    <div class="mt-1">${schedule.group_name}</div>
                                    <div>${schedule.teacher_name}</div>
                                    <div class="mt-1">${schedule.room_name}</div>
                                    <div class="text-xs">${schedule.start_time} - ${schedule.end_time}</div>
                                    ${schedule.has_conflicts ? '<div class="mt-1 font-semibold">⚠️ CONFLICTO</div>' : ''}
                                </div>
                            </td>
                        `;
                    } else {
                        return `
                            <td class="border border-gray-300 px-2 py-2 bg-green-50 hover:bg-green-100 cursor-pointer" onclick="quickSchedule('${day}', '${time}', '${nextTime}')">
                                <div class="text-xs text-green-600 text-center">Disponible</div>
                            </td>
                        `;
                    }
                }).join('')}
            </tr>
        `;
    }).join('');
}

function renderSchedulesList() {
    const tbody = document.getElementById('schedulesList');
    
    if (filteredSchedules.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    No se encontraron horarios
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = filteredSchedules.map(schedule => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">${schedule.subject_name}</div>
                <div class="text-sm text-gray-500">${schedule.assignment_id ? 'Asignado' : 'Sin asignar'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    ${schedule.group_name}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
                ${schedule.teacher_name}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm text-gray-900">${dayNames[schedule.day]}</div>
                <div class="text-sm text-gray-500">${schedule.start_time} - ${schedule.end_time}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                ${schedule.room_name}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="px-3 py-1 rounded-full text-sm font-medium ${schedule.has_conflicts ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'}">
                    ${schedule.has_conflicts ? 'Conflicto' : 'OK'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-2">
                    <button onclick="editSchedule(${schedule.id})" class="text-blue-600 hover:text-blue-900" title="Editar">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button onclick="deleteSchedule(${schedule.id})" class="text-red-600 hover:text-red-900" title="Eliminar">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function quickSchedule(day, startTime, endTime) {
    document.getElementById('daySelect').value = day;
    document.getElementById('startTimeSelect').value = startTime;
    document.getElementById('endTimeSelect').value = endTime;
    openScheduleModal();
}

function openScheduleModal() {
    document.getElementById('scheduleModalTitle').textContent = 'Nuevo Horario';
    document.getElementById('scheduleForm').reset();
    document.getElementById('scheduleId').value = '';
    document.getElementById('conflictDetection').classList.add('hidden');
    document.getElementById('availableRooms').classList.add('hidden');
    document.getElementById('scheduleModal').classList.remove('hidden');
    document.getElementById('scheduleModal').classList.add('flex');
}

function closeScheduleModal() {
    document.getElementById('scheduleModal').classList.add('hidden');
    document.getElementById('scheduleModal').classList.remove('flex');
}

function editSchedule(id) {
    const schedule = allSchedules.find(s => s.id === id);
    if (!schedule) {
        showNotification('❌ Horario no encontrado', 'error');
        return;
    }
    
    document.getElementById('scheduleModalTitle').textContent = 'Editar Horario';
    document.getElementById('scheduleId').value = schedule.id;
    document.getElementById('assignmentSelect').value = schedule.assignment_id;
    document.getElementById('roomSelect').value = schedule.room_id;
    document.getElementById('daySelect').value = schedule.day;
    document.getElementById('startTimeSelect').value = schedule.start_time;
    document.getElementById('endTimeSelect').value = schedule.end_time;
    document.getElementById('scheduleNotes').value = schedule.notes || '';
    
    document.getElementById('scheduleModal').classList.remove('hidden');
    document.getElementById('scheduleModal').classList.add('flex');
}

async function deleteSchedule(id) {
    if (!confirm('¿Estás seguro de eliminar este horario?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/schedules/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.message || 'Error al eliminar');
        }
        
        showNotification(result.message || '✅ Horario eliminado exitosamente');
        loadSchedules();
    } catch (error) {
        console.error('Error:', error);
        showNotification('❌ ' + error.message, 'error');
    }
}

function checkConflicts() {
    const assignmentId = document.getElementById('assignmentSelect').value;
    const roomId = document.getElementById('roomSelect').value;
    const day = document.getElementById('daySelect').value;
    const startTime = document.getElementById('startTimeSelect').value;
    const endTime = document.getElementById('endTimeSelect').value;
    
    if (!assignmentId || !roomId || !day || !startTime || !endTime) {
        showNotification('❌ Complete todos los campos requeridos', 'error');
        return;
    }
    
    // Simular detección de conflictos
    const conflicts = [];
    const availableRooms = [];
    
    // Verificar conflictos de docente
    const assignment = assignments.find(a => a.id == assignmentId);
    const teacherConflicts = allSchedules.filter(s => 
        s.teacher_name === assignment?.teacher_name && 
        s.day === day && 
        ((startTime >= s.start_time && startTime < s.end_time) || 
         (endTime > s.start_time && endTime <= s.end_time))
    );
    
    if (teacherConflicts.length > 0) {
        conflicts.push(`El docente ${assignment.teacher_name} ya tiene clase de ${teacherConflicts[0].start_time} a ${teacherConflicts[0].end_time}`);
    }
    
    // Verificar conflictos de aula
    const roomConflicts = allSchedules.filter(s => 
        s.room_id == roomId && 
        s.day === day && 
        ((startTime >= s.start_time && startTime < s.end_time) || 
         (endTime > s.start_time && endTime <= s.end_time))
    );
    
    if (roomConflicts.length > 0) {
        const room = rooms.find(r => r.id == roomId);
        conflicts.push(`El aula ${room?.name} ya está ocupada de ${roomConflicts[0].start_time} a ${roomConflicts[0].end_time}`);
    }
    
    // Mostrar conflictos
    const conflictDiv = document.getElementById('conflictDetection');
    const conflictsList = document.getElementById('conflictsList');
    
    if (conflicts.length > 0) {
        conflictsList.innerHTML = conflicts.map(conflict => `<li>• ${conflict}</li>`).join('');
        conflictDiv.classList.remove('hidden');
    } else {
        conflictDiv.classList.add('hidden');
    }
    
    // Mostrar aulas disponibles
    const availableRoomsDiv = document.getElementById('availableRooms');
    const availableRoomsList = document.getElementById('availableRoomsList');
    
    const freeRooms = rooms.filter(room => {
        const roomBusy = allSchedules.some(s => 
            s.room_id == room.id && 
            s.day === day && 
            ((startTime >= s.start_time && startTime < s.end_time) || 
             (endTime > s.start_time && endTime <= s.end_time))
        );
        return !roomBusy;
    });
    
    if (freeRooms.length > 0) {
        availableRoomsList.innerHTML = freeRooms.map(room => 
            `<span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded text-xs mr-2 mb-1">${room.name} (${room.capacity})</span>`
        ).join('');
        availableRoomsDiv.classList.remove('hidden');
    } else {
        availableRoomsDiv.classList.add('hidden');
    }
    
    if (conflicts.length === 0) {
        showNotification('✅ No se detectaron conflictos');
    } else {
        showNotification(`⚠️ Se detectaron ${conflicts.length} conflicto(s)`, 'error');
    }
}

// Form submission
document.getElementById('scheduleForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const scheduleId = document.getElementById('scheduleId').value;
    const assignmentId = parseInt(document.getElementById('assignmentSelect').value);
    const roomId = parseInt(document.getElementById('roomSelect').value);
    const day = document.getElementById('daySelect').value;
    const startTime = document.getElementById('startTimeSelect').value;
    const endTime = document.getElementById('endTimeSelect').value;
    
    // Buscar la asignación seleccionada para obtener group_id y teacher_id
    const assignment = assignments.find(a => a.id === assignmentId);
    
    if (!assignment) {
        showNotification('❌ Por favor selecciona una asignación válida', 'error');
        return;
    }
    
    // Mapear el día al formato esperado por el backend
    const dayMapping = {
        'monday': 'Lunes',
        'tuesday': 'Martes',
        'wednesday': 'Miércoles',
        'thursday': 'Jueves',
        'friday': 'Viernes',
        'saturday': 'Sábado'
    };
    
    const data = {
        group_id: assignment.group_id,
        teacher_id: assignment.teacher_id,
        room_id: roomId,
        day_of_week: dayMapping[day] || day,
        start_time: startTime,
        end_time: endTime
    };
    
    try {
        const url = scheduleId ? `${API_BASE}/schedules/${scheduleId}` : `${API_BASE}/schedules`;
        const method = scheduleId ? 'PATCH' : 'POST';
        
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (!response.ok) {
            const errorMsg = result.message || 'Error al guardar horario';
            throw new Error(errorMsg);
        }
        
        showNotification('✅ Horario guardado exitosamente');
        closeScheduleModal();
        await loadSchedules();
    } catch (error) {
        console.error('Error:', error);
        showNotification('❌ ' + error.message, 'error');
    }
});

function generateSchedules() {
    if (confirm('¿Desea generar horarios automáticamente? Esto puede sobrescribir horarios existentes.')) {
        showNotification('🤖 Generando horarios automáticamente...');
        // Implementar generación automática
    }
}

function exportSchedule() {
    showNotification('📊 Exportando horarios...');
    
    if (allSchedules.length === 0) {
        showNotification('❌ No hay horarios para exportar', 'error');
        return;
    }
    
    // Preparar datos para exportar
    const exportData = allSchedules.map(schedule => {
        const dayNames = {
            'monday': 'Lunes',
            'tuesday': 'Martes',
            'wednesday': 'Miércoles',
            'thursday': 'Jueves',
            'friday': 'Viernes',
            'saturday': 'Sábado',
            'Lunes': 'Lunes',
            'Martes': 'Martes',
            'Miércoles': 'Miércoles',
            'Jueves': 'Jueves',
            'Viernes': 'Viernes',
            'Sábado': 'Sábado'
        };
        
        return {
            'Día': dayNames[schedule.day] || schedule.day,
            'Hora Inicio': schedule.start_time,
            'Hora Fin': schedule.end_time,
            'Materia': schedule.subject_name || 'Sin materia',
            'Grupo': schedule.group_name || 'Sin grupo',
            'Docente': schedule.teacher_name || 'Sin docente',
            'Aula': schedule.room_name || 'Sin aula',
            'Tipo': schedule.type || 'Regular'
        };
    });
    
    // Ordenar por día y hora
    const dayOrder = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    exportData.sort((a, b) => {
        const dayCompare = dayOrder.indexOf(a['Día']) - dayOrder.indexOf(b['Día']);
        if (dayCompare !== 0) return dayCompare;
        return a['Hora Inicio'].localeCompare(b['Hora Inicio']);
    });
    
    // Convertir a CSV
    const headers = Object.keys(exportData[0] || {});
    const csvContent = [
        headers.join(','),
        ...exportData.map(row => headers.map(header => {
            const value = row[header] || '';
            // Escapar comillas y comas
            return `"${String(value).replace(/"/g, '""')}"`;
        }).join(','))
    ].join('\n');
    
    // Crear y descargar archivo
    const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    const fecha = new Date().toISOString().split('T')[0];
    
    link.setAttribute('href', url);
    link.setAttribute('download', `horarios_${fecha}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('✅ Horarios exportados exitosamente');
}

// Filters
function applyFilters() {
    const groupFilter = document.getElementById('groupFilter').value;
    const searchTerm = document.getElementById('searchSchedules')?.value.toLowerCase() || '';
    const conflictFilter = document.getElementById('conflictFilter')?.value || '';
    
    filteredSchedules = allSchedules.filter(schedule => {
        const matchesGroup = !groupFilter || schedule.group_name === groupFilter;
        const matchesSearch = !searchTerm || 
            schedule.subject_name.toLowerCase().includes(searchTerm) ||
            schedule.teacher_name.toLowerCase().includes(searchTerm) ||
            schedule.room_name.toLowerCase().includes(searchTerm);
        
        let matchesConflict = true;
        if (conflictFilter === 'conflicts') {
            matchesConflict = schedule.has_conflicts;
        } else if (conflictFilter === 'no-conflicts') {
            matchesConflict = !schedule.has_conflicts;
        }
        
        return matchesGroup && matchesSearch && matchesConflict;
    });
    
    renderCurrentView();
}

// Event listeners
document.getElementById('groupFilter').addEventListener('change', applyFilters);
document.getElementById('searchSchedules')?.addEventListener('input', applyFilters);
document.getElementById('conflictFilter')?.addEventListener('change', applyFilters);

// Load initial data - Cargar asignaciones primero, luego horarios
async function loadInitialData() {
    await Promise.all([loadAssignments(), loadRooms()]);
    await loadSchedules();
}

loadInitialData();
</script>

</body>
</html>