# ✅ Guardar Horarios Arreglado

## Problema Identificado

Al intentar guardar un horario, aparecía el error:
```
The group id field is required. (and 1 more error)
```

## Causa Raíz

El formulario estaba enviando `assignment_id` pero el backend (ScheduleController) requiere:
- `group_id` (requerido)
- `teacher_id` (opcional)
- `day_of_week` (requerido)
- `start_time` (requerido)
- `end_time` (requerido)
- `room_id` (opcional)

## Solución Implementada

Se actualizó el código del submit del formulario para:

1. **Extraer datos de la asignación seleccionada**
2. **Mapear el día al formato correcto**
3. **Enviar los campos requeridos por el backend**

### Código Actualizado

```javascript
document.getElementById('scheduleForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const scheduleId = document.getElementById('scheduleId').value;
    const assignmentId = parseInt(document.getElementById('assignmentSelect').value);
    const roomId = parseInt(document.getElementById('roomSelect').value);
    const day = document.getElementById('daySelect').value;
    const startTime = document.getElementById('startTimeSelect').value;
    const endTime = document.getElementById('endTimeSelect').value;
    
    // Buscar la asignación seleccionada para obtener group_id y teacher_id
    const assignment = assignments.find(a => a.id === assignmentId);
    
    if (!assignment) {
        showNotification('❌ Por favor selecciona una asignación válida', 'error');
        return;
    }
    
    // Mapear el día al formato esperado por el backend
    const dayMapping = {
        'monday': 'Lunes',
        'tuesday': 'Martes',
        'wednesday': 'Miércoles',
        'thursday': 'Jueves',
        'friday': 'Viernes',
        'saturday': 'Sábado'
    };
    
    const data = {
        group_id: assignment.group_id,
        teacher_id: assignment.teacher_id,
        room_id: roomId,
        day_of_week: dayMapping[day] || day,
        start_time: startTime,
        end_time: endTime
    };
    
    // ... resto del código de envío
});
```

## Validaciones del Backend

El ScheduleController valida:

### Campos Requeridos
- `group_id`: ID del grupo (requerido)
- `day_of_week`: Día de la semana (requerido)
- `start_time`: Hora de inicio en formato H:i (requerido)
- `end_time`: Hora de fin en formato H:i (requerido)

### Campos Opcionales
- `room_id`: ID del aula
- `teacher_id`: ID del docente

### Validaciones de Conflictos

El backend verifica automáticamente conflictos de:

1. **Grupo**: No puede tener dos clases al mismo tiempo
2. **Aula**: No puede estar ocupada por dos grupos al mismo tiempo
3. **Docente**: No puede dar dos clases al mismo tiempo

Si hay conflicto, retorna error 409 con mensaje descriptivo.

## Estructura de Datos

### Request (POST /api/schedules)

```json
{
  "group_id": 1,
  "teacher_id": 1,
  "room_id": 1,
  "day_of_week": "Lunes",
  "start_time": "08:00",
  "end_time": "10:00"
}
```

### Response (201 Created)

```json
{
  "id": 31,
  "group_id": 1,
  "teacher_id": 1,
  "room_id": 1,
  "day_of_week": "Lunes",
  "start_time": "08:00:00",
  "end_time": "10:00:00",
  "assigned_by": 1,
  "created_at": "2025-11-15T16:30:00.000000Z",
  "updated_at": "2025-11-15T16:30:00.000000Z"
}
```

### Response de Error (409 Conflict)

```json
{
  "message": "Schedule conflict for group"
}
```

O:

```json
{
  "message": "Schedule conflict for room"
}
```

O:

```json
{
  "message": "Schedule conflict for teacher"
}
```

## Mapeo de Días

El formulario usa días en inglés (minúsculas) pero el backend espera días en español (con mayúscula inicial):

| Frontend | Backend |
|----------|---------|
| monday | Lunes |
| tuesday | Martes |
| wednesday | Miércoles |
| thursday | Jueves |
| friday | Viernes |
| saturday | Sábado |

## ✅ Resultado

Ahora el formulario de horarios:
- ✅ Extrae correctamente `group_id` y `teacher_id` de la asignación
- ✅ Mapea el día al formato correcto
- ✅ Envía todos los campos requeridos
- ✅ Valida que se haya seleccionado una asignación
- ✅ Muestra mensajes de error descriptivos
- ✅ Detecta conflictos automáticamente

## 🧪 Pruebas

1. Ir a `/admin/horarios`
2. Hacer clic en "+ Nuevo Horario"
3. Seleccionar una asignación (ej: "Dr. Juan Pérez García - Introducción a la Programación - Grupo A")
4. Seleccionar un aula
5. Seleccionar día y horas
6. Hacer clic en "Guardar Horario"
7. El horario se crea exitosamente y aparece en la grilla

## 🔍 Verificación de Conflictos

El sistema detecta automáticamente:

### Conflicto de Grupo
Si intentas crear dos horarios para el mismo grupo en el mismo día y hora:
```
❌ Schedule conflict for group
```

### Conflicto de Aula
Si intentas usar la misma aula para dos grupos en el mismo día y hora:
```
❌ Schedule conflict for room
```

### Conflicto de Docente
Si intentas asignar al mismo docente dos clases en el mismo día y hora:
```
❌ Schedule conflict for teacher
```

## 📝 Notas

- El backend usa una tolerancia configurable (`SCHEDULE_CONFLICT_TOLERANCE_MINUTES`) para detectar conflictos
- Los horarios se guardan con el ID del usuario que los creó (`assigned_by`)
- Las horas se almacenan en formato H:i:s (con segundos)
- Los días se almacenan en español con mayúscula inicial

## 🔗 Archivos Relacionados

- `resources/views/admin/schedules.blade.php` - Vista de horarios actualizada
- `app/Http/Controllers/ScheduleController.php` - Controlador de horarios
- `app/Models/Schedule.php` - Modelo de horarios
- `database/migrations/2025_11_02_121000_create_schedules_table.php` - Migración de horarios

## ✨ Mejoras Futuras

1. **Validación en Frontend**: Validar conflictos antes de enviar al backend
2. **Sugerencias de Horarios**: Sugerir horarios disponibles automáticamente
3. **Arrastrar y Soltar**: Permitir crear horarios arrastrando en la grilla
4. **Vista de Conflictos**: Mostrar todos los conflictos en una vista dedicada
5. **Notificaciones**: Notificar a docentes cuando se les asigna un horario
