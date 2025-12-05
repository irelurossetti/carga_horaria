# 📦 Sistema de Recursos Físicos (Inventario) - COMPLETADO

## ✅ Estado: IMPLEMENTADO Y FUNCIONAL

---

## 🎯 Funcionalidades Implementadas

### 1. Modelo Resource
- ✅ Tabla `resources` con campos:
  - `name`: Nombre del recurso
  - `serial_number`: Número de serie (único)
  - `type`: Tipo (Proyector, Laptop, Pizarra Digital, Micrófono, Parlantes, Otro)
  - `status`: Estado (Disponible, En Uso, Mantenimiento, Dañado)
  - `description`: Descripción adicional
  - `brand`: Marca
  - `model`: Modelo
  - `purchase_date`: Fecha de compra
  - Soft deletes habilitado

### 2. Relación Many-to-Many
- ✅ Tabla pivote `reservation_resources`
- ✅ Una reserva puede tener múltiples recursos
- ✅ Un recurso puede estar en múltiples reservas (en diferentes horarios)
- ✅ Campos adicionales en pivote:
  - `quantity`: Cantidad de recursos asignados
  - `notes`: Notas específicas del recurso en la reserva

### 3. Validaciones
- ✅ Validación de disponibilidad de aula
- ✅ Validación de disponibilidad de recursos
- ✅ Verificación de conflictos de horario
- ✅ Verificación de estado del recurso (no asignar si está en Mantenimiento o Dañado)

---

## 📊 Estructura de Base de Datos

### Tabla: `resources`
```sql
CREATE TABLE resources (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    serial_number VARCHAR(255) UNIQUE NULL,
    type ENUM('Proyector', 'Laptop', 'Pizarra Digital', 'Micrófono', 'Parlantes', 'Otro'),
    status ENUM('Disponible', 'En Uso', 'Mantenimiento', 'Dañado') DEFAULT 'Disponible',
    description TEXT NULL,
    brand VARCHAR(255) NULL,
    model VARCHAR(255) NULL,
    purchase_date DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

### Tabla: `reservation_resources`
```sql
CREATE TABLE reservation_resources (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    reservation_id BIGINT NOT NULL,
    resource_id BIGINT NOT NULL,
    quantity INT DEFAULT 1,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE,
    UNIQUE KEY (reservation_id, resource_id)
);
```

---

## 🔌 API Endpoints

### Recursos

#### Listar Recursos
```http
GET /api/resources
Query Parameters:
  - type: Proyector|Laptop|Pizarra Digital|Micrófono|Parlantes|Otro
  - status: Disponible|En Uso|Mantenimiento|Dañado
  - available: true|false
