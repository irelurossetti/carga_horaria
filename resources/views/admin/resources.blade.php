@extends('layouts.admin')

@section('title', 'Gestión de Recursos Físicos')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Recursos Físicos (Inventario)</h1>
            <p class="text-gray-600 mt-1">Gestiona proyectores, laptops y otros recursos del campus</p>
        </div>
        <button onclick="showNewResourceModal()" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
            <i class="fas fa-plus mr-2"></i>Nuevo Recurso
        </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <select id="filterType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                    <option value="">Todos</option>
                    <option value="Proyector">Proyector</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Pizarra Digital">Pizarra Digital</option>
                    <option value="Micrófono">Micrófono</option>
                    <option value="Parlantes">Parlantes</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select id="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                    <option value="">Todos</option>
                    <option value="Disponible">Disponible</option>
                    <option value="En Uso">En Uso</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                    <option value="Dañado">Dañado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" id="searchInput" placeholder="Nombre o serial..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
            </div>
        </div>
    </div>

    <!-- Tabla de Recursos -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody id="resourcesTableBody" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Cargando recursos...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Nuevo/Editar Recurso -->
<div id="resourceModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Nuevo Recurso</h3>
            <button onclick="closeResourceModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="resourceForm" class="space-y-4">
            <input type="hidden" id="resourceId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                    <input type="text" id="resourceName" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                    <select id="resourceType" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Seleccionar...</option>
                        <option value="Proyector">Proyector</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Pizarra Digital">Pizarra Digital</option>
                        <option value="Micrófono">Micrófono</option>
                        <option value="Parlantes">Parlantes</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Número de Serie</label>
                    <input type="text" id="resourceSerial" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                    <select id="resourceStatus" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="Disponible">Disponible</option>
                        <option value="En Uso">En Uso</option>
                        <option value="Mantenimiento">Mantenimiento</option>
                        <option value="Dañado">Dañado</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ubicación</label>
                <input type="text" id="resourceLocation" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="resourceDescription" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeResourceModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
                    <i class="fas fa-save mr-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
let resources = [];

// Cargar recursos
async function loadResources() {
    try {
        const response = await fetch('/api/resources', {
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
        resources = data.data || data;
        renderResources();
    } catch (error) {
        console.error('Error completo:', error);
        const tbody = document.getElementById('resourcesTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-red-500">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Error al cargar recursos: ${error.message}
                </td>
            </tr>
        `;
    }
}

// Renderizar tabla
function renderResources() {
    const tbody = document.getElementById('resourcesTableBody');
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const filterType = document.getElementById('filterType').value;
    const filterStatus = document.getElementById('filterStatus').value;
    
    let filtered = resources.filter(r => {
        const matchSearch = !searchTerm || 
            r.name.toLowerCase().includes(searchTerm) || 
            (r.serial_number && r.serial_number.toLowerCase().includes(searchTerm));
        const matchType = !filterType || r.type === filterType;
        const matchStatus = !filterStatus || r.status === filterStatus;
        return matchSearch && matchType && matchStatus;
    });
    
    if (filtered.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    <i class="fas fa-inbox mr-2"></i>No se encontraron recursos
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = filtered.map(resource => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${resource.name}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                    ${resource.type}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${resource.serial_number || '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                ${getStatusBadge(resource.status)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${resource.location || '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <button onclick="editResource(${resource.id})" class="text-blue-600 hover:text-blue-900">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteResource(${resource.id})" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function getStatusBadge(status) {
    const badges = {
        'Disponible': 'bg-green-100 text-green-800',
        'En Uso': 'bg-yellow-100 text-yellow-800',
        'Mantenimiento': 'bg-orange-100 text-orange-800',
        'Dañado': 'bg-red-100 text-red-800'
    };
    return `<span class="px-2 py-1 text-xs font-medium rounded-full ${badges[status] || 'bg-gray-100 text-gray-800'}">${status}</span>`;
}

// Modal
function showNewResourceModal() {
    document.getElementById('modalTitle').textContent = 'Nuevo Recurso';
    document.getElementById('resourceForm').reset();
    document.getElementById('resourceId').value = '';
    document.getElementById('resourceModal').classList.remove('hidden');
}

function closeResourceModal() {
    document.getElementById('resourceModal').classList.add('hidden');
}

function editResource(id) {
    const resource = resources.find(r => r.id === id);
    if (!resource) return;
    
    document.getElementById('modalTitle').textContent = 'Editar Recurso';
    document.getElementById('resourceId').value = resource.id;
    document.getElementById('resourceName').value = resource.name;
    document.getElementById('resourceType').value = resource.type;
    document.getElementById('resourceSerial').value = resource.serial_number || '';
    document.getElementById('resourceStatus').value = resource.status;
    document.getElementById('resourceLocation').value = resource.location || '';
    document.getElementById('resourceDescription').value = resource.description || '';
    document.getElementById('resourceModal').classList.remove('hidden');
}

// Guardar
document.getElementById('resourceForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('resourceId').value;
    const data = {
        name: document.getElementById('resourceName').value,
        type: document.getElementById('resourceType').value,
        serial_number: document.getElementById('resourceSerial').value,
        status: document.getElementById('resourceStatus').value,
        location: document.getElementById('resourceLocation').value,
        description: document.getElementById('resourceDescription').value
    };
    
    try {
        const url = id ? `/api/resources/${id}` : '/api/resources';
        const method = id ? 'PATCH' : 'POST';
        
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            showNotification(id ? 'Recurso actualizado' : 'Recurso creado', 'success');
            closeResourceModal();
            loadResources();
        } else {
            const error = await response.json();
            showNotification(error.message || 'Error al guardar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al guardar recurso', 'error');
    }
});

// Eliminar
async function deleteResource(id) {
    if (!confirm('¿Eliminar este recurso?')) return;
    
    try {
        const response = await fetch(`/api/resources/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            showNotification('Recurso eliminado', 'success');
            loadResources();
        } else {
            showNotification('Error al eliminar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error al eliminar recurso', 'error');
    }
}

function showNotification(message, type) {
    // Implementar notificación toast
    alert(message);
}

// Filtros
document.getElementById('filterType').addEventListener('change', renderResources);
document.getElementById('filterStatus').addEventListener('change', renderResources);
document.getElementById('searchInput').addEventListener('input', renderResources);

// Cargar al inicio
loadResources();
}); // Fin DOMContentLoaded
</script>
@endpush
