<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro de Asistencia con QR - FICCT SGA</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
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
<div class="min-h-screen p-4 md:p-8">
    <!-- Header -->
    <div class="max-w-6xl mx-auto mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Registro de Asistencia con QR</h1>
                <p class="text-gray-500 mt-1">Escanea el código QR para registrar tu asistencia</p>
            </div>
            <a href="{{ route('docente.dashboard') }}" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                ← Volver a Dashboard
            </a>
        </div>
    </div>

    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Escanear QR -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Escanear Código QR</h2>
            
            <div id="qr-reader" class="mb-4 rounded-lg overflow-hidden border-2 border-gray-300"></div>
            
            <button id="startScanBtn" onclick="startScanner()" class="w-full px-6 py-3 bg-brand-primary hover:bg-brand-hover text-white rounded-lg font-medium transition-colors mb-4">
                📷 Iniciar Escáner
            </button>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Instrucciones:</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Permite el acceso a la cámara cuando se solicite</li>
                    <li>• Coloca el código QR frente a la cámara</li>
                    <li>• Mantén el código dentro del marco</li>
                    <li>• El registro se hará automáticamente</li>
                </ul>
            </div>
        </div>

        <!-- Escaneos Recientes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Escaneos Recientes</h2>
            
            <div id="recentScans" class="space-y-3">
                <p class="text-gray-500 text-center py-8">No hay escaneos recientes</p>
            </div>
        </div>

        <!-- Generar QR (Solo para docentes) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 lg:col-span-2">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Generar Código QR</h2>
            <p class="text-gray-600 mb-4">Genera un código QR para una clase específica</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Horario</label>
                    <select id="scheduleSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-primary focus:border-transparent">
                        <option value="">Seleccionar horario...</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button onclick="generateQR()" class="w-full px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                        📱 Generar Código QR
                    </button>
                </div>
            </div>
            
            <div id="qrDisplay" class="hidden">
                <div class="bg-gray-50 rounded-lg p-6 text-center">
                    <img id="qrImage" src="" alt="QR Code" class="mx-auto mb-4" style="max-width: 300px;">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-yellow-800">
                            ⏱️ Este código QR expira en <span id="countdown" class="font-bold">5:00</span> minutos
                        </p>
                    </div>
                    <button onclick="downloadQR()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        💾 Descargar QR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const API_BASE = '/api';
let html5QrCode = null;
let countdownInterval = null;
let currentScheduleId = null;

// Cargar horarios del docente (desde datos del servidor)
function loadSchedules() {
    const schedules = @json($schedules ?? []);
    console.log('Horarios cargados desde el servidor:', schedules);
    
    const select = document.getElementById('scheduleSelect');
    select.innerHTML = '<option value="">Seleccionar horario...</option>';
    
    if (schedules.length === 0) {
        select.innerHTML = '<option value="">No hay horarios asignados</option>';
        return;
    }
    
    // Mapeo de días en español
    const dayMapping = {
        'monday': 'Lunes',
        'Lunes': 'Lunes',
        'tuesday': 'Martes',
        'Martes': 'Martes',
        'wednesday': 'Miércoles',
        'Miércoles': 'Miércoles',
        'thursday': 'Jueves',
        'Jueves': 'Jueves',
        'friday': 'Viernes',
        'Viernes': 'Viernes',
        'saturday': 'Sábado',
        'Sábado': 'Sábado',
        'sunday': 'Domingo',
        'Domingo': 'Domingo'
    };
    
    schedules.forEach(schedule => {
        const option = document.createElement('option');
        option.value = schedule.id;
        
        const subjectName = schedule.subject_name || 'Sin materia';
        const groupName = schedule.group_name || 'Sin grupo';
        const dayOfWeek = dayMapping[schedule.day_of_week] || schedule.day_of_week || 'Sin día';
        const startTime = schedule.start_time || '00:00';
        const endTime = schedule.end_time || '00:00';
        const roomName = schedule.room_name || 'Sin aula';
        
        // Formato: "Materia - Grupo (Día HH:MM-HH:MM)"
        option.textContent = `${subjectName} - ${groupName} (${dayOfWeek} ${startTime}-${endTime})`;
        option.dataset.subject = subjectName;
        option.dataset.group = groupName;
        option.dataset.room = roomName;
        option.dataset.day = dayOfWeek;
        option.dataset.time = `${startTime}-${endTime}`;
        
        select.appendChild(option);
    });
    
    console.log(`${schedules.length} horarios cargados correctamente`);
}

