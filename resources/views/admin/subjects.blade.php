<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestión de Materias - FICCT SGA</title>
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
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Materias</h1>
                <p class="text-gray-500 mt-1">Administra las materias del sistema académico</p>
            </div>
            <button onclick="openSubjectModal()" class="px-6 py-3 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium transition-colors shadow-sm">
                + Nueva Materia
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Materias</p>
                        <p id="totalSubjects" class="text-2xl font-bold text-gray-900">0</p>
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
                        <p class="text-sm font-medium text-gray-500">Activas</p>
                        <p id="activeSubjects" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Horas Totales</p>
                        <p id="totalHours" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Semestres</p>
                        <p id="totalSemesters" class="text-2xl font-bold text-gray-900">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" id="searchInput" placeholder="Nombre o código..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Semestre</label>
                    <select id="semesterFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Todos los semestres</option>
                        <option value="1">1er Semestre</option>
                        <option value="2">2do Semestre</option>
                        <option value="3">3er Semestre</option>
                        <option value="4">4to Semestre</option>
                        <option value="5">5to Semestre</option>
                        <option value="6">6to Semestre</option>
                        <option value="7">7mo Semestre</option>
                        <option value="8">8vo Semestre</option>
                        <option value="9">9no Semestre</option>
                        <option value="10">10mo Semestre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="active">Activa</option>
                        <option value="inactive">Inactiva</option>
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
                <h2 class="text-lg font-semibold text-gray-900">Lista de Materias</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Semestre</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Horas</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="subjectsTable" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand-primary mx-auto"></div>
                                <p class="mt-4 text-gray-500">Cargando materias...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal para Materia -->
<div id="subjectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 id="subjectModalTitle" class="text-xl font-bold text-gray-900">Nueva Materia</h3>
                <button onclick="closeSubjectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <form id="subjectForm" class="p-6 space-y-6">
            <input type="hidden" id="subjectId">
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                    <input type="text" id="subjectCode" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Ej: INF-101">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                    <input type="text" id="subjectName" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Nombre de la materia">
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Semestre *</label>
                    <select id="subjectSemester" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Seleccionar</option>
                        <option value="1">1er Semestre</option>
                        <option value="2">2do Semestre</option>
                        <option value="3">3er Semestre</option>
                        <option value="4">4to Semestre</option>
                        <option value="5">5to Semestre</option>
                        <option value="6">6to Semestre</option>
                        <option value="7">7mo Semestre</option>
                        <option value="8">8vo Semestre</option>
                        <option value="9">9no Semestre</option>
                        <option value="10">10mo Semestre</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Horas Teóricas *</label>
                    <input type="number" id="theoreticalHours" required min="0" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Horas Prácticas *</label>
                    <input type="number" id="practicalHours" required min="0" max="10" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="0">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="subjectDescription" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Descripción de la materia..."></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prerrequisitos</label>
                    <input type="text" id="prerequisites" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent" placeholder="Ej: INF-100, MAT-101">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="subjectStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="active">Activa</option>
                        <option value="inactive">Inactiva</option>
                    </select>
                </div>
            </div>
        </form>

        <div class="p-6 pt-0">
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeSubjectModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="document.getElementById('subjectForm').requestSubmit()" class="px-6 py-2 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium transition-colors">
                    Guardar Materia
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '/api';
let allSubjects = [];
let filteredSubjects = [];