```

#### Crear Recurso
```http
POST /api/resources
Body:
{
  "name": "Proyector Epson EB-X41",
  "serial_number": "EPSON-001",
  "type": "Proyector",
  "status": "Disponible",
  "description": "Proyector de 3600 lúmenes",
  "brand": "Epson",
  "model": "EB-X41",
  "purchase_date": "2024-06-01"
}
```

#### Ver Recurso
```http
GET /api/resources/{id}
```

#### Actualizar Recurso
```http
PATCH /api/resources/{id}
Body:
{
  "status": "Mantenimiento",
  "description": "En mantenimiento preventivo"
}
```

#### Eliminar Recurso
```http
DELETE /api/resources/{id}
```

#### Verificar Disponibilidad
```http
POST /api/resources/{id}/check-availability
Body:
{
  "start_time": "2024-12-05 08:00:00",
  "end_time": "2024-12-05 10:00:00"
}
```

#### Recursos Disponibles en Horario
```http
GET /api/resources/available-at?start_time=2024-12-05 08:00:00&end_time=2024-12-05 10:00:00&type=Proyector
```

### Reservas con Recursos

#### Crear Reserva con Recursos
```http
POST /api/reservations
Body:
{
  "room_id": 1,
  "reserved_at": "2024-12-05 08:00:00",
  "expires_at": "2024-12-05 10:00:00",
  "notes": "Clase de programación",
  "resources": [
    {
      "id": 1,
      "quantity": 1,
      "notes": "Proyector para presentación"
    },
    {
      "id": 4,
      "quantity": 1,
      "notes": "Laptop para demostración"
    }
  ]
}
```

**Respuesta Exitosa (201)**:
```json
{
  "id": 1,
  "room_id": 1,
  "teacher_id": 1,
  "reserved_at": "2024-12-05 08:00:00",
  "expires_at": "2024-12-05 10:00:00",
  "notes": "Clase de programación",
  "resources": [
    {
      "id": 1,
      "name": "Proyector Epson EB-X41",
      "type": "Proyector",
      "pivot": {
        "quantity": 1,
        "notes": "Proyector para presentación"
      }
    },
    {
      "id": 4,
      "name": "Laptop Dell Latitude 5420",
      "type": "Laptop",
      "pivot": {
        "quantity": 1,
        "notes": "Laptop para demostración"
      }
    }
  ]
}
```

**Respuesta de Error - Recurso No Disponible (422)**:
```json
{
  "message": "Algunos recursos no están disponibles en ese horario",
  "error": "resource_conflict",
  "unavailable_resources": [
    {
      "id": 1,
      "name": "Proyector Epson EB-X41",
      "type": "Proyector",
      "status": "En Uso"
    }
  ]
}
```

---

## 💻 Uso en Código

### Verificar Disponibilidad de Recurso
```php
$resource = Resource::find(1);

$isAvailable = $resource->isAvailableAt(
    '2024-12-05 08:00:00',
    '2024-12-05 10:00:00'
);

if ($isAvailable) {
    // El recurso está disponible
} else {
    // El recurso no está disponible
}
```

### Crear Reserva con Recursos
```php
$reservation = Reservation::create([
    'room_id' => 1,
    'teacher_id' => 1,
    'reserved_at' => '2024-12-05 08:00:00',
    'expires_at' => '2024-12-05 10:00:00',
]);

// Asignar recursos
$reservation->resources()->attach(1, [
    'quantity' => 1,
    'notes' => 'Proyector para presentación'
]);

$reservation->resources()->attach(4, [
    'quantity' => 1,
    'notes' => 'Laptop para demostración'
]);
```

### Obtener Recursos de una Reserva
```php
$reservation = Reservation::with('resources')->find(1);

foreach ($reservation->resources as $resource) {
    echo $resource->name;
    echo $resource->pivot->quantity;
    echo $resource->pivot->notes;
}
```

### Filtrar Recursos Disponibles
```php
// Solo recursos disponibles
$available = Resource::available()->get();

// Proyectores disponibles
$projectors = Resource::available()->ofType('Proyector')->get();

// Recursos disponibles en un horario específico
$resources = Resource::available()->get()->filter(function ($resource) {
    return $resource->isAvailableAt('2024-12-05 08:00:00', '2024-12-05 10:00:00');
});
```

---

## 🧪 Datos de Prueba

Se crearon 12 recursos de prueba:

### Proyectores (3)
1. Proyector Epson EB-X41 (Disponible)
2. Proyector BenQ MH535 (Disponible)
3. Proyector Sony VPL-DX221 (Mantenimiento)

### Laptops (3)
4. Laptop Dell Latitude 5420 (Disponible)
5. Laptop HP ProBook 450 (Disponible)
6. Laptop Lenovo ThinkPad (Dañado)

### Pizarras Digitales (2)
7. Pizarra Digital Smart Board (Disponible)
8. Pizarra Digital Promethean (Disponible)

### Micrófonos (2)
9. Micrófono Inalámbrico Shure (Disponible)
10. Micrófono de Solapa Sennheiser (Disponible)

### Parlantes (2)
11. Sistema de Parlantes JBL (Disponible)
12. Parlantes Bose Portátiles (Disponible)

---

## 🔒 Validaciones Implementadas

### 1. Validación de Aula
```php
// Verifica que el aula no esté ocupada en el horario solicitado
$roomConflict = Reservation::where('room_id', $roomId)
    ->where(function ($query) use ($startTime, $endTime) {
        // Lógica de solapamiento de horarios
    })
    ->exists();
