<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Grupos - FICCT SGA</title>
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
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Grupos</h1>
                <p class="text-gray-500 mt-1">Administra los grupos académicos por materia</p>
            </div>
            <button onclick="openGroupModal()" class="px-6 py-3 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium transition-colors shadow-sm">
                + Nuevo Grupo
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Grupos</p>
                        <p id="totalGroups" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Activos</p>
                        <p id="activeGroups" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Estudiantes</p>
                        <p id="totalStudents" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Materias</p>
                        <p id="totalSubjects" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" id="searchInput" placeholder="Nombre del grupo..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                    <select id="subjectFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Todas las materias</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="clearFilters()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Lista de Grupos</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Capacidad</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Inscritos</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="groupsTable" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-primary mx-auto"></div>
                                <p class="mt-4 text-gray-500">Cargando grupos...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal para Grupo -->
<div id="groupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="groupModalTitle" class="text-xl font-bold text-gray-900">Nuevo Grupo</h3>
                <button onclick="closeGroupModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form id="groupForm" class="p-6 space-y-6">
            <input type="hidden" id="groupId">
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código del Grupo *</label>
                    <input type="text" id="groupCode" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Ej: INF-101-A">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Grupo *</label>
                    <input type="text" id="groupName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Ej: Grupo A">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Materia *</label>
                <select id="groupSubject" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                    <option value="">Seleccionar materia</option>
                </select>
            </div>
            
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Capacidad Máxima *</label>
                    <input type="number" id="groupCapacity" required min="1" max="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="30">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estudiantes Inscritos</label>
                    <input type="number" id="enrolledStudents" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="groupStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="groupDescription" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Descripción del grupo..."></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Horario</label>
                    <input type="text" id="groupSchedule" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Ej: Lun-Mie-Vie 08:00-10:00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aula Asignada</label>
                    <select id="groupRoom" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Sin asignar</option>
                    </select>
                </div>
            </div>
        </form>

        <div class="p-6 pt-0">
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeGroupModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="document.getElementById('groupForm').requestSubmit()" class="px-6 py-2 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium transition-colors">
                    Guardar Grupo
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '/api';
let allGroups = [];
let filteredGroups = [];
let subjects = [];
let rooms = [];

