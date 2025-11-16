# ✅ Sistema de Asistencia con QR para Docentes

## Implementación Completada

Se ha creado una vista completa de asistencia con QR específicamente para el panel del docente.

## 🎯 Características

### Para Docentes:

1. **Generar Código QR**
   - Seleccionar un horario de clase
   - Generar código QR válido por 5 minutos
   - Cuenta regresiva visible
   - Descargar el QR como imagen

2. **Escanear Código QR**
   - Usar la cámara del dispositivo
   - Escanear QR de otros docentes/admin
   - Registro automático de asistencia
   - Historial de escaneos recientes

3. **Validación de Tiempo**
   - QR válido por 5 minutos (300 segundos)
   - Cuenta regresiva en tiempo real
   - Expiración automática

## 📍 Acceso

### Ruta para Docentes:
```
/docente/asistencia-qr
```

### Ruta nombrada:
```php
route('docente.attendance-qr')
```

## 🔐 Seguridad del QR

### Backend (ScheduleController.php)

El QR se genera con un token firmado HMAC-SHA256:

```php
public function generateQr(Request $request, $id)
{
    $schedule = Schedule::with('teacher')->findOrFail($id);
    
    // Solo el docente asignado o admin pueden generar el QR
    $user = $request->user();
    if (! $user->hasRole('administrador')) {
        $teacher = $schedule->teacher;
        if (! $teacher || $teacher->email !== $user->email) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
    }
    
    $ttl = (int) env('QR_TTL_SECONDS', 300); // 5 minutos por defecto
    $payload = [
        'schedule_id' => $schedule->id,
        'iat' => time(),
        'exp' => time() + $ttl,
        'iss' => env('APP_NAME', 'carga_horaria'),
    ];
    
    // Generar token firmado
    $payloadJson = json_encode($payload);
    $payloadB64 = rtrim(strtr(base64_encode($payloadJson), '+/', '-_'), '=');
    $secret = env('QR_SECRET', 'CHANGE_ME');
    $sig = hash_hmac('sha256', $payloadB64, $secret);
    $token = $payloadB64 . '.' . $sig;
    
    // Generar imagen QR
    $result = Builder::create()
        ->data($token)
        ->size(300)
        ->margin(10)
        ->build();
    
    return response($result->getString(), 200, ['Content-Type' => $result->getMimeType()]);
}
```

### Configuración en .env

```env
QR_TTL_SECONDS=300  # 5 minutos
QR_SECRET=tu_clave_secreta_aqui  # Cambiar en producción
```

## 🔄 Flujo de Uso

### Escenario 1: Docente genera QR para su clase

1. Docente ingresa a `/docente/asistencia-qr`
2. Selecciona su horario de clase
3. Hace clic en "Generar Código QR"
4. El QR se muestra con cuenta regresiva de 5 minutos
5. Puede descargar el QR o mostrarlo en pantalla
6. Los estudiantes escanean el QR para registrar asistencia

### Escenario 2: Docente escanea QR de otro docente

1. Docente ingresa a `/docente/asistencia-qr`
2. Hace clic en "Iniciar Escáner"
3. Permite acceso a la cámara
4. Escanea el QR mostrado por otro docente/admin
5. Su asistencia se registra automáticamente
6. Ve confirmación en "Escaneos Recientes"

## 📱 Interfaz

### Secciones:

1. **Escanear Código QR**
   - Visor de cámara
   - Botón para iniciar/detener escáner
   - Instrucciones de uso

2. **Escaneos Recientes**
   - Últimos 5 escaneos
   - Hora y clase
   - Indicador visual de éxito

3. **Generar Código QR**
   - Dropdown con horarios del docente
   - Botón para generar
   - Imagen del QR
   - Cuenta regresiva
   - Botón para descargar

## 🔧 Endpoints API Utilizados

### Generar QR:
```
GET /api/schedules/{id}/qrcode
```

**Respuesta**: Imagen PNG del código QR

### Escanear QR (pendiente de implementar):
```
POST /api/attendance/scan
Body: { "token": "eyJ..." }
```

**Respuesta**: 
```json
{
  "message": "Asistencia registrada",
  "schedule": { ... }
}
```

## ⚠️ Pendiente de Implementar

### Endpoint de Escaneo

Necesitas crear el endpoint `/api/attendance/scan` que:

1. Recibe el token del QR
2. Valida la firma HMAC
3. Verifica que no haya expirado
4. Registra la asistencia en la tabla `attendances`
5. Retorna confirmación

### Ejemplo de implementación:

```php
public function scanQR(Request $request)
{
    $token = $request->input('token');
    
    // Separar payload y firma
    [$payloadB64, $sig] = explode('.', $token);
    
    // Verificar firma
    $secret = env('QR_SECRET', 'CHANGE_ME');
    $expectedSig = hash_hmac('sha256', $payloadB64, $secret);
    
    if (!hash_equals($expectedSig, $sig)) {
        return response()->json(['message' => 'Token inválido'], 401);
    }
    
    // Decodificar payload
    $payloadJson = base64_decode(strtr($payloadB64, '-_', '+/'));
    $payload = json_decode($payloadJson, true);
    
    // Verificar expiración
    if (time() > $payload['exp']) {
        return response()->json(['message' => 'QR expirado'], 401);
    }
    
    // Registrar asistencia
    $schedule = Schedule::findOrFail($payload['schedule_id']);
    
    $attendance = Attendance::create([
        'schedule_id' => $schedule->id,
        'teacher_id' => $schedule->teacher_id,
        'date' => now()->format('Y-m-d'),
        'time' => now()->format('H:i:s'),
        'status' => 'present',
        'recorded_by' => auth()->id(),
    ]);
    
    return response()->json([
        'message' => 'Asistencia registrada exitosamente',
        'schedule' => $schedule->load(['group', 'teacher']),
        'attendance' => $attendance
    ]);
}
```

## 📋 Checklist de Implementación

- ✅ Vista de QR para docentes creada
- ✅ Ruta agregada (`/docente/asistencia-qr`)
- ✅ Endpoint de generación de QR existe
- ✅ Validación de tiempo (5 minutos)
- ✅ Cuenta regresiva en frontend
- ✅ Escáner de QR con cámara
- ✅ Interfaz responsive
- ❌ Endpoint de escaneo (`/api/attendance/scan`) - **PENDIENTE**
- ❌ Agregar enlace en el menú del docente - **PENDIENTE**

## 🎨 Mejoras Futuras

1. **Notificaciones push** cuando se registra asistencia
2. **Historial completo** de asistencias del día
3. **Estadísticas** de asistencia en tiempo real
4. **Modo offline** para escanear sin conexión
5. **Múltiples idiomas** para el QR
6. **Sonido de confirmación** al escanear

## 🔗 Enlaces Relacionados

- Vista Admin QR: `/admin/asistencia-qr`
- Dashboard Docente: `/docente/dashboard`
- Historial Asistencias: `/docente/historial-asistencias`

## ✨ Resultado

Los docentes ahora tienen acceso completo al sistema de QR desde su panel, pueden generar códigos para sus clases y escanear códigos de otros docentes. El QR expira automáticamente después de 5 minutos por seguridad.
