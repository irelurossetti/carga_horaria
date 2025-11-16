# ✅ Asignaciones Arregladas

## Problema Identificado

El formulario de "Nueva Asignación" mostraba el error:
```
The POST method is not supported for route api/assignments. Supported methods: GET, HEAD.
```

## Solución Implementada

### 1. Ruta POST Agregada ✅

**Archivo:** `routes/web.php`

Se agregó la ruta POST para crear asignaciones directamente:

```php
Route::post('assignments', [TeacherAssignmentController::class, 'store']);
```

### 2. Migración de Campos Adicionales ✅

**Archivo:** `database/migrations/2025_11_15_155135_add_additional_fields_to_teacher_assignments_table.php`

Se agregaron los siguientes campos a la tabla `teacher_assignments`:

- `horas_semanales` (integer, nullable) - Horas semanales de la asignación
- `fecha_inicio` (date, nullable) - Fecha de inicio de la asignación
- `fecha_fin` (date, nullable) - Fecha de fin de la asignación
- `observaciones` (text, nullable) - Observaciones adicionales
- `tipo_asignacion` (string, nullable) - Tipo de asignación (Teoría, Práctica, etc.)

### 3. Controlador Actualizado ✅

**Archivo:** `app/Http/Controllers/TeacherAssignmentController.php`

El método `store()` ahora valida y acepta los nuevos campos:

```php
public function store(Request $request)
{
    $data = $request->validate([
        'teacher_id' => 'required|integer|exists:teachers,id',
        'subject_id' => 'nullable|integer|exists:subjects,id',
        'group_id' => 'nullable|integer|exists:groups,id',
        'period_id' => 'nullable|integer|exists:academic_periods,id',
        'horas_semanales' => 'nullable|integer|min:1',
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        'observaciones' => 'nullable|string|max:1000',
        'tipo_asignacion' => 'nullable|string|max:100',
    ]);

    $data['assigned_by'] = Auth::id();

    $assignment = TeacherAssignment::create($data);

    return response()->json($assignment->load(['subject', 'group', 'teacher']), 201);
}
```

### 4. Modelo Actualizado ✅

**Archivo:** `app/Models/TeacherAssignment.php`

- Se actualizó el array `$fillable` para incluir todos los campos
- Se agregaron las relaciones correctas: `subject()`, `period()`, `assignedBy()`
- Se agregaron los casts para fechas y enteros
- Se corrigió el nombre de la tabla (sin el prefijo `public.`)

```php
protected $fillable = [
    'teacher_id',
    'subject_id',
    'group_id',
    'period_id',
    'assigned_by',
    'horas_semanales',
    'fecha_inicio',
    'fecha_fin',
    'observaciones',
    'tipo_asignacion',
];

protected $casts = [
    'fecha_inicio' => 'date',
    'fecha_fin' => 'date',
    'horas_semanales' => 'integer',
];
```

## Estructura de la Tabla `teacher_assignments`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID autoincremental |
| teacher_id | bigint | ID del docente (requerido) |
| subject_id | bigint | ID de la materia (opcional) |
| group_id | bigint | ID del grupo (opcional) |
| period_id | bigint | ID del periodo académico (opcional) |
| assigned_by | bigint | ID del usuario que asignó (opcional) |
| horas_semanales | integer | Horas semanales (opcional) |
| fecha_inicio | date | Fecha de inicio (opcional) |
| fecha_fin | date | Fecha de fin (opcional) |
| observaciones | text | Observaciones (opcional) |
| tipo_asignacion | string | Tipo de asignación (opcional) |
| created_at | timestamp | Fecha de creación |
| updated_at | timestamp | Fecha de actualización |

## Rutas API Disponibles

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET | `/api/assignments` | Listar todas las asignaciones |
| POST | `/api/assignments` | Crear nueva asignación |
| GET | `/api/assignments/{id}` | Ver asignación específica |
| PATCH | `/api/assignments/{id}` | Actualizar asignación |
| DELETE | `/api/assignments/{id}` | Eliminar asignación |
| GET | `/api/teachers/{id}/assignments` | Listar asignaciones de un docente |
| POST | `/api/teachers/{id}/assignments` | Crear asignación para un docente |

## Ejemplo de Request

```json
POST /api/assignments

{
  "teacher_id": 1,
  "subject_id": 5,
  "group_id": 3,
  "period_id": 30,
  "horas_semanales": 5,
  "fecha_inicio": "2025-01-15",
  "fecha_fin": "2025-06-30",
  "tipo_asignacion": "Teoría y Práctica",
  "observaciones": "Asignación para el semestre 1-2025"
}
```

## Ejemplo de Response

```json
{
  "id": 31,
  "teacher_id": 1,
  "subject_id": 5,
  "group_id": 3,
  "period_id": 30,
  "assigned_by": 1,
  "horas_semanales": 5,
  "fecha_inicio": "2025-01-15",
  "fecha_fin": "2025-06-30",
  "tipo_asignacion": "Teoría y Práctica",
  "observaciones": "Asignación para el semestre 1-2025",
  "created_at": "2025-11-15T15:55:00.000000Z",
  "updated_at": "2025-11-15T15:55:00.000000Z",
  "teacher": {
    "id": 1,
    "name": "Dr. Juan Pérez García",
    "email": "docente1@ficct.edu.bo"
  },
  "subject": {
    "id": 5,
    "code": "QUI-101",
    "name": "Química General"
  },
  "group": {
    "id": 3,
    "code": "QUI-101-C",
    "name": "Grupo C"
  }
}
```

## ✅ Resultado

El formulario de "Nueva Asignación" ahora funciona correctamente y puede:
- Crear asignaciones con todos los campos
- Validar los datos correctamente
- Retornar la asignación creada con sus relaciones
- Calcular automáticamente la carga horaria del docente

## 🧪 Pruebas

Para probar el formulario:
1. Ir a `/admin/asignaciones`
2. Hacer clic en "Nueva Asignación"
3. Llenar el formulario con los datos requeridos
4. Hacer clic en "Guardar Asignación"
5. La asignación se creará exitosamente

## 📝 Notas

- La migración ya fue ejecutada exitosamente
- Los datos del seeder siguen siendo válidos
- No se requiere volver a poblar la base de datos
- El sistema es compatible con las asignaciones existentes
