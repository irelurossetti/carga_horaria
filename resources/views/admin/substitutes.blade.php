@extends('layouts.admin')

@section('title', 'Gestión de Suplencias')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestión de Suplencias</h1>
            <p class="text-gray-600 mt-1">Administra las suplencias de docentes ausentes</p>
        </div>
        <button onclick="showSearchModal()" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
            <i class="fas fa-search mr-2"></i>Buscar Suplente
        </button>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Suplencias</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalSubstitutes">0</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-friends text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Este Mes</p>
                    <p class="text-2xl font-bold text-green-600" id="monthSubstitutes">0</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Suplente Más Activo</p>
                    <p class="text-lg font-bold text-brand-primary" id="topSubstitute">-</p>
                </div>
                <div class="w-12 h-12 bg-brand-primary/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-trophy text-brand-primary text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date" id="filterStartDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date" id="filterEndDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Suplente</label>
                <select id="filterSubstitute" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="">Todos</option>
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="applyFilters()" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla de Suplencias -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Materia/Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Docente Original</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Suplente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aula</th>
                    </tr>
                </thead>
                <tbody id="substitutesTable" class="divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Cargando suplencias...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Buscar Suplente -->
<div id="searchModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-900">Buscar Docente Suplente</h3>
            <button onclick="closeSearchModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <form id="searchForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                        <input type="date" id="searchDate" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora Inicio *</label>
                        <input type="time" id="searchStartTime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hora Fin *</label>
                        <input type="time" id="searchEndTime" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Docente Ausente (opcional)</label>
                    <select id="searchExcludeTeacher" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                        <option value="">Seleccionar...</option>
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
                    <i class="fas fa-search mr-2"></i>Buscar Disponibles
                </button>
            </form>

            <div id="searchResults" class="mt-6 hidden">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Docentes Disponibles (<span id="resultCount">0</span>)</h4>
                <div id="resultsContainer" class="space-y-2 max-h-96 overflow-y-auto"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asignar Suplente -->
<div id="assignModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-900">Confirmar Asignación</h3>
            <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="assignForm" class="p-6 space-y-4">
            <input type="hidden" id="assignScheduleId">
            <input type="hidden" id="assignSubstituteId">
            <input type="hidden" id="assignOriginalId">
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-gray-700"><strong>Suplente:</strong> <span id="assignSubstituteName"></span></p>
                <p class="text-sm text-gray-700 mt-1"><strong>Horario:</strong> <span id="assignScheduleInfo"></span></p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Motivo (opcional)</label>
                <textarea id="assignReason" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary" placeholder="Ej: Licencia médica, permiso personal..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeAssignModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
                    <i class="fas fa-check mr-2"></i>Asignar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSubstitutes();
    loadStats();
    loadTeachers();
    
    // Event listener para el formulario de búsqueda
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', handleSearch);
    }
});

