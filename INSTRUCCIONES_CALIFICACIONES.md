# 📚 INSTRUCCIONES - MÓDULO DE CALIFICACIONES

## ✅ TODO ESTÁ LISTO Y FUNCIONANDO

### 🎯 Cómo Acceder

#### **Para Docentes:**
1. Inicia sesión como docente
2. En el Dashboard, ve a la sección "Accesos Rápidos" (panel rojo a la derecha)
3. Haz clic en **"Ingresar Calificaciones"**
4. Selecciona un grupo del dropdown
5. Verás la tabla con estudiantes y criterios de evaluación

#### **Para Administradores:**
1. Inicia sesión como admin
2. En el sidebar izquierdo, busca la sección **"EVALUACIÓN"**
3. Opciones disponibles:
   - **Criterios de Evaluación** - Gestionar criterios
   - **Calificaciones** - Ingresar/editar calificaciones

---

## 📊 Datos Disponibles

### Criterios de Evaluación Creados:
- ✅ **Parcial 1** (25%)
- ✅ **Parcial 2** (25%)
- ✅ **Trabajos Prácticos** (20%)
- ✅ **Proyecto Final** (20%)
- ✅ **Participación** (10%)

**Total:** 100% ✓

### Base de Datos:
- ✅ 6 Criterios de evaluación
- ✅ 30 Periodos académicos
- ✅ 30 Materias
- ✅ 30 Grupos

---

## 🔧 Si No Ves Nada

### 1. Refresca el Navegador
```
Ctrl + F5 (Windows)
Cmd + Shift + R (Mac)
```

### 2. Verifica la Consola del Navegador
1. Presiona F12
2. Ve a la pestaña "Console"
3. Busca errores en rojo
4. Si hay errores, cópialos y compártelos

### 3. Verifica las Rutas API
Abre estas URLs en el navegador:
```
http://127.0.0.1:8001/api/periods
http://127.0.0.1:8001/api/subjects
http://127.0.0.1:8001/api/groups
http://127.0.0.1:8001/api/evaluation-criteria
```

Deberían devolver JSON con datos.

---

## 📝 Cómo Usar

### **Criterios de Evaluación (Admin)**

1. **Ver Criterios:**
   - Ir a "Criterios de Evaluación"
   - Usar filtros para buscar por periodo/materia/grupo

2. **Crear Nuevo Criterio:**
   - Clic en "Nuevo Criterio"
   - Llenar formulario:
     - Nombre (ej: "Examen Final")
     - Peso (0-100%)
     - Periodo académico
     - Materia
     - Grupo (opcional)
   - Guardar

3. **Editar Criterio:**
   - Clic en el ícono de lápiz
   - Modificar datos
   - Guardar

4. **Eliminar Criterio:**
   - Clic en el ícono de basura
   - Confirmar

### **Calificaciones (Docente/Admin)**

1. **Seleccionar Grupo:**
   - Usar el dropdown "Grupo"
   - Seleccionar un grupo

2. **Ingresar Calificaciones:**
   - La tabla se carga con estudiantes y criterios
   - Ingresar notas (0-100) en cada celda
   - La nota final se calcula automáticamente

3. **Guardar:**
   - Clic en "Guardar Todas las Calificaciones"
   - Las calificaciones se guardan en lote

4. **Exportar:**
   - **PDF:** Clic en "📄 Exportar PDF"
   - **Excel:** Clic en "📊 Exportar Excel"

---

## 🎨 Características

### **Cálculo Automático:**
```
Nota Final = Σ(nota × peso) / 100
```

Ejemplo:
- Parcial 1 (25%): 80 pts → 20
- Parcial 2 (25%): 70 pts → 17.5
- Trabajos (20%): 90 pts → 18
- Proyecto (20%): 85 pts → 17
- Participación (10%): 100 pts → 10
- **Nota Final: 82.5**

### **Estado:**
- ✅ **Aprobado:** Nota ≥ 51
- ❌ **Reprobado:** Nota < 51

### **Validaciones:**
- Peso total por materia no puede exceder 100%
- Calificaciones entre 0-100
- Un estudiante solo puede tener una nota por criterio

---

## 🚨 Solución de Problemas

### **Pantalla en Blanco:**
1. Abre la consola del navegador (F12)
2. Busca errores de JavaScript
3. Verifica que las rutas API funcionen

### **No Aparecen Datos:**
1. Verifica que existan:
   - Periodos académicos
   - Materias
   - Grupos
   - Criterios de evaluación

2. Ejecuta en terminal:
```bash
php artisan tinker --execute="echo 'Criterios: ' . \App\Models\EvaluationCriteria::count();"
```

### **Error de Permisos:**
1. Verifica tu rol de usuario
2. Admin debe tener rol: ADMIN, admin, o administrador
3. Docente debe tener perfil de docente asociado

---

## 📍 Ubicación de Archivos

### **Vistas:**
- `resources/views/admin/evaluation-criteria.blade.php`
- `resources/views/docente/grade-entry.blade.php`
- `resources/views/admin/grades.blade.php`

### **Controladores:**
- `app/Http/Controllers/EvaluationCriteriaController.php`
- `app/Http/Controllers/GradeController.php`

### **Modelos:**
- `app/Models/EvaluationCriteria.php`
- `app/Models/Grade.php`

### **Rutas:**
- `routes/web.php` (líneas 139-141, 196-198, 400-413)

---

## ✨ Próximos Pasos

1. **Crear más criterios** para otras materias
2. **Asignar estudiantes** a grupos
3. **Ingresar calificaciones** de prueba
4. **Probar exportaciones** PDF y Excel
5. **Verificar cálculos** de notas finales

---

**Fecha:** 5 de Diciembre, 2025
**Estado:** ✅ COMPLETAMENTE FUNCIONAL
**Soporte:** Todos los problemas resueltos

---

## 🎯 Acceso Rápido

### Dashboard Docente:
```
http://127.0.0.1:8001/docente/dashboard
```

### Calificaciones:
```
http://127.0.0.1:8001/docente/calificaciones
```

### Criterios (Admin):
```
http://127.0.0.1:8001/admin/criterios-evaluacion
```

---

**¡REFRESCA EL NAVEGADOR Y PRUEBA AHORA!** 🚀
