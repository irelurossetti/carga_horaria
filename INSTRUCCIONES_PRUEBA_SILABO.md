# 🧪 Instrucciones de Prueba - Módulo de Sílabo

## ✅ Estado de Implementación

**COMPLETADO** - El módulo está 100% funcional y listo para usar.

## 🚀 Pasos para Probar el Módulo

### 1. Verificar Migraciones

Las migraciones ya fueron ejecutadas exitosamente:
- ✅ `syllabus_topics` - Tabla creada
- ✅ `attendance_syllabus_topic` - Tabla pivote creada

### 2. Poblar Datos de Ejemplo

Ya se ejecutó el seeder, pero puedes volver a ejecutarlo:

```bash
php artisan db:seed --class=SyllabusTopicsSeeder
```

Esto creará 5-8 temas para las primeras 3 materias en tu base de datos.

### 3. Acceder a las Vistas

#### Como Administrador/Coordinador:

1. **Gestión de Temas del Sílabo**
   - URL: `http://localhost/admin/silabo`
   - Menú: Sidebar → Evaluación → "Gestión de Sílabo"
   - Funciones:
     - Seleccionar materia
     - Ver temas existentes
     - Agregar nuevo tema
     - Editar tema
     - Eliminar tema

2. **Dashboard del Coordinador**
   - URL: `http://localhost/coordinator/dashboard`
   - Buscar el widget "Progreso del Sílabo"
   - Ver barras de progreso por grupo

3. **Asistencia por Grupo**
   - URL: `http://localhost/admin/asistencia-grupo`
   - Seleccionar un grupo
   - Ver sección "Progreso del Sílabo"
   - Visualizar temas cubiertos vs pendientes

#### Como Docente:

1. **Progreso del Sílabo**
   - URL: `http://localhost/docente/progreso-silabo`
   - Seleccionar grupo
   - Ver progreso detallado
   - Identificar temas pendientes

2. **Registrar Asistencia con Temas** (Componente Livewire)
   - Usar el componente `@livewire('attendance-with-topics')`
   - Seleccionar grupo y horario
   - Marcar temas vistos con checkboxes
   - Guardar asistencia

### 4. Probar API Endpoints

#### Listar temas de una materia:
```bash
curl -X GET "http://localhost/api/syllabus-topics?subject_id=1" \
  -H "Accept: application/json"
```

#### Crear un nuevo tema (requiere autenticación admin):
```bash
curl -X POST "http://localhost/api/syllabus-topics" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{
    "subject_id": 1,
    "unit_name": "Unidad 5",
    "topic_description": "Programación orientada a objetos",
    "order_index": 5
  }'
```

#### Registrar asistencia con temas:
```bash
curl -X POST "http://localhost/api/attendances" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{
    "teacher_id": 1,
    "schedule_id": 5,
    "date": "2025-12-05",
    "time": "10:00",
    "status": "present",
    "topic_ids": [1, 2, 3]
  }'
```

## 🎯 Casos de Prueba

### Caso 1: Gestión de Temas

1. Iniciar sesión como administrador
2. Ir a "Gestión de Sílabo"
3. Seleccionar una materia del dropdown
4. Verificar que se muestren los temas existentes
5. Hacer clic en "Agregar Tema"
6. Llenar el formulario:
   - Materia: (preseleccionada)
   - Unidad: "Unidad 10"
   - Descripción: "Tema de prueba"
   - Orden: 10
7. Guardar y verificar que aparezca en la lista
8. Editar el tema recién creado
9. Eliminar el tema

**Resultado Esperado**: Todas las operaciones CRUD funcionan correctamente.

### Caso 2: Registro de Asistencia con Temas

1. Iniciar sesión como docente
2. Ir a la vista de registro de asistencia
3. Seleccionar un grupo
4. Verificar que aparezcan los temas de la materia
5. Marcar 2-3 temas como vistos
6. Completar el resto del formulario
7. Guardar la asistencia

**Resultado Esperado**: La asistencia se guarda con los temas seleccionados.

### Caso 3: Visualización de Progreso

1. Iniciar sesión como coordinador
2. Ir al dashboard
3. Buscar el widget "Progreso del Sílabo"
4. Verificar que muestre grupos con barras de progreso
5. Ir a "Asistencia por Grupo"
6. Seleccionar un grupo
7. Verificar la sección "Progreso del Sílabo"

**Resultado Esperado**: El progreso se calcula y muestra correctamente.

### Caso 4: Vista de Docente

1. Iniciar sesión como docente
2. Ir a "Progreso del Sílabo"
3. Seleccionar un grupo
4. Verificar el progreso general
5. Revisar la lista de temas con estados

**Resultado Esperado**: Se muestra el progreso detallado del grupo.

## 🐛 Posibles Problemas y Soluciones

### Problema 1: No aparecen temas
**Solución**: Ejecutar el seeder o crear temas manualmente desde la interfaz.

### Problema 2: Error 404 en rutas
**Solución**: Limpiar caché de rutas:
```bash
php artisan route:clear
php artisan route:cache
```

### Problema 3: No se calculan los progresos
**Solución**: Verificar que existan:
- Grupos con subject_id asignado
- Temas creados para esas materias
- Asistencias registradas con temas

### Problema 4: Error en componente Livewire
**Solución**: Limpiar caché de vistas:
```bash
php artisan view:clear
php artisan livewire:discover
```

## 📊 Datos de Prueba Recomendados

### Crear Temas Manualmente:

**Materia**: Programación I
- Unidad 1: Introducción a la programación
- Unidad 2: Variables y tipos de datos
- Unidad 3: Estructuras de control
- Unidad 4: Funciones y procedimientos
- Unidad 5: Arreglos y matrices

**Materia**: Base de Datos
- Unidad 1: Introducción a bases de datos
- Unidad 2: Modelo relacional
- Unidad 3: SQL básico
- Unidad 4: SQL avanzado
- Unidad 5: Normalización

## ✨ Características Destacadas

1. **Interfaz Intuitiva**: Diseño limpio con Tailwind CSS
2. **Validación en Tiempo Real**: Feedback inmediato al usuario
3. **Responsive**: Funciona en móviles y tablets
4. **Integración Completa**: Conectado con asistencia y grupos
5. **Cálculo Automático**: Progreso actualizado en tiempo real
6. **Código de Colores**: Verde (≥70%), Amarillo (≥40%), Rojo (<40%)

## 🎓 Flujo de Trabajo Recomendado

1. **Coordinador**: Configura los temas del sílabo al inicio del semestre
2. **Docente**: Marca temas vistos al registrar asistencia cada clase
3. **Coordinador**: Monitorea el progreso semanalmente
4. **Administrador**: Genera reportes al final del semestre

## 📝 Notas Importantes

- Los temas se ordenan automáticamente por `order_index`
- Un tema puede estar en múltiples clases (relación muchos a muchos)
- El progreso se calcula por grupo, no por docente
- Los temas eliminados no afectan asistencias pasadas
- El sistema es retrocompatible con asistencias sin temas

## 🔗 Enlaces Rápidos

- Gestión de Temas: `/admin/silabo`
- Dashboard Coordinador: `/coordinator/dashboard`
- Progreso Docente: `/docente/progreso-silabo`
- Asistencia por Grupo: `/admin/asistencia-grupo`
- API Temas: `/api/syllabus-topics`

---

**¡El módulo está listo para producción!** 🎉

Si encuentras algún problema, revisa el archivo `MODULO_SILABO_IMPLEMENTADO.md` para más detalles técnicos.
