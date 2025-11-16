<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Incidencias - FICCT SGA</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Instrument Sans','sans-serif']},colors:{brand:{primary:'#881F34',hover:'#6d1829'}}}}}</script>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">
    @include('layouts.admin-sidebar')
    <main class="flex-1 ml-64 p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Incidencias en Aulas</h1>
                <p class="text-gray-500 mt-1">Reporta problemas de equipamiento o infraestructura</p>
            </div>
            <button onclick="openModal()" class="px-6 py-3 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium transition-colors shadow-sm">+ Reportar Incidencia</button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg"><svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Pendientes</p><p id="pending" class="text-2xl font-bold text-gray-900">0</p></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg"><svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">En Proceso</p><p id="inProgress" class="text-2xl font-bold text-gray-900">0</p></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg"><svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Resueltas</p><p id="resolved" class="text-2xl font-bold text-gray-900">0</p></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg"><svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Total</p><p id="total" class="text-2xl font-bold text-gray-900">0</p></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Reportes de Incidencias</h2>
                <select id="statusFilter" onchange="applyFilters()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Todos</option>
                    <option value="pending">Pendientes</option>
                    <option value="in_progress">En Proceso</option>
                    <option value="resolved">Resueltas</option>
                </select>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aula</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Problema</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reportado por</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="table" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Reportar Incidencia</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        </div>
        <form id="form" class="p-6 space-y-4">
            <input type="hidden" id="id">
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Aula *</label><select id="room" required class="w-full px-4 py-2 border border-gray-300 rounded-lg"><option value="">Seleccionar</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label><select id="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg"><option value="equipment">Equipamiento</option><option value="infrastructure">Infraestructura</option><option value="cleaning">Limpieza</option><option value="other">Otro</option></select></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label><textarea id="description" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Prioridad</label><select id="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg"><option value="low">Baja</option><option value="medium" selected>Media</option><option value="high">Alta</option><option value="urgent">Urgente</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Estado</label><select id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg"><option value="pending">Pendiente</option><option value="in_progress">En Proceso</option><option value="resolved">Resuelta</option></select></div>
            </div>
        </form>
        <div class="p-6 pt-0">
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">Cancelar</button>
                <button type="button" onclick="document.getElementById('form').requestSubmit()" class="px-6 py-2 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium">Reportar</button>
            </div>
        </div>
    </div>
</div>

<script>
const API='/api';let data=[],filtered=[],rooms=[];
function notify(m,t='success'){const n=document.createElement('div');n.className=`fixed top-4 right-4 p-4 rounded-lg border ${t==='success'?'bg-green-50 border-green-200 text-green-800':'bg-red-50 border-red-200 text-red-800'} z-50 shadow-lg`;n.innerHTML=`<span class="font-medium">${m}</span>`;document.body.appendChild(n);setTimeout(()=>n.remove(),3000)}
async function load(){try{const r=await fetch(`${API}/incidents`,{headers:{'Accept':'application/json'}});if(r.ok)data=await r.json();else data=[{id:1,room_name:'Aula 201',type:'equipment',description:'Proyector no funciona',reporter:'Dr. Juan Pérez',priority:'high',status:'pending',created_at:'2025-11-14'},{id:2,room_name:'Aula 102',type:'infrastructure',description:'Aire acondicionado dañado',reporter:'Ing. María García',priority:'urgent',status:'in_progress',created_at:'2025-11-13'}];filtered=[...data];render();updateStats()}catch(e){console.error(e);data=[];render()}}
async function loadRooms(){try{const r=await fetch(`${API}/rooms`,{headers:{'Accept':'application/json'}});if(r.ok)rooms=await r.json();else rooms=[{id:1,name:'Aula 101'},{id:2,name:'Aula 102'},{id:3,name:'Aula 201'}];const s=document.getElementById('room');s.innerHTML='<option value="">Seleccionar</option>'+rooms.map(r=>`<option value="${r.id}">${r.name}</option>`).join('')}catch(e){console.error(e)}}
function render(){const t=document.getElementById('table');if(!filtered.length){t.innerHTML='<tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">No hay incidencias</td></tr>';return}t.innerHTML=filtered.map(i=>{const p={low:{c:'gray',l:'Baja'},medium:{c:'blue',l:'Media'},high:{c:'yellow',l:'Alta'},urgent:{c:'red',l:'Urgente'}}[i.priority||'medium'];const s={pending:{c:'yellow',l:'Pendiente'},in_progress:{c:'blue',l:'En Proceso'},resolved:{c:'green',l:'Resuelta'}}[i.status||'pending'];return`<tr class="hover:bg-gray-50"><td class="px-6 py-4 whitespace-nowrap text-sm">${new Date(i.created_at).toLocaleDateString('es-ES')}</td><td class="px-6 py-4 text-sm font-medium">${i.room_name||'N/A'}</td><td class="px-6 py-4"><div class="text-sm font-medium text-gray-900">${i.type==='equipment'?'Equipamiento':i.type==='infrastructure'?'Infraestructura':i.type==='cleaning'?'Limpieza':'Otro'}</div><div class="text-sm text-gray-500">${i.description}</div></td><td class="px-6 py-4 text-sm">${i.reporter_name||'N/A'}</td><td class="px-6 py-4 text-center"><span class="px-3 py-1 bg-${p.c}-100 text-${p.c}-800 rounded-full text-sm font-medium">${p.l}</span></td><td class="px-6 py-4 text-center"><span class="px-3 py-1 bg-${s.c}-100 text-${s.c}-800 rounded-full text-sm font-medium">${s.l}</span></td><td class="px-6 py-4 text-right"><button onclick="edit(${i.id})" class="text-blue-600 hover:text-blue-900 mr-3"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button><button onclick="del(${i.id})" class="text-red-600 hover:text-red-900"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td></tr>`}).join('')}
function updateStats(){document.getElementById('pending').textContent=data.filter(i=>i.status==='pending').length;document.getElementById('inProgress').textContent=data.filter(i=>i.status==='in_progress').length;document.getElementById('resolved').textContent=data.filter(i=>i.status==='resolved').length;document.getElementById('total').textContent=data.length}
function openModal(){document.getElementById('form').reset();document.getElementById('id').value='';document.getElementById('status').value='pending';document.getElementById('modal').classList.remove('hidden');document.getElementById('modal').classList.add('flex')}
function closeModal(){document.getElementById('modal').classList.add('hidden');document.getElementById('modal').classList.remove('flex')}
function edit(id){const i=data.find(x=>x.id===id);if(!i)return;document.getElementById('id').value=i.id;document.getElementById('room').value=i.room_id;document.getElementById('type').value=i.type;document.getElementById('description').value=i.description;document.getElementById('priority').value=i.priority;document.getElementById('status').value=i.status;openModal()}
async function del(id){if(!confirm('¿Eliminar?'))return;try{const r=await fetch(`${API}/incidents/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}});if(!r.ok)throw new Error('Error');notify('✅ Eliminado');load()}catch(e){notify('❌ '+e.message,'error')}}
function applyFilters(){const s=document.getElementById('statusFilter').value;filtered=data.filter(i=>!s||i.status===s);render()}
document.getElementById('form').addEventListener('submit',async(e)=>{e.preventDefault();const id=document.getElementById('id').value;const d={room_id:parseInt(document.getElementById('room').value),type:document.getElementById('type').value,description:document.getElementById('description').value,priority:document.getElementById('priority').value,status:document.getElementById('status').value};try{const u=id?`${API}/incidents/${id}`:`${API}/incidents`;const m=id?'PATCH':'POST';const r=await fetch(u,{method:m,headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:JSON.stringify(d)});if(!r.ok)throw new Error('Error');notify('✅ Guardado');closeModal();load()}catch(e){notify('❌ '+e.message,'error')}});
Promise.all([load(),loadRooms()]);
</script>
</body>
</html>