// Iniciar escáner
function startScanner() {
    const btn = document.getElementById('startScanBtn');
    btn.disabled = true;
    btn.textContent = 'Iniciando cámara...';
    
    html5QrCode = new Html5Qrcode("qr-reader");
    
    html5QrCode.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess,
        onScanError
    ).then(() => {
        btn.textContent = '🛑 Detener Escáner';
        btn.onclick = stopScanner;
        btn.disabled = false;
    }).catch(err => {
        console.error('Error al iniciar cámara:', err);
        showNotification('❌ Error al acceder a la cámara', 'error');
        btn.disabled = false;
        btn.textContent = '📷 Iniciar Escáner';
    });
}

// Detener escáner
function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            const btn = document.getElementById('startScanBtn');
            btn.textContent = '📷 Iniciar Escáner';
            btn.onclick = startScanner;
        });
    }
}

// Cuando se escanea un QR
async function onScanSuccess(decodedText) {
    console.log('QR escaneado:', decodedText);
    
    // Detener el escáner temporalmente
    if (html5QrCode) {
        await html5QrCode.pause();
    }
    
    try {
        // Enviar el token al backend para registrar asistencia
        const response = await fetch(`${API_BASE}/attendance/scan`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ token: decodedText })
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showNotification('✅ Asistencia registrada exitosamente');
            addRecentScan(result);
        } else {
            showNotification('❌ ' + (result.message || 'Error al registrar asistencia'), 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('❌ Error al procesar el código QR', 'error');
    }
    
    // Reanudar el escáner después de 2 segundos
    setTimeout(() => {
        if (html5QrCode) {
            html5QrCode.resume();
        }
    }, 2000);
}

function onScanError(errorMessage) {
    // Ignorar errores de escaneo (son muy frecuentes)
}

// Generar QR
async function generateQR() {
    const scheduleId = document.getElementById('scheduleSelect').value;
    
    if (!scheduleId) {
        showNotification('❌ Por favor selecciona un horario', 'error');
        return;
    }
    
    try {
        console.log('Generando QR para schedule ID:', scheduleId);
        
        const response = await fetch(`${API_BASE}/schedules/${scheduleId}/qrcode`, {
            headers: { 
                'Accept': 'image/png',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });
        
        console.log('Response status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Error response:', errorText);
            
            try {
                const errorJson = JSON.parse(errorText);
                throw new Error(errorJson.message || 'Error al generar QR');
            } catch (e) {
                throw new Error(`Error ${response.status}: ${errorText}`);
            }
        }
        
        const blob = await response.blob();
        console.log('Blob recibido:', blob.type, blob.size);
        
        const url = URL.createObjectURL(blob);
        
        document.getElementById('qrImage').src = url;
        document.getElementById('qrDisplay').classList.remove('hidden');
        currentScheduleId = scheduleId;
        
        // Iniciar cuenta regresiva de 5 minutos
        startCountdown(300); // 300 segundos = 5 minutos
        
        showNotification('✅ Código QR generado exitosamente');
    } catch (error) {
        console.error('Error completo:', error);
        showNotification('❌ ' + error.message, 'error');
    }
}

// Cuenta regresiva
function startCountdown(seconds) {
    if (countdownInterval) clearInterval(countdownInterval);
    
    let remaining = seconds;
    const countdownEl = document.getElementById('countdown');
    
    countdownInterval = setInterval(() => {
        remaining--;
        const mins = Math.floor(remaining / 60);
        const secs = remaining % 60;
        countdownEl.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
        
        if (remaining <= 0) {
            clearInterval(countdownInterval);
            document.getElementById('qrDisplay').classList.add('hidden');
            showNotification('⏱️ El código QR ha expirado', 'error');
        }
    }, 1000);
}

// Descargar QR
function downloadQR() {
    const img = document.getElementById('qrImage');
    const link = document.createElement('a');
    link.href = img.src;
    link.download = `qr-asistencia-${currentScheduleId}.png`;
    link.click();
}

// Agregar escaneo reciente
function addRecentScan(data) {
    const container = document.getElementById('recentScans');
    
    // Limpiar mensaje de "no hay escaneos"
    if (container.querySelector('p')) {
        container.innerHTML = '';
    }
    
    const scanDiv = document.createElement('div');
    scanDiv.className = 'bg-green-50 border border-green-200 rounded-lg p-4';
    scanDiv.innerHTML = `
        <div class="flex items-center justify-between">
            <div>
                <p class="font-semibold text-green-900">${data.schedule?.group?.name || 'Clase'}</p>
                <p class="text-sm text-green-700">${new Date().toLocaleTimeString()}</p>
            </div>
            <span class="text-2xl">✅</span>
        </div>
    `;
    
    container.prepend(scanDiv);
    
    // Mantener solo los últimos 5 escaneos
    while (container.children.length > 5) {
        container.removeChild(container.lastChild);
    }
}

// Notificaciones
function showNotification(message, type = 'success') {
    const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg border ${alertClass} z-50 shadow-lg`;
    notification.innerHTML = `<span class="font-medium">${message}</span>`;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Cargar horarios al iniciar
loadSchedules();
</script>

</body>
</html>
