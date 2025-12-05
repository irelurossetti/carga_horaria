@extends('layouts.admin')

@section('title', 'Reservar Aulas Liberadas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reservar Aulas Liberadas</h1>
            <p class="text-gray-600 mt-1">Gestiona y reserva aulas que han sido liberadas por cancelaciones</p>
        </div>
        <button onclick="showNewReservation()" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
            <i class="fas fa-plus mr-2"></i>Nueva Reserva
        </button>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Reservas Activas</p>
                    <p class="text-2xl font-bold text-gray-900" id="activeReservations">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pendientes</p>
                    <p class="text-2xl font-bold text-yellow-600" id="pendingReservations">0</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Aulas Liberadas</p>
                    <p class="text-2xl font-bold text-green-600" id="freedRooms">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-door-open text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Completadas</p>
                    <p class="text-2xl font-bold text-brand-primary" id="completedReservations">0</p>
                </div>
                <div class="w-12 h-12 bg-brand-primary/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-double text-brand-primary text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select id="filterStatus" onchange="filterReservations()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="">Todos</option>
                    <option value="pending">Pendiente</option>
                    <option value="approved">Aprobada</option>
                    <option value="rejected">Rechazada</option>
                    <option value="completed">Completada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date" id="filterDateFrom" onchange="filterReservations()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date" id="filterDateTo" onchange="filterReservations()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" id="searchReservation" onkeyup="filterReservations()" placeholder="Aula, docente..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>
        </div>
    </div>

    <!-- Tabla de Reservas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody id="reservationsTable" class="divide-y divide-gray-200">
                    <!-- Contenido dinámico -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nueva Reserva -->
    <div id="newReservationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-900">Nueva Reserva de Aula</h3>
                <button onclick="closeNewReservation()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="reservationForm" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aula *</label>
                        <select id="roomId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                            <option value="">Seleccionar aula...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                        <input type="date" id="reservationDate" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora Inicio *</label>
                        <input type="time" id="startTime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora Fin *</label>
                        <input type="time" id="endTime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de la Reserva *</label>
                    <textarea id="reason" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary" placeholder="Describe el motivo de la reserva..."></textarea>
                </div>
                
                <!-- Sección de Recursos Físicos -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-sm font-medium text-gray-700">Recursos Físicos (Opcional)</label>
                        <button type="button" onclick="addResourceRow()" class="text-sm text-brand-primary hover:text-brand-primary-dark">
                            <i class="fas fa-plus mr-1"></i>Agregar Recurso
                        </button>
                    </div>
                    <div id="resourcesContainer" class="space-y-2">
                        <!-- Recursos dinámicos -->
                    </div>
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
                        <i class="fas fa-save mr-2"></i>Crear Reserva
                    </button>
                    <button type="button" onclick="closeNewReservation()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let reservations = [];
let rooms = [];

document.addEventListener('DOMContentLoaded', function() {
    loadReservations();
    loadRooms();
    
    // Pre-llenar si viene de consulta de aulas
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('room_id')) {
        setTimeout(() => {
            showNewReservation();
            document.getElementById('roomId').value = urlParams.get('room_id');
            document.getElementById('reservationDate').value = urlParams.get('date');
            document.getElementById('startTime').value = urlParams.get('start_time');
            document.getElementById('endTime').value = urlParams.get('end_time');
        }, 500);
    }
    
    document.getElementById('reservationForm').addEventListener('submit', handleSubmit);
});