function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg border ${alertClass} z-50 shadow-lg`;
    notification.innerHTML = `<span class="font-medium">${message}</span>`;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

async function loadSubjects() {
    try {
        const response = await fetch(`${API_BASE}/subjects`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (!response.ok) throw new Error('Error al cargar materias');
        
        allSubjects = await response.json();
        filteredSubjects = [...allSubjects];
        renderSubjects();
        updateStats();
    } catch (error) {
        console.error('Error:', error);
        // Datos de prueba
        allSubjects = [
            { id: 1, code: 'INF-101', name: 'Introducción a la Programación', semester: 1, theoretical_hours: 4, practical_hours: 2, status: 'active', description: 'Fundamentos de programación', prerequisites: 'Ninguno' },
            { id: 2, code: 'MAT-101', name: 'Cálculo I', semester: 1, theoretical_hours: 4, practical_hours: 0, status: 'active', description: 'Cálculo diferencial', prerequisites: 'Ninguno' },
            { id: 3, code: 'INF-201', name: 'Estructura de Datos', semester: 2, theoretical_hours: 3, practical_hours: 3, status: 'active', description: 'Estructuras de datos fundamentales', prerequisites: 'INF-101' },
        ];
        filteredSubjects = [...allSubjects];
        renderSubjects();
        updateStats();
    }
}

function renderSubjects() {
    const tbody = document.getElementById('subjectsTable');
    
    if (filteredSubjects.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    No se encontraron materias
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = filteredSubjects.map(subject => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-medium text-gray-900">${subject.code}</span>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">${subject.name}</div>
                <div class="text-sm text-gray-500">${subject.description || 'Sin descripción'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    ${subject.semester}° Sem
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <div class="text-sm text-gray-900">T: ${subject.theoretical_hours}h</div>
                <div class="text-sm text-gray-500">P: ${subject.practical_hours}h</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="px-3 py-1 rounded-full text-sm font-medium ${subject.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${subject.status === 'active' ? 'Activa' : 'Inactiva'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end gap-2">
                    <button onclick="editSubject(${subject.id})" class="text-blue-600 hover:text-blue-900" title="Editar">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button onclick="deleteSubject(${subject.id})" class="text-red-600 hover:text-red-900" title="Eliminar">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function updateStats() {
    document.getElementById('totalSubjects').textContent = allSubjects.length;
    document.getElementById('activeSubjects').textContent = allSubjects.filter(s => s.status === 'active').length;
    document.getElementById('totalHours').textContent = allSubjects.reduce((sum, s) => sum + (s.theoretical_hours || 0) + (s.practical_hours || 0), 0);
    document.getElementById('totalSemesters').textContent = new Set(allSubjects.map(s => s.semester)).size;
}

function openSubjectModal() {
    document.getElementById('subjectModalTitle').textContent = 'Nueva Materia';
    document.getElementById('subjectForm').reset();
    document.getElementById('subjectId').value = '';
    document.getElementById('subjectStatus').value = 'active';
    document.getElementById('subjectModal').classList.remove('hidden');
    document.getElementById('subjectModal').classList.add('flex');
}

function closeSubjectModal() {
    document.getElementById('subjectModal').classList.add('hidden');
    document.getElementById('subjectModal').classList.remove('flex');
}

function editSubject(id) {
    const subject = allSubjects.find(s => s.id === id);
    if (!subject) {
        showNotification('❌ Materia no encontrada', 'error');
        return;
    }
    
    document.getElementById('subjectModalTitle').textContent = 'Editar Materia';
    document.getElementById('subjectId').value = subject.id;
    document.getElementById('subjectCode').value = subject.code;
    document.getElementById('subjectName').value = subject.name;
    document.getElementById('subjectSemester').value = subject.semester;
    document.getElementById('theoreticalHours').value = subject.theoretical_hours || 0;
    document.getElementById('practicalHours').value = subject.practical_hours || 0;
    document.getElementById('subjectDescription').value = subject.description || '';
    document.getElementById('prerequisites').value = subject.prerequisites || '';
    document.getElementById('subjectStatus').value = subject.status;
    
    document.getElementById('subjectModal').classList.remove('hidden');
    document.getElementById('subjectModal').classList.add('flex');
}

async function deleteSubject(id) {
    if (!confirm('¿Estás seguro de eliminar esta materia?')) return;
    
    try {
        const response = await fetch(`${API_BASE}/subjects/${id}`, {
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
        
        showNotification(result.message || '✅ Materia eliminada exitosamente');
        loadSubjects();
    } catch (error) {
        console.error('Error:', error);
        showNotification('❌ ' + error.message, 'error');
    }
}

// Form submission
document.getElementById('subjectForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const subjectId = document.getElementById('subjectId').value;
    const data = {
        code: document.getElementById('subjectCode').value,
        name: document.getElementById('subjectName').value,
        semester: parseInt(document.getElementById('subjectSemester').value),
        theoretical_hours: parseInt(document.getElementById('theoreticalHours').value) || 0,
        practical_hours: parseInt(document.getElementById('practicalHours').value) || 0,
        description: document.getElementById('subjectDescription').value,
        prerequisites: document.getElementById('prerequisites').value,
        status: document.getElementById('subjectStatus').value
    };
    
    try {
        const url = subjectId ? `${API_BASE}/subjects/${subjectId}` : `${API_BASE}/subjects`;
        const method = subjectId ? 'PATCH' : 'POST';
        
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
        
        showNotification(result.message || '✅ Materia guardada exitosamente');
        closeSubjectModal();
        loadSubjects();
    } catch (error) {
        console.error('Error:', error);
        showNotification('❌ ' + error.message, 'error');
    }
});

// Filters
function applyFilters() {
    const search = document.getElementById('searchInput')?.value?.toLowerCase() || '';
    const semester = document.getElementById('semesterFilter')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';
    
    console.log('Aplicando filtros:', { search, semester, status });
    console.log('Total materias:', allSubjects.length);
    
    filteredSubjects = allSubjects.filter(subject => {
        const matchesSearch = !search || 
            (subject.name && subject.name.toLowerCase().includes(search)) || 
            (subject.code && subject.code.toLowerCase().includes(search));
        const matchesSemester = !semester || (subject.semester && subject.semester.toString() === semester);
        const matchesStatus = !status || subject.status === status;
        
        return matchesSearch && matchesSemester && matchesStatus;
    });
    
    console.log('Materias filtradas:', filteredSubjects.length);
    renderSubjects();
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('semesterFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filteredSubjects = [...allSubjects];
    renderSubjects();
}

// Event listeners
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('semesterFilter').addEventListener('change', applyFilters);
document.getElementById('statusFilter').addEventListener('change', applyFilters);

// Load initial data
loadSubjects();
</script>

</body>
</html>