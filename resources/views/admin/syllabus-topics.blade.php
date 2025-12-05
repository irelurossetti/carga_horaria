@extends('layouts.admin')

@section('title', 'Gestión de Temas del Sílabo')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestión de Temas del Sílabo</h1>
            <p class="text-gray-600 mt-1">Configura los temas y unidades de cada materia</p>
        </div>
        <button onclick="openAddModal()" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark transition-colors">
            <i class="fas fa-plus mr-2"></i>Agregar Tema
        </button>
    </div>

    <!-- Filtro por Materia -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                <select id="subjectFilter" onchange="loadTopics()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="">Seleccionar materia...</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Lista de Temas -->
    <div id="topicsSection" class="hidden bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Temas Configurados</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Orden</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody id="topicsTable" class="divide-y divide-gray-200">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Agregar/Editar Tema -->
<div id="topicModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 id="modalTitle" class="text-xl font-semibold text-gray-900">Agregar Tema</h3>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="topicId">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                <select id="topicSubject" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="">Seleccionar materia...</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unidad</label>
                <input type="text" id="topicUnit" placeholder="Ej: Unidad 1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción del Tema</label>
                <textarea id="topicDescription" rows="4" placeholder="Describe el contenido del tema..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Orden</label>
                <input type="number" id="topicOrder" min="1" placeholder="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancelar
            </button>
            <button onclick="saveTopic()" class="px-4 py-2 bg-brand-primary text-white rounded-lg hover:bg-brand-primary-dark">
                Guardar
            </button>
        </div>
    </div>
</div>

<script>
let subjects = [];
let topics = [];

document.addEventListener('DOMContentLoaded', function() {
    loadSubjects();
});

async function loadSubjects() {
    try {
        console.log('Cargando materias...');
        const response = await fetch('/api/subjects');
        console.log('Response status:', response.status);
        
        const data = await response.json();
        console.log('Data received:', data);
        
        // El endpoint devuelve directamente el array
        subjects = Array.isArray(data) ? data : (data.data || []);
        console.log('Subjects array:', subjects, 'Total:', subjects.length);
        
        if (subjects.length === 0) {
            alert('No hay materias en la base de datos. Por favor crea materias primero.');
            return;
        }
        
        const selects = ['subjectFilter', 'topicSubject'];
        selects.forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) {
                select.innerHTML = '<option value="">Seleccionar materia...</option>' +
                    subjects.map(s => `<option value="${s.id}">${s.name || s.code || 'Materia ' + s.id}</option>`).join('');
                console.log('Select populated:', selectId);
            }
        });
    } catch (error) {
        console.error('Error loading subjects:', error);
        alert('Error al cargar materias: ' + error.message);
    }
}

async function loadTopics() {
    const subjectId = document.getElementById('subjectFilter').value;
    
    if (!subjectId) {
        document.getElementById('topicsSection').classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`/api/syllabus-topics?subject_id=${subjectId}`);
        topics = await response.json();
        
        displayTopics(topics);
        document.getElementById('topicsSection').classList.remove('hidden');
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayTopics(topics) {
    const tbody = document.getElementById('topicsTable');
    
    if (topics.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                    No hay temas configurados para esta materia
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = topics.map(topic => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 text-center font-medium text-gray-900">${topic.order_index}</td>
            <td class="px-6 py-4 font-medium text-gray-900">${topic.unit_name}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${topic.topic_description}</td>
            <td class="px-6 py-4 text-center">
                <button onclick="editTopic(${topic.id})" class="text-blue-600 hover:text-blue-800 mr-3">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteTopic(${topic.id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Agregar Tema';
    document.getElementById('topicId').value = '';
    document.getElementById('topicSubject').value = document.getElementById('subjectFilter').value || '';
    document.getElementById('topicUnit').value = '';
    document.getElementById('topicDescription').value = '';
    document.getElementById('topicOrder').value = '';
    document.getElementById('topicModal').classList.remove('hidden');
}

function editTopic(id) {
    const topic = topics.find(t => t.id === id);
    if (!topic) return;
    
    document.getElementById('modalTitle').textContent = 'Editar Tema';
    document.getElementById('topicId').value = topic.id;
    document.getElementById('topicSubject').value = topic.subject_id;
    document.getElementById('topicUnit').value = topic.unit_name;
    document.getElementById('topicDescription').value = topic.topic_description;
    document.getElementById('topicOrder').value = topic.order_index;
    document.getElementById('topicModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('topicModal').classList.add('hidden');
}

async function saveTopic() {
    const id = document.getElementById('topicId').value;
    const data = {
        subject_id: document.getElementById('topicSubject').value,
        unit_name: document.getElementById('topicUnit').value,
        topic_description: document.getElementById('topicDescription').value,
        order_index: document.getElementById('topicOrder').value || null
    };
    
    if (!data.subject_id || !data.unit_name || !data.topic_description) {
        alert('Por favor completa todos los campos requeridos');
        return;
    }
    
    try {
        const url = id ? `/api/syllabus-topics/${id}` : '/api/syllabus-topics';
        const method = id ? 'PATCH' : 'POST';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.content;
        }
        
        const response = await fetch(url, {
            method: method,
            headers: headers,
            body: JSON.stringify(data)
        });
        
        const responseData = await response.json();
        
        if (response.ok) {
            closeModal();
            loadTopics();
            alert('Tema guardado exitosamente');
        } else {
            console.error('Error response:', responseData);
            alert('Error al guardar el tema: ' + (responseData.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar el tema: ' + error.message);
    }
}

async function deleteTopic(id) {
    if (!confirm('¿Estás seguro de eliminar este tema?')) return;
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const headers = {
            'Accept': 'application/json'
        };
        
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken.content;
        }
        
        const response = await fetch(`/api/syllabus-topics/${id}`, {
            method: 'DELETE',
            headers: headers
        });
        
        const responseData = await response.json();
        
        if (response.ok) {
            loadTopics();
            alert('Tema eliminado exitosamente');
        } else {
            console.error('Error response:', responseData);
            alert('Error al eliminar el tema: ' + (responseData.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar el tema: ' + error.message);
    }
}
</script>
@endsection