async function loadReservations() {
    console.log('Cargando reservas desde API...');
    try {
        const response = await fetch('/api/reservations', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Reservas recibidas:', data);
        
        // Transformar datos de la API al formato esperado
        reservations = (data.data || data || []).map(r => ({
            id: r.id,
            room: r.room?.name || 'N/A',
            room_id: r.room_id,
            requester: r.teacher?.name || 'Admin',
            date: r.reserved_at ? r.reserved_at.split(' ')[0] : '',
            start_time: r.reserved_at ? r.reserved_at.split(' ')[1].substring(0, 5) : '',
            end_time: r.expires_at ? r.expires_at.split(' ')[1].substring(0, 5) : '',
            reason: r.notes || '',
            status: 'approved', // Por defecto aprobada
            resources: r.resources || []
        }));
        
        console.log('Reservas transformadas:', reservations);
        updateStats();
        displayReservations(reservations);
    } catch (error) {
        console.error('Error cargando reservas:', error);
        reservations = [];
        updateStats();
        displayReservations(reservations);
    }
}

async function loadRooms() {
    console.log('Cargando aulas...');
    try {
        const response = await fetch('/api/rooms', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Datos recibidos:', data);
        
        rooms = data.data || data || [];
        console.log('Total aulas:', rooms.length);
        
        const select = document.getElementById('roomId');
        if (!select) {
            console.error('Elemento roomId no encontrado');
            return;
        }
        
        if (rooms.length === 0) {
            select.innerHTML = '<option value="">No hay aulas disponibles</option>';
        } else {
            select.innerHTML = '<option value="">Seleccionar aula...</option>' +
                rooms.map(room => `<option value="${room.id}">${room.name} (Cap: ${room.capacity || 'N/A'})</option>`).join('');
        }
        console.log('Aulas cargadas exitosamente');
    } catch (error) {
        console.error('Error cargando aulas:', error);
        const select = document.getElementById('roomId');
        if (select) {
            select.innerHTML = '<option value="">Error al cargar aulas</option>';
        }
    }
}

function updateStats() {
    const active = reservations.filter(r => r.status === 'approved').length;
    const pending = reservations.filter(r => r.status === 'pending').length;
    const completed = reservations.filter(r => r.status === 'completed').length;
    
    document.getElementById('activeReservations').textContent = active;
    document.getElementById('pendingReservations').textContent = pending;
    document.getElementById('completedReservations').textContent = completed;
    document.getElementById('freedRooms').textContent = '5'; // Simulado
}

function displayReservations(data) {
    const tbody = document.getElementById('reservationsTable');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No hay reservas registradas</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(res => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4">
                <div class="font-medium text-gray-900">${res.room}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">${res.requester}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${formatDate(res.date)}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${res.start_time} - ${res.end_time}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${res.reason}</td>
            <td class="px-6 py-4">
                ${getStatusBadge(res.status)}
            </td>
            <td class="px-6 py-4">
                <div class="flex gap-2">
                    ${res.status === 'pending' ? `
                        <button onclick="approveReservation(${res.id})" class="text-green-600 hover:text-green-700" title="Aprobar">
                            <i class="fas fa-check"></i>
                        </button>
                        <button onclick="rejectReservation(${res.id})" class="text-red-600 hover:text-red-700" title="Rechazar">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : ''}
                    <button onclick="deleteReservation(${res.id})" class="text-gray-600 hover:text-gray-700" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function getStatusBadge(status) {
    const badges = {
        pending: '<span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded">Pendiente</span>',
        approved: '<span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Aprobada</span>',
        rejected: '<span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-medium rounded">Rechazada</span>',
        completed: '<span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">Completada</span>'
    };
    return badges[status] || status;
}

function formatDate(dateStr) {
    const date = new Date(dateStr + 'T00:00:00');
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function showNewReservation() {
    document.getElementById('newReservationModal').classList.remove('hidden');
}

function closeNewReservation() {
    document.getElementById('newReservationModal').classList.add('hidden');
    document.getElementById('reservationForm').reset();
}

async function handleSubmit(e) {
    e.preventDefault();
    
    const date = document.getElementById('reservationDate').value;
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    
    const formData = {
        room_id: parseInt(document.getElementById('roomId').value),
        reserved_at: `${date} ${startTime}:00`,
        expires_at: `${date} ${endTime}:00`,
        notes: document.getElementById('reason').value,
        resources: getSelectedResources()
    };
    
    console.log('Enviando reserva:', formData);
    
    try {
        const response = await fetch('/api/reservations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(formData)
        });
        
        console.log('Response status:', response.status);
        
        if (response.ok) {
            const data = await response.json();
            console.log('Reserva creada:', data);
            alert('✅ Reserva creada exitosamente' + (formData.resources.length > 0 ? ' con ' + formData.resources.length + ' recurso(s) asignado(s)' : ''));
            closeNewReservation();
            loadReservations();
        } else {
            const error = await response.json();
            console.error('Error del servidor:', error);
            
            if (error.unavailable_resources) {
                const resourceNames = error.unavailable_resources.map(r => r.name).join(', ');
                alert('❌ ' + error.message + ':\n' + resourceNames);
            } else {
                alert('❌ Error: ' + (error.message || 'No se pudo crear la reserva'));
            }
        }
    } catch (error) {
        console.error('Error de red:', error);
        alert('❌ Error de conexión al crear la reserva');
    }
}

async function approveReservation(id) {
    if (confirm('¿Aprobar esta reserva?')) {
        const reservation = reservations.find(r => r.id === id);
        if (reservation) {
            reservation.status = 'approved';
            updateStats();
            displayReservations(reservations);
        }
    }
}

async function rejectReservation(id) {
    if (confirm('¿Rechazar esta reserva?')) {
        const reservation = reservations.find(r => r.id === id);
        if (reservation) {
            reservation.status = 'rejected';
            updateStats();
            displayReservations(reservations);
        }
    }
}

async function deleteReservation(id) {
    if (confirm('¿Eliminar esta reserva?')) {
        reservations = reservations.filter(r => r.id !== id);
        updateStats();
        displayReservations(reservations);
    }
}

function filterReservations() {
    const status = document.getElementById('filterStatus').value;
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;
    const search = document.getElementById('searchReservation').value.toLowerCase();
    
    let filtered = reservations;
    
    if (status) {
        filtered = filtered.filter(r => r.status === status);
    }
    
    if (dateFrom) {
        filtered = filtered.filter(r => r.date >= dateFrom);
    }
    
    if (dateTo) {
        filtered = filtered.filter(r => r.date <= dateTo);
    }
    
    if (search) {
        filtered = filtered.filter(r => 
            r.room.toLowerCase().includes(search) ||
            r.requester.toLowerCase().includes(search) ||
            r.reason.toLowerCase().includes(search)
        );
    }
    
    displayReservations(filtered);
}

// ============================================
// FUNCIONES PARA RECURSOS FÍSICOS
// ============================================
let availableResources = [];

async function loadAvailableResources() {
    try {
        const response = await fetch('/api/resources?status=Disponible', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            availableResources = await response.json();
        }
    } catch (error) {
        console.error('Error cargando recursos:', error);
    }
}

function addResourceRow() {
    const container = document.getElementById('resourcesContainer');
    const rowId = 'resource-row-' + Date.now();
    
    const row = document.createElement('div');
    row.id = rowId;
    row.className = 'flex gap-2 items-start';
    row.innerHTML = `
        <div class="flex-1">
            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-primary">
                <option value="">Seleccionar recurso...</option>
                ${availableResources.map(r => `
                    <option value="${r.id}" data-name="${r.name}" data-type="${r.type}">
                        ${r.name} (${r.type})
                    </option>
                `).join('')}
            </select>
        </div>
        <div class="w-24">
            <input type="number" min="1" value="1" placeholder="Cant." 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-primary">
        </div>
        <div class="flex-1">
            <input type="text" placeholder="Notas (opcional)" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-primary">
        </div>
        <button type="button" onclick="removeResourceRow('${rowId}')" 
                class="px-3 py-2 text-red-600 hover:text-red-800">
            <i class="fas fa-trash"></i>
        </button>
    `;
    
    container.appendChild(row);
}

function removeResourceRow(rowId) {
    document.getElementById(rowId).remove();
}

function getSelectedResources() {
    const container = document.getElementById('resourcesContainer');
    const rows = container.querySelectorAll('[id^="resource-row-"]');
    const resources = [];
    
    rows.forEach(row => {
        const select = row.querySelector('select');
        const quantity = row.querySelector('input[type="number"]');
        const notes = row.querySelector('input[type="text"]');
        
        if (select.value) {
            resources.push({
                id: parseInt(select.value),
                quantity: parseInt(quantity.value) || 1,
                notes: notes.value || ''
            });
        }
    });
    
    return resources;
}

// Modificar showNewReservation para cargar recursos
const originalShowNewReservation = showNewReservation;
showNewReservation = function() {
    originalShowNewReservation();
    loadAvailableResources();
    document.getElementById('resourcesContainer').innerHTML = '';
};
</script>
@endsection