async function loadSubstitutes() {
    try {
        const response = await fetch('/api/substitutes', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        const result = await response.json();
        
        if (!result.success) {
            console.error('Error del servidor:', result.error);
        }
        
        const substitutes = result.data || [];
        renderSubstitutes(substitutes);
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('substitutesTable').innerHTML = `
            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">
                <i class="fas fa-info-circle mr-2"></i>No hay suplencias registradas aún
            </td></tr>
        `;
    }
}

function renderSubstitutes(substitutes) {
    const tbody = document.getElementById('substitutesTable');
    
    if (substitutes.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay suplencias registradas</td></tr>
        `;
        return;
    }
    
    tbody.innerHTML = substitutes.map(s => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${s.date || new Date(s.created_at).toLocaleDateString()}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                ${s.schedule?.start_time?.substring(0,5)} - ${s.schedule?.end_time?.substring(0,5)}
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
                ${s.schedule?.group?.subject?.name || 'N/A'}<br>
                <span class="text-gray-500">${s.schedule?.group?.name || ''}</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
                ${s.original_teacher?.name || 'N/A'}
            </td>
            <td class="px-6 py-4 text-sm">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${s.external_substitute_name ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'}">
                    ${s.external_substitute_name || s.substitute_teacher?.name || 'N/A'}
                    ${s.external_substitute_name ? ' (Externo)' : ''}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                ${s.schedule?.room?.name || 'N/A'}
            </td>
        </tr>
    `).join('');
}

async function loadStats() {
    try {
        const response = await fetch('/api/substitutes/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        const result = await response.json();
        
        if (!result.success) {
            console.error('Error del servidor:', result.error);
        }
        
        const stats = result.data || { total: 0, by_teacher: [] };
        
        document.getElementById('totalSubstitutes').textContent = stats.total || 0;
        document.getElementById('monthSubstitutes').textContent = stats.total || 0;
        
        if (stats.by_teacher && stats.by_teacher.length > 0) {
            const top = stats.by_teacher[0];
            document.getElementById('topSubstitute').textContent = top.teacher || '-';
        }
    } catch (error) {
        console.error('Error:', error);
        document.getElementById('totalSubstitutes').textContent = '0';
        document.getElementById('monthSubstitutes').textContent = '0';
    }
}

async function loadTeachers() {
    try {
        const response = await fetch('/api/teachers', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) return;
        
        const result = await response.json();
        const teachers = result.data || result || [];
        
        const select = document.getElementById('searchExcludeTeacher');
        select.innerHTML = '<option value="">Seleccionar...</option>' +
            teachers.map(t => `<option value="${t.id}">${t.name}</option>`).join('');
    } catch (error) {
        console.error('Error:', error);
    }
}

function showSearchModal() {
    document.getElementById('searchModal').classList.remove('hidden');
    document.getElementById('searchDate').value = new Date().toISOString().split('T')[0];
}

function closeSearchModal() {
    document.getElementById('searchModal').classList.add('hidden');
    document.getElementById('searchResults').classList.add('hidden');
}

async function handleSearch(e) {
    e.preventDefault();
    
    const date = document.getElementById('searchDate').value;
    const startTime = document.getElementById('searchStartTime').value;
    const endTime = document.getElementById('searchEndTime').value;
    const excludeTeacherId = document.getElementById('searchExcludeTeacher').value;
    
    const params = new URLSearchParams({
        date,
        start_time: startTime,
        end_time: endTime
    });
    
    if (excludeTeacherId) {
        params.append('exclude_teacher_id', excludeTeacherId);
    }
    
    try {
        const response = await fetch(`/api/substitutes/available?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) throw new Error('Error en la búsqueda');
        
        const result = await response.json();
        const teachers = result.data || [];
        
        document.getElementById('resultCount').textContent = teachers.length;
        document.getElementById('searchResults').classList.remove('hidden');
        
        const container = document.getElementById('resultsContainer');
        if (teachers.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500 py-4">No hay docentes disponibles en este horario</p>';
            return;
        }
        
        container.innerHTML = teachers.map(t => `
            <div class="border border-gray-200 rounded-lg p-4 hover:border-brand-primary transition-colors">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h5 class="font-semibold text-gray-900">${t.name}</h5>
                        <p class="text-sm text-gray-600">${t.email}</p>
                        <p class="text-sm text-gray-500 mt-1">Carga del día: ${t.daily_hours || 0} horas</p>
                    </div>
                    <button onclick="selectSubstitute(${t.id}, '${t.name}')" 
                            class="px-3 py-1 bg-brand-primary text-white text-sm rounded hover:bg-brand-primary-dark transition-colors">
                        Seleccionar
                    </button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error:', error);
        alert('Error al buscar docentes disponibles');
    }
}

function selectSubstitute(teacherId, teacherName) {
    // Aquí necesitarías tener el schedule_id y original_teacher_id
    // Por ahora, mostramos un mensaje
    alert(`Funcionalidad completa: Asignar ${teacherName} como suplente.\nNecesitas seleccionar desde una cancelación o incidencia específica.`);
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}

function applyFilters() {
    // Implementar filtros
    loadSubstitutes();
}
</script>
@endpush
