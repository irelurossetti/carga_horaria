@extends('layouts.admin')

@section('title', 'Progreso del Sílabo')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Progreso del Sílabo</h1>
            <p class="text-gray-600 mt-1">Visualiza el avance de contenido en tus grupos</p>
        </div>
    </div>

    <!-- Filtro por Grupo -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Grupo</label>
                <select id="groupSelect" onchange="loadProgress()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                    <option value="">Seleccionar grupo...</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Progreso General -->
    <div id="progressSection" class="hidden bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-gray-900">Progreso General</h3>
                <span id="progressPercent" class="text-2xl font-bold text-brand-primary">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-6">
                <div id="progressBar" class="bg-brand-primary h-6 rounded-full transition-all flex items-center justify-end pr-3" style="width: 0%">
                    <span class="text-white text-xs font-medium"></span>
                </div>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mt-2">
                <span id="coveredCount">0 temas cubiertos</span>
                <span id="totalCount">de 0 totales</span>
            </div>
        </div>

        <!-- Lista de Temas -->
        <div>
            <h4 class="text-md font-semibold text-gray-900 mb-4">Detalle de Temas</h4>
            <div id="topicsList" class="space-y-3">
            </div>
        </div>
    </div>

    <!-- Mensaje cuando no hay datos -->
    <div id="noDataMessage" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
        <svg class="w-16 h-16 text-blue-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        <p class="text-gray-600">Selecciona un grupo para ver el progreso del sílabo</p>
    </div>
</div>

<script>
let groups = [];
let topics = [];

document.addEventListener('DOMContentLoaded', function() {
    loadGroups();
});

async function loadGroups() {
    try {
        const response = await fetch('/api/groups');
        const data = await response.json();
        groups = data.data || [];
        
        const select = document.getElementById('groupSelect');
        select.innerHTML = '<option value="">Seleccionar grupo...</option>' +
            groups.map(g => `<option value="${g.id}">${g.name} - ${g.subject?.name || 'Sin materia'}</option>`).join('');
    } catch (error) {
        console.error('Error:', error);
    }
}

async function loadProgress() {
    const groupId = document.getElementById('groupSelect').value;
    
    if (!groupId) {
        document.getElementById('progressSection').classList.add('hidden');
        document.getElementById('noDataMessage').classList.remove('hidden');
        return;
    }
    
    const group = groups.find(g => g.id == groupId);
    if (!group || !group.subject_id) {
        document.getElementById('noDataMessage').classList.remove('hidden');
        document.getElementById('progressSection').classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(`/api/syllabus-topics?subject_id=${group.subject_id}`);
        topics = await response.json();
        
        if (topics.length === 0) {
            document.getElementById('noDataMessage').classList.remove('hidden');
            document.getElementById('progressSection').classList.add('hidden');
            return;
        }
        
        // Simular progreso (en producción vendría del backend)
        const coveredCount = Math.floor(topics.length * 0.6);
        const progress = Math.round((coveredCount / topics.length) * 100);
        
        displayProgress(topics, coveredCount, progress);
        
        document.getElementById('progressSection').classList.remove('hidden');
        document.getElementById('noDataMessage').classList.add('hidden');
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayProgress(topics, coveredCount, progress) {
    document.getElementById('progressPercent').textContent = progress + '%';
    document.getElementById('progressBar').style.width = progress + '%';
    document.getElementById('coveredCount').textContent = `${coveredCount} temas cubiertos`;
    document.getElementById('totalCount').textContent = `de ${topics.length} totales`;
    
    const topicsList = document.getElementById('topicsList');
    topicsList.innerHTML = topics.map((topic, index) => {
        const isCovered = index < coveredCount;
        return `
            <div class="flex items-start gap-4 p-4 border border-gray-200 rounded-lg ${isCovered ? 'bg-green-50 border-green-200' : 'bg-gray-50'}">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full ${isCovered ? 'bg-green-500' : 'bg-gray-300'} flex items-center justify-center text-white font-bold">
                        ${isCovered ? '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>' : topic.order_index}
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="font-semibold text-gray-900">${topic.unit_name}</h5>
                        <span class="px-3 py-1 text-xs font-medium rounded-full ${isCovered ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600'}">
                            ${isCovered ? 'Cubierto' : 'Pendiente'}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">${topic.topic_description}</p>
                    ${isCovered ? '<p class="text-xs text-green-600 mt-2"><i class="fas fa-check-circle mr-1"></i>Visto en clase</p>' : ''}
                </div>
            </div>
        `;
    }).join('');
}
</script>
@endsection