function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg border ${alertClass} z-50 shadow-lg`;
    notification.innerHTML = `<span class="font-medium">${message}</span>`;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

async function loadGroups() {
    try {
        const response = await fetch(`${API_BASE}/groups`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (!response.ok) throw new Error('Error al cargar grupos');
        
        allGroups = await response.json();
        filteredGroups = [...allGroups];
        renderGroups();
        updateStats();
    } catch (error) {
        console.error('Error:', error);
        // Datos de prueba
        allGroups = [
            { id: 1, code: 'INF-101-A', name: 'Grupo A', subject_id: 1, subject_name: 'Introducción a la Programación', capacity: 30, enrolled_students: 25, status: 'active', description: 'Grupo matutino', schedule: 'Lun-Mie-Vie 08:00-10:00', room_id: 1, room_name: 'Aula 101' },
            { id: 2, code: 'INF-101-B', name: 'Grupo B', subject_id: 1, subject_name: 'Introducción a la Programación', capacity: 30, enrolled_students: 28, status: 'active', description: 'Grupo vespertino', schedule: 'Lun-Mie-Vie 14:00-16:00', room_id: 2, room_name: 'Aula 102' },
            { id: 3, code: 'MAT-101-A', name: 'Grupo A', subject_id: 2, subject_name: 'Cálculo I', capacity: 35, enrolled_students: 32, status: 'active', description: 'Grupo único', schedule: 'Mar-Jue 10:00-12:00', room_id: 3, room_name: 'Aula 201' },
        ];
        filteredGroups = [...allGroups];
        renderGroups();
        updateStats();
    }
}

async function loadSubjects() {
    try {
        const response = await fetch(`${API_BASE}/subjects`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (response.ok) {
            subjects = await response.json();
        } else {
            subjects = [
                { id: 1, name: 'Introducción a la Programación', code: 'INF-101' },
                { id: 2, name: 'Cálculo I', code: 'MAT-101' },
                { id: 3, name: 'Estructura de Datos', code: 'INF-201' },
            ];
        }
        
        populateSubjectSelects();
    } catch (error) {
        console.error('Error loading subjects:', error);
        subjects = [
            { id: 1, name: 'Introducción a la Programación', code: 'INF-101' },
            { id: 2, name: 'Cálculo I', code: 'MAT-101' },
            { id: 3, name: 'Estructura de Datos', code: 'INF-201' },
        ];
        populateSubjectSelects();
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
                { id: 1, name: 'Aula 101', floor: 1 },
                { id: 2, name: 'Aula 102', floor: 1 },
                { id: 3, name: 'Aula 201', floor: 2 },
            ];
        }
        
        populateRoomSelect();
    } catch (error) {
        console.error('Error loading rooms:', error);
        rooms = [
            { id: 1, name: 'Aula 101', floor: 1 },
            { id: 2, name: 'Aula 102', floor: 1 },
            { id: 3, name: 'Aula 201', floor: 2 },
        ];
        populateRoomSelect();
    }
}

function populateSubjectSelects() {
    const groupSubjectSelect = document.getElementById('groupSubject');
    const subjectFilterSelect = document.getElementById('subjectFilter');
    
    const subjectOptions = subjects.map(subject => 
        `<option value="${subject.id}">${subject.code} - ${subject.name}</option>`
    ).join('');
    
    groupSubjectSelect.innerHTML = '<option value="">Seleccionar materia</option>' + subjectOptions;
    subjectFilterSelect.innerHTML = '<option value="">Todas las materias</option>' + subjectOptions;
}

function populateRoomSelect() {
    const roomSelect = document.getElementById('groupRoom');
    
    const roomOptions = rooms.map(room => {
        const floorText = room.floor ? `(Piso ${room.floor})` : '';
        return `<option value="${room.id}">${room.name} ${floorText}</option>`;
    }).join('');
    
    roomSelect.innerHTML = '<option value="">Sin asignar</option>' + roomOptions;
}

function renderGroups() {
    const tbody = document.getElementById('groupsTable');
    
    if (filteredGroups.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    No se encontraron grupos
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = filteredGroups.map(group => {
        const occupancyPercentage = group.capacity > 0 ? Math.round((group.enrolled_students / group.capacity) * 100) : 0;
        const occupancyColor = occupancyPercentage >= 90 ? 'text-red-600' : occupancyPercentage >= 70 ? 'text-yellow-600' : 'text-green-600';
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${group.name}</div>
                    <div class="text-sm text-gray-500">${group.description || 'Sin descripción'}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">${group.subject_name}</div>
                    <div class="text-sm text-gray-500">${group.schedule || 'Sin horario'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="text-sm font-medium text-gray-900">${group.capacity}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="text-sm font-medium ${occupancyColor}">${group.enrolled_students}</div>
                    <div class="text-xs text-gray-500">${occupancyPercentage}% ocupado</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="px-3 py-1 rounded-full text-sm font-medium ${group.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${group.status === 'active' ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="editGroup(${group.id})" class="text-blue-600 hover:text-blue-900" title="Editar">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button onclick="deleteGroup(${group.id})" class="text-red-600 hover:text-red-900" title="Eliminar">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function updateStats() {
    document.getElementById('totalGroups').textContent = allGroups.length;
    document.getElementById('activeGroups').textContent = allGroups.filter(g => g.status === 'active').length;
    document.getElementById('totalStudents').textContent = allGroups.reduce((sum, g) => sum + (g.enrolled_students || 0), 0);
    document.getElementById('totalSubjects').textContent = new Set(allGroups.map(g => g.subject_id)).size;
}

function openGroupModal() {
    document.getElementById('groupModalTitle').textContent = 'Nuevo Grupo';
    document.getElementById('groupForm').reset();
    document.getElementById('groupId').value = '';
    document.getElementById('groupStatus').value = 'active';
    document.getElementById('enrolledStudents').value = '0';
    document.getElementById('groupModal').classList.remove('hidden');
    document.getElementById('groupModal').classList.add('flex');
}

function closeGroupModal() {
    document.getElementById('groupModal').classList.add('hidden');
    document.getElementById('groupModal').classList.remove('flex');
}

function editGroup(id) {
    const group = allGroups.find(g => g.id === id);
    if (!group) {
        showNotification('❌ Grupo no encontrado', 'error');
        return;
    }
    
    document.getElementById('groupModalTitle').textContent = 'Editar Grupo';
    document.getElementById('groupId').value = group.id;
    document.getElementById('groupCode').value = group.code || '';
    document.getElementById('groupName').value = group.name;
    document.getElementById('groupSubject').value = group.subject_id;
    document.getElementById('groupCapacity').value = group.capacity;
    document.getElementById('enrolledStudents').value = group.enrolled_students || 0;
    document.getElementById('groupStatus').value = group.status;
    document.getElementById('groupDescription').value = group.description || '';
    document.getElementById('groupSchedule').value = group.schedule || '';
    document.getElementById('groupRoom').value = group.room_id || '';
    
    document.getElementById('groupModal').classList.remove('hidden');
    document.getElementById('groupModal').classList.add('flex');
}

async function deleteGroup(id) {
    if (!confirm('¿Estás seguro de eliminar este grupo?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/groups/${id}`, {
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
        
        showNotification(result.message || '✅ Grupo eliminado exitosamente');
        loadGroups();
    } catch (error) {
        console.error('Error:', error);
        showNotification('❌ ' + error.message, 'error');
    }
}

// Form submission
document.getElementById('groupForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const groupId = document.getElementById('groupId').value;
    const data = {
        code: document.getElementById('groupCode').value,
        name: document.getElementById('groupName').value,
        subject_id: parseInt(document.getElementById('groupSubject').value),
        capacity: parseInt(document.getElementById('groupCapacity').value),
        enrolled_students: parseInt(document.getElementById('enrolledStudents').value) || 0,
        status: document.getElementById('groupStatus').value,
        description: document.getElementById('groupDescription').value,
        schedule: document.getElementById('groupSchedule').value,
        room_id: document.getElementById('groupRoom').value ? parseInt(document.getElementById('groupRoom').value) : null
    };
    
    try {
        const url = groupId ? `${API_BASE}/groups/${groupId}` : `${API_BASE}/groups`;
        const method = groupId ? 'PATCH' : 'POST';
        
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
            throw new Error(result.message || 'Error al guardar');
        }
        
        showNotification(result.message || '✅ Grupo guardado exitosamente');
        closeGroupModal();
        loadGroups();
    } catch (error) {
        console.error('Error:', error);
        showNotification('❌ ' + error.message, 'error');
    }
});

// Filters
function applyFilters() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const subjectId = document.getElementById('subjectFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    filteredGroups = allGroups.filter(group => {
        const matchesSearch = !search || 
            group.name.toLowerCase().includes(search) || 
            group.subject_name.toLowerCase().includes(search);
        const matchesSubject = !subjectId || group.subject_id.toString() === subjectId;
        const matchesStatus = !status || group.status === status;
        
        return matchesSearch && matchesSubject && matchesStatus;
    });
    
    renderGroups();
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('subjectFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filteredGroups = [...allGroups];
    renderGroups();
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('subjectFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);

// Load initial data
Promise.all([loadGroups(), loadSubjects(), loadRooms()]);
</script>

</body>
</html>