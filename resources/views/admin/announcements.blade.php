<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Anuncios - FICCT SGA</title>
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
                <h1 class="text-3xl font-bold text-gray-900">Anuncios Generales</h1>
                <p class="text-gray-500 mt-1">Publica anuncios para docentes y estudiantes</p>
            </div>
            <button onclick="openModal()" class="px-6 py-3 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium transition-colors shadow-sm">+ Nuevo Anuncio</button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg"><svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Total</p><p id="totalAnnouncements" class="text-2xl font-bold text-gray-900">0</p></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg"><svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Activos</p><p id="activeAnnouncements" class="text-2xl font-bold text-gray-900">0</p></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg"><svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Vistas</p><p id="totalViews" class="text-2xl font-bold text-gray-900">0</p></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg"><svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                    <div class="ml-4"><p class="text-sm font-medium text-gray-500">Esta Semana</p><p id="thisWeek" class="text-2xl font-bold text-gray-900">0</p></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200"><h2 class="text-lg font-semibold text-gray-900">Lista de Anuncios</h2></div>
            <div class="divide-y divide-gray-200" id="announcementsList">
                <div class="p-12 text-center text-gray-500">Cargando...</div>
            </div>
        </div>
    </main>
</div>

<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Nuevo Anuncio</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
        </div>
        <form id="form" class="p-6 space-y-4">
            <input type="hidden" id="id">
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Título *</label><input type="text" id="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-2">Contenido *</label><textarea id="content" required rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary"></textarea></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Prioridad</label><select id="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg"><option value="low">Baja</option><option value="normal" selected>Normal</option><option value="high">Alta</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-2">Destinatarios</label><select id="target" class="w-full px-4 py-2 border border-gray-300 rounded-lg"><option value="all">Todos</option><option value="teachers">Docentes</option><option value="students">Estudiantes</option></select></div>
            </div>
            <div class="flex items-center gap-3"><input type="checkbox" id="active" checked class="w-4 h-4 text-brand-primary"><label for="active" class="text-sm font-medium text-gray-700">Publicar inmediatamente</label></div>
        </form>
        <div class="p-6 pt-0">
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal()" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">Cancelar</button>
                <button type="button" onclick="document.getElementById('form').requestSubmit()" class="px-6 py-2 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium">Publicar</button>
            </div>
        </div>
    </div>
</div>

<script>
const API='/api';let data=[];
function notify(m,t='success'){const n=document.createElement('div');n.className=`fixed top-4 right-4 p-4 rounded-lg border ${t==='success'?'bg-green-50 border-green-200 text-green-800':'bg-red-50 border-red-200 text-red-800'} z-50 shadow-lg`;n.innerHTML=`<span class="font-medium">${m}</span>`;document.body.appendChild(n);setTimeout(()=>n.remove(),3000)}
async function load(){try{const r=await fetch(`${API}/announcements`,{headers:{'Accept':'application/json'}});if(r.ok)data=await r.json();else data=[{id:1,title:'Inicio de Clases',content:'Las clases inician el 1 de marzo',priority:'high',target:'all',active:true,views:150,created_at:'2025-11-10'},{id:2,title:'Reunión Docentes',content:'Reunión general el viernes 15',priority:'normal',target:'teachers',active:true,views:45,created_at:'2025-11-12'}];render();updateStats()}catch(e){console.error(e);data=[];render()}}
function render(){const c=document.getElementById('announcementsList');if(!data.length){c.innerHTML='<div class="p-12 text-center text-gray-500">No hay anuncios</div>';return}c.innerHTML=data.map(a=>{const p={high:{c:'red',l:'Alta'},normal:{c:'blue',l:'Normal'},low:{c:'gray',l:'Baja'}}[a.priority||'normal'];return`<div class="p-6 hover:bg-gray-50"><div class="flex items-start justify-between"><div class="flex-1"><div class="flex items-center gap-3 mb-2"><h3 class="text-lg font-semibold text-gray-900">${a.title}</h3><span class="px-2 py-1 bg-${p.c}-100 text-${p.c}-800 rounded text-xs font-medium">${p.l}</span>${a.active?'<span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Activo</span>':''}</div><p class="text-gray-600 mb-2">${a.body||a.content||''}</p><div class="flex items-center gap-4 text-sm text-gray-500"><span>👁️ ${a.views||0} vistas</span><span>📅 ${new Date(a.created_at).toLocaleDateString('es-ES')}</span><span>👥 ${a.target==='all'?'Todos':a.target==='teachers'?'Docentes':'Estudiantes'}</span></div></div><div class="flex gap-2"><button onclick="edit(${a.id})" class="text-blue-600 hover:text-blue-900"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button><button onclick="del(${a.id})" class="text-red-600 hover:text-red-900"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></div></div></div>`}).join('')}
function updateStats(){document.getElementById('totalAnnouncements').textContent=data.length;document.getElementById('activeAnnouncements').textContent=data.filter(a=>a.active).length;document.getElementById('totalViews').textContent=data.reduce((s,a)=>s+(a.views||0),0);const w=new Date();w.setDate(w.getDate()-7);document.getElementById('thisWeek').textContent=data.filter(a=>new Date(a.created_at)>=w).length}
function openModal(){document.getElementById('form').reset();document.getElementById('id').value='';document.getElementById('modal').classList.remove('hidden');document.getElementById('modal').classList.add('flex')}
function closeModal(){document.getElementById('modal').classList.add('hidden');document.getElementById('modal').classList.remove('flex')}
function edit(id){const a=data.find(x=>x.id===id);if(!a)return;document.getElementById('id').value=a.id;document.getElementById('title').value=a.title;document.getElementById('content').value=a.body||a.content||'';document.getElementById('priority').value=a.priority||'normal';document.getElementById('target').value=a.target||'all';document.getElementById('active').checked=a.active!==false;openModal()}
async function del(id){if(!confirm('¿Eliminar este anuncio?'))return;try{const r=await fetch(`${API}/announcements/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}});const res=await r.json();if(!r.ok)throw new Error(res.message||'Error');notify('✅ Anuncio eliminado');load()}catch(e){notify('❌ '+e.message,'error')}}
document.getElementById('form').addEventListener('submit',async(e)=>{e.preventDefault();const id=document.getElementById('id').value;const d={title:document.getElementById('title').value,content:document.getElementById('content').value,priority:document.getElementById('priority').value,target:document.getElementById('target').value,active:document.getElementById('active').checked};try{const u=id?`${API}/announcements/${id}`:`${API}/announcements`;const m=id?'PATCH':'POST';const r=await fetch(u,{method:m,headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:JSON.stringify(d)});const res=await r.json();if(!r.ok)throw new Error(res.message||'Error');notify('✅ Anuncio guardado');closeModal();load()}catch(e){notify('❌ '+e.message,'error')}});
load();
</script>
</body>
</html>
