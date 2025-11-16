# 🌱 Seeder Completo - Base de Datos Poblada

## ✅ COMPLETADO EXITOSAMENTE

La base de datos ha sido poblada con **30+ registros de cada entidad** del sistema.

## 📊 Registros Creados

| Entidad | Cantidad | Descripción |
|---------|----------|-------------|
| **Roles** | 4 | ADMIN, COORDINADOR, DOCENTE, ESTUDIANTE |
| **Usuarios** | 61 | 1 admin + 30 docentes + 30 estudiantes |
| **Periodos Académicos** | 30 | Desde Gestión 1-2020 hasta Gestión 2-2034 |
| **Aulas** | 30 | Distribuidas en edificios A, B, C, D |
| **Materias** | 30 | Desde INF-101 hasta ADM-402 |
| **Docentes** | 30 | Con usuarios, emails y departamentos |
| **Grupos** | 30 | Grupos A, B, C, D, E por materia |
| **Asignaciones** | 30 | Docentes asignados a materias y grupos |
| **Horarios** | 30 | Distribuidos de lunes a viernes |
| **Asistencias** | 30 | Registros de asistencia de docentes |
| **Anulaciones** | 30 | Clases canceladas o virtuales |
| **Conflictos** | 30 | Conflictos de horarios detectados |
| **Reservas** | 30 | Reservas de aulas |
| **Anuncios** | 30 | Anuncios del sistema |
| **Incidencias** | 30 | Incidencias reportadas |
| **Bitácora** | 50 | Registros de actividad del sistema |
| **TOTAL** | **505** | **Registros en total** |

## 🚀 Cómo Ejecutar el Seeder

### Opción 1: Limpiar y poblar desde cero (RECOMENDADO)

```bash
php artisan migrate:fresh --seed --seeder=CompleteSeeder
```

Este comando:
1. Elimina todas las tablas
2. Ejecuta todas las migraciones
3. Ejecuta el CompleteSeeder automáticamente

### Opción 2: Solo ejecutar el seeder (si ya tienes las tablas)

```bash
php artisan db:seed --class=CompleteSeeder
```

⚠️ **ADVERTENCIA:** Esta opción puede fallar si ya existen datos con los mismos códigos/emails.

## 🔑 Credenciales de Acceso

### Administrador
```
Email: admin@ficct.edu.bo
Password: password
```

### Docentes (30 usuarios)
```
Email: docente1@ficct.edu.bo hasta docente30@ficct.edu.bo
Password: password
```

**Ejemplos:**
- docente1@ficct.edu.bo (Dr. Juan Pérez García)
- docente15@ficct.edu.bo (Dr. Alberto Cruz Navarro)
- docente30@ficct.edu.bo (Dr. Héctor Ramos Ortiz)

### Estudiantes (30 usuarios)
```
Email: est001@ficct.edu.bo hasta est030@ficct.edu.bo
Password: password
```

**Ejemplos:**
- est001@ficct.edu.bo (Alejandro González)
- est015@ficct.edu.bo (Oscar Hernández)
- est030@ficct.edu.bo (Diego Torres)

## 📝 Detalles de los Datos Generados

### Periodos Académicos
- 30 periodos desde 2020 hasta 2034
- El último periodo (Gestión 1-2025) está **activo**
- Los periodos anteriores están **cerrados**
- Los últimos 2 están en estado **draft** (planificados)

### Aulas
- 30 aulas en edificios A, B, C, D
- Capacidades entre 25 y 45 estudiantes
- Recursos variados: proyector, computadora, pizarra, aire acondicionado
- Ubicaciones en diferentes pisos

### Materias
- 30 materias de diferentes áreas
- Códigos desde INF-101 hasta ADM-402
- Créditos entre 2 y 4
- Incluye: Programación, Matemáticas, Física, Bases de Datos, Redes, IA, etc.

### Docentes
- 30 docentes con nombres realistas
- Distribuidos en 5 departamentos:
  - Sistemas
  - Redes
  - Industrial
  - Electrónica
  - Civil
- Cada uno con DNI, teléfono y email único

### Grupos
- 30 grupos (A, B, C, D, E)
- Asignados a diferentes materias
- Capacidades entre 25 y 40 estudiantes

### Horarios
- 30 horarios de clases
- Distribuidos de lunes a viernes
- Horarios de 7:00 AM a 1:00 PM
- Asignados a aulas, docentes y grupos específicos

### Asistencias
- 30 registros de asistencia de docentes
- Estados: presente, ausente, tarde
- Fechas de los últimos 30 días
- Algunas con observaciones

### Anulaciones
- 30 anulaciones de clases
- Modos: cancelada o virtual
- Razones variadas: feriados, enfermedad, mantenimiento, etc.

### Conflictos
- 30 conflictos detectados entre horarios
- Tipos: teacher, room, time, capacity
- 50% resueltos, 50% pendientes

### Reservas
- 30 reservas de aulas
- Fechas futuras (próximos 30 días)
- Duración de 2 horas cada una
- Algunas con notas especiales

### Anuncios
- 30 anuncios del sistema
- Títulos variados: inicio de clases, exámenes, eventos, etc.
- Algunos marcados como importantes (pinned)
- Fechas de publicación y expiración

### Incidencias
- 30 incidencias reportadas
- Estados: open, in_progress, resolved
- Relacionadas con aulas y docentes
- Algunas ya resueltas

### Bitácora
- 50 registros de actividad del sistema
- Acciones: login, logout, create, update, delete, view, export, etc.
- Módulos: users, teachers, schedules, attendances, etc.
- Incluye IP, user agent y timestamps

## 🔄 Volver a Empezar

Si necesitas limpiar todo y empezar de nuevo:

```bash
php artisan migrate:fresh --seed --seeder=CompleteSeeder
```

Este comando es seguro y recreará todo desde cero.

## 📁 Ubicación del Seeder

```
database/seeders/CompleteSeeder.php
```

## ✨ Características del Seeder

- ✅ Respeta todas las relaciones de claves foráneas
- ✅ Crea datos en el orden correcto
- ✅ Usa datos realistas y variados
- ✅ Compatible con PostgreSQL
- ✅ Maneja correctamente los tipos de datos booleanos
- ✅ Genera IDs automáticamente
- ✅ Incluye timestamps correctos
- ✅ Muestra progreso en consola
- ✅ Resumen final con estadísticas

## 🎉 ¡Listo para Usar!

Tu base de datos ahora tiene **505 registros** distribuidos en **16 tablas** diferentes, lista para pruebas completas del sistema de gestión de carga horaria.

Puedes iniciar sesión con cualquiera de las credenciales proporcionadas y explorar todas las funcionalidades del sistema.