```

### 2. Validación de Recursos
```php
// Verifica que cada recurso esté disponible
foreach ($resources as $resourceData) {
    $resource = Resource::find($resourceData['id']);
    
    if (!$resource->isAvailableAt($startTime, $endTime)) {
        // Recurso no disponible
    }
}
```

### 3. Actualización Automática de Estado
```php
// Al asignar un recurso a una reserva, se actualiza su estado
Resource::where('id', $resourceId)->update(['status' => 'En Uso']);
```

---

## 📋 Permisos

### Docentes
- ✅ Ver recursos
- ✅ Verificar disponibilidad
- ✅ Crear reservas con recursos
- ❌ Crear/editar/eliminar recursos

### Administradores
- ✅ Ver recursos
- ✅ Crear recursos
- ✅ Editar recursos
- ✅ Eliminar recursos
- ✅ Verificar disponibilidad
- ✅ Crear reservas con recursos

---

## 🎯 Casos de Uso

### Caso 1: Reservar Aula con Proyector
```bash
# 1. Verificar aulas disponibles
GET /api/reservations/available?date=2024-12-05&start_time=08:00&end_time=10:00

# 2. Verificar proyectores disponibles
GET /api/resources/available-at?start_time=2024-12-05 08:00:00&end_time=2024-12-05 10:00:00&type=Proyector

# 3. Crear reserva con proyector
POST /api/reservations
{
  "room_id": 1,
  "reserved_at": "2024-12-05 08:00:00",
  "expires_at": "2024-12-05 10:00:00",
  "resources": [{"id": 1, "quantity": 1}]
}
```

### Caso 2: Poner Recurso en Mantenimiento
```bash
PATCH /api/resources/1
{
  "status": "Mantenimiento",
  "description": "Mantenimiento preventivo programado"
}
```

### Caso 3: Consultar Historial de Uso de un Recurso
```bash
GET /api/resources/1
# Incluye todas las reservas donde se usó el recurso
```

---

## ✅ Checklist de Implementación

- [x] Migración `resources` creada
- [x] Migración `reservation_resources` creada
- [x] Modelo `Resource` creado
- [x] Relación `belongsToMany` en `Resource`
- [x] Relación `belongsToMany` en `Reservation`
- [x] Método `isAvailableAt()` en `Resource`
- [x] Scopes `available()` y `ofType()` en `Resource`
- [x] `ResourceController` creado
- [x] Validación de disponibilidad de aula
- [x] Validación de disponibilidad de recursos
- [x] Actualización automática de estado
- [x] Rutas API configuradas
- [x] Seeder con datos de prueba
- [x] Soft deletes habilitado
- [x] Documentación completa

---

## 🚀 Próximos Pasos (Opcional)

1. **Vista Web para Gestión de Recursos**
   - CRUD de recursos desde la interfaz
   - Calendario de disponibilidad

2. **Notificaciones**
   - Alertar cuando un recurso necesita mantenimiento
   - Recordatorios de devolución

3. **Reportes**
   - Recursos más utilizados
   - Historial de mantenimiento
   - Costos de adquisición

4. **QR Codes**
   - Generar QR para cada recurso
   - Escanear para ver detalles

---

**Fecha de Implementación**: Diciembre 5, 2024
**Versión**: 1.0.0
**Estado**: ✅ COMPLETADO Y FUNCIONAL

---

## 🎉 ¡Sistema de Recursos Físicos Implementado!

Ahora puedes:
- ✅ Gestionar inventario de recursos
- ✅ Asignar recursos a reservas
- ✅ Validar disponibilidad automáticamente
- ✅ Evitar conflictos de horario
- ✅ Rastrear uso de recursos
