<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ingreso de Calificaciones - FICCT SGA</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Instrument Sans','sans-serif']},colors:{brand:{primary:'#881F34',hover:'#6d1829'}}}}}</script>
</head>
<body class="bg-gray-50">
    <!-- Barra superior roja -->
    <nav class="bg-brand-primary text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/></svg>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg">FICCT SGA</h1>
                        <p class="text-xs text-white/80">INGRESO DE CALIFICACIONES</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('docente.dashboard') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition-colors">
                        ← Volver al Dashboard
                    </a>
                    <span class="text-sm hidden sm:block">
                        {{ Auth::user()->name ?? 'Docente' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 bg-white/10 rounded-full hover:bg-white/20 transition-colors" title="Cerrar Sesión">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Ingreso de Calificaciones</h1>
            <p class="text-gray-600 mt-1">Registra las calificaciones de tus estudiantes por grupo</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Grupo</label>
                    <select id="groupId" onchange="loadGroupGrades()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary">
                        <option value="">Seleccionar grupo...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Materia</label>
                    <input type="text" id="subjectName" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" placeholder="Selecciona un grupo">
                </div>
                <div class="flex items-end">
                    <button onclick="saveAllGrades()" id="saveAllBtn" disabled class="w-full px-6 py-2 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        💾 Guardar Todas las Calificaciones
                    </button>
                </div>
            </div>
        </div>

        <!-- Información del Grupo -->
        <div id="groupInfo" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start gap-6">
                <div class="w-20 h-20 bg-brand-primary rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
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
                            <p class="text-sm text-gray-600">Promedio General</p>
                            <p class="text-2xl font-bold text-blue-600" id="avgGrade">0</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Aprobados</p>
                            <p class="text-2xl font-bold text-green-600" id="passedCount">0</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Reprobados</p>
                            <p class="text-2xl font-bold text-red-600" id="failedCount">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Calificaciones -->
        <div id="gradesSection" class="hidden bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Calificaciones por Estudiante</h3>
                <div class="flex gap-2">
                    <button onclick="exportToExcel()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                        📊 Exportar Excel
                    </button>
                    <button onclick="exportToPDF()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                        📄 Exportar PDF
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" id="gradesTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky left-0 bg-gray-50 z-10">Estudiante</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky left-0 bg-gray-50 z-10" style="left: 200px;">Código</th>
                            <!-- Columnas dinámicas de criterios -->
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase bg-blue-50">Nota Final</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase bg-blue-50">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="gradesTableBody" class="divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mensaje de ayuda -->
        <div id="helpMessage" class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-900">Instrucciones</h4>
                    <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                        <li>Selecciona un grupo para ver la lista de estudiantes</li>
                        <li>Ingresa las calificaciones en cada criterio de evaluación (0-100)</li>
                        <li>La nota final se calcula automáticamente según el peso de cada criterio</li>
                        <li>Haz clic en "Guardar Todas las Calificaciones" para guardar los cambios</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

<script>
let groups = [];
let currentGroupData = null;
let criteria = [];
let students = [];
let modifiedGrades = new Set();

document.addEventListener('DOMContentLoaded', function() {
    loadGroups();
});

async function loadGroups() {
    try {
        const response = await fetch('/api/groups', {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar grupos');
        
        const data = await response.json();
        groups = data.data || data || [];
        
        const select = document.getElementById('groupId');
        select.innerHTML = '<option value="">Seleccionar grupo...</option>' +
            groups.map(g => `<option value="${g.id}">${g.name}</option>`).join('');
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los grupos');
    }
}

async function loadGroupGrades() {
    const groupId = document.getElementById('groupId').value;
    
    if (!groupId) {
        hideAllSections();
        return;
    }
    
    try {
        const response = await fetch(`/api/grades/group/${groupId}`, {
            method: 'GET',
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) throw new Error('Error al cargar calificaciones');
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Error al cargar datos');
        }
        
        currentGroupData = data;
        criteria = data.criteria || [];
        students = data.students || [];
        
        showGroupInfo(data.group);
        renderGradesTable();
        calculateStats();
        
        document.getElementById('saveAllBtn').disabled = false;
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar las calificaciones: ' + error.message);
    }
}

function showGroupInfo(group) {
    document.getElementById('groupInfo').classList.remove('hidden');
    document.getElementById('gradesSection').classList.remove('hidden');
    
    document.getElementById('groupName').textContent = group.name;
    document.getElementById('subjectName').value = group.subject ? group.subject.name : 'N/A';
    document.getElementById('groupDetails').textContent = `Materia: ${group.subject ? group.subject.name : 'N/A'}`;
}

function renderGradesTable() {
    const thead = document.querySelector('#gradesTable thead tr');
    const tbody = document.getElementById('gradesTableBody');
    
    // Limpiar columnas dinámicas anteriores
    const existingCriteriaCols = thead.querySelectorAll('.criteria-col');
    existingCriteriaCols.forEach(col => col.remove());
    
    // Agregar columnas de criterios antes de "Nota Final"
    const finalGradeCol = thead.querySelector('th:nth-last-child(2)');
    
    criteria.forEach(criterion => {
        const th = document.createElement('th');
        th.className = 'criteria-col px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase bg-purple-50';
        th.innerHTML = `
            <div class="flex flex-col items-center">
                <span>${criterion.name}</span>
                <span class="text-xs font-normal text-gray-400">(${criterion.weight}%)</span>
            </div>
        `;
        thead.insertBefore(th, finalGradeCol);
    });
    
    // Renderizar filas de estudiantes
    tbody.innerHTML = students.map(student => {
        const finalGrade = student.final_grade || 0;
        const status = finalGrade >= 51 ? 'Aprobado' : 'Reprobado';
        const statusColor = finalGrade >= 51 ? 'green' : 'red';
        
        const criteriaInputs = criteria.map(criterion => {
            const gradeData = student.grades.find(g => g.criteria_id === criterion.id);
            const score = gradeData ? gradeData.score : '';
            
            return `
                <td class="px-4 py-3 text-center">
                    <input 
                        type="number" 
                        min="0" 
                        max="100" 
                        step="0.01"
                        value="${score}"
                        data-student-id="${student.id}"
                        data-criteria-id="${criterion.id}"
                        onchange="markAsModified(this)"
                        class="w-20 px-2 py-1 border border-gray-300 rounded text-center focus:ring-2 focus:ring-brand-primary"
                        placeholder="0-100"
                    />
                </td>
            `;
        }).join('');
        
        return `
            <tr class="hover:bg-gray-50" data-student-id="${student.id}">
                <td class="px-4 py-4 sticky left-0 bg-white z-10">
                    <div class="font-medium text-gray-900">${student.name}</div>
                </td>
                <td class="px-4 py-4 sticky bg-white z-10" style="left: 200px;">
                    <div class="text-sm text-gray-500">${student.registration_number || 'N/A'}</div>
                </td>
                ${criteriaInputs}
                <td class="px-4 py-4 text-center bg-blue-50">
                    <span class="text-lg font-bold text-gray-900" data-final-grade>${finalGrade.toFixed(2)}</span>
                </td>
                <td class="px-4 py-4 text-center bg-blue-50">
                    <span class="px-3 py-1 bg-${statusColor}-100 text-${statusColor}-700 text-xs font-medium rounded" data-status>
                        ${status}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

function markAsModified(input) {
    const studentId = input.dataset.studentId;
    const criteriaId = input.dataset.criteriaId;
    const key = `${studentId}-${criteriaId}`;
    
    modifiedGrades.add(key);
    input.classList.add('border-yellow-400', 'bg-yellow-50');
    
    // Recalcular nota final del estudiante
    recalculateStudentGrade(studentId);
}

function recalculateStudentGrade(studentId) {
    const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
    const inputs = row.querySelectorAll('input[type="number"]');
    
    let totalWeight = 0;
    let weightedSum = 0;
    
    inputs.forEach(input => {
        const criteriaId = input.dataset.criteriaId;
        const criterion = criteria.find(c => c.id == criteriaId);
        const score = parseFloat(input.value) || 0;
        
        if (score > 0 && criterion) {
            weightedSum += (score * criterion.weight) / 100;
            totalWeight += parseFloat(criterion.weight);
        }
    });
    
    const finalGrade = totalWeight > 0 ? weightedSum : 0;
    const finalGradeSpan = row.querySelector('[data-final-grade]');
    const statusSpan = row.querySelector('[data-status]');
    
    finalGradeSpan.textContent = finalGrade.toFixed(2);
    
    const status = finalGrade >= 51 ? 'Aprobado' : 'Reprobado';
    const statusColor = finalGrade >= 51 ? 'green' : 'red';
    
    statusSpan.className = `px-3 py-1 bg-${statusColor}-100 text-${statusColor}-700 text-xs font-medium rounded`;
    statusSpan.textContent = status;
    
    calculateStats();
}

function calculateStats() {
    const rows = document.querySelectorAll('#gradesTableBody tr');
    let totalGrade = 0;
    let passedCount = 0;
    let failedCount = 0;
    
    rows.forEach(row => {
        const finalGrade = parseFloat(row.querySelector('[data-final-grade]').textContent);
        totalGrade += finalGrade;
        
        if (finalGrade >= 51) {
            passedCount++;
        } else {
            failedCount++;
        }
    });
    
    const avgGrade = rows.length > 0 ? (totalGrade / rows.length).toFixed(2) : 0;
    
    document.getElementById('totalStudents').textContent = rows.length;
    document.getElementById('avgGrade').textContent = avgGrade;
    document.getElementById('passedCount').textContent = passedCount;
    document.getElementById('failedCount').textContent = failedCount;
}

async function saveAllGrades() {
    if (modifiedGrades.size === 0) {
        alert('No hay cambios para guardar');
        return;
    }
    
    const gradesToSave = [];
    
    modifiedGrades.forEach(key => {
        const [studentId, criteriaId] = key.split('-');
        const input = document.querySelector(`input[data-student-id="${studentId}"][data-criteria-id="${criteriaId}"]`);
        const score = parseFloat(input.value);
        
        if (!isNaN(score) && score >= 0 && score <= 100) {
            gradesToSave.push({
                student_id: parseInt(studentId),
                evaluation_criteria_id: parseInt(criteriaId),
                score: score,
                comments: null
            });
        }
    });
    
    if (gradesToSave.length === 0) {
        alert('No hay calificaciones válidas para guardar');
        return;
    }
    
    try {
        const response = await fetch('/api/grades/bulk', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ grades: gradesToSave })
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Error al guardar');
        }
        
        alert(data.message);
        
        // Limpiar marcas de modificación
        modifiedGrades.clear();
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.classList.remove('border-yellow-400', 'bg-yellow-50');
        });
        
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar las calificaciones: ' + error.message);
    }
}

function hideAllSections() {
    document.getElementById('groupInfo').classList.add('hidden');
    document.getElementById('gradesSection').classList.add('hidden');
    document.getElementById('subjectName').value = '';
    document.getElementById('saveAllBtn').disabled = true;
}

function exportToExcel() {
    const groupId = document.getElementById('groupId').value;
    if (!groupId) {
        alert('Por favor selecciona un grupo primero');
        return;
    }
    window.open(`/api/grades/group/${groupId}/export-excel`, '_blank');
}

function exportToPDF() {
    const groupId = document.getElementById('groupId').value;
    if (!groupId) {
        alert('Por favor selecciona un grupo primero');
        return;
    }
    window.open(`/api/grades/group/${groupId}/export-pdf`, '_blank');
}
</script>
</body>
</html>
