# 🎓 Sistema de Calificaciones - Guía de Uso

## 🚀 Acceso Rápido

### Para Docentes
1. Login en el sistema
2. Ir a **Dashboard Docente**
3. Clic en **"Calificaciones"** en el menú lateral
4. O acceder directamente: `http://127.0.0.1:8001/docente/calificaciones`

### Para Administradores
1. Login como admin
2. Ir a **Dashboard Admin**
3. Clic en **"Calificaciones"** en el menú
4. O acceder directamente: `http://127.0.0.1:8001/calificaciones`

---

## 📝 Paso a Paso: Ingresar Calificaciones

### 1️⃣ Seleccionar Grupo
![Selector de Grupo](https://via.placeholder.com/800x100/881F34/FFFFFF?text=Selector+de+Grupo)

- En el dropdown **"Grupo"**, selecciona el grupo que deseas calificar
- Automáticamente se cargará:
  - Lista de estudiantes
  - Criterios de evaluación
  - Calificaciones existentes (si las hay)

### 2️⃣ Ingresar Notas
![Tabla de Calificaciones](https://via.placeholder.com/800x300/FFFFFF/000000?text=Tabla+de+Calificaciones)

- Cada estudiante tiene una fila
- Cada criterio tiene una columna
- **Ingresa las notas** en cada celda (0-100)
- Puedes usar decimales: `85.5`, `92.75`, etc.

**Ejemplo de Criterios**:
| Criterio | Peso |
|----------|------|
| Parcial 1 | 25% |
| Parcial 2 | 25% |
| Trabajos | 20% |
| Proyecto | 20% |
| Participación | 10% |

### 3️⃣ Ver Cálculo Automático
- La **Nota Final** se calcula automáticamente
- El **Estado** se actualiza:
  - 🟢 **Aprobado** si Nota Final ≥ 51
  - 🔴 **Reprobado** si Nota Final < 51

### 4️⃣ Guardar Cambios
- Clic en **"💾 Guardar Todas las Calificaciones"**
- Espera el mensaje de confirmación
- Las celdas modificadas se marcan en amarillo hasta guardar

---

## 📊 Exportar Reportes

### Exportar a PDF 📄
1. Clic en **"📄 Exportar PDF"**
2. Se descarga automáticamente
3. El PDF incluye:
   - Encabezado institucional FICCT
   - Información del grupo y materia
   - Tabla completa de calificaciones
   - Estadísticas: promedio, aprobados, reprobados
   - Fecha de generación

**Características del PDF**:
- ✅ Formato profesional
- ✅ Soporte UTF-8 (ñ, á, é, í, ó, ú)
- ✅ Listo para imprimir
- ✅ Tamaño A4

### Exportar a Excel 📊
1. Clic en **"📊 Exportar Excel"**
2. Se descarga archivo `.xlsx`
3. El Excel incluye:
   - Encabezados con colores institucionales
   - Todas las calificaciones
   - Nota final y estado
   - Formato UTF-8

**Características del Excel**:
- ✅ Formato XLSX (compatible con Excel, Google Sheets, LibreOffice)
- ✅ Columnas auto-ajustables
- ✅ Soporte UTF-8 completo
- ✅ Listo para análisis de datos

---

## 📈 Estadísticas en Tiempo Real

En la parte superior verás:

```
┌─────────────────────────────────────────────────────────┐
│  Total Estudiantes: 25                                  │
│  Promedio General: 78.50                                │
│  Aprobados: 22                                          │
│  Reprobados: 3                                          │
└─────────────────────────────────────────────────────────┘
```

Estas estadísticas se actualizan automáticamente al cambiar las notas.

---

## 🎯 Ejemplos de Uso

### Ejemplo 1: Calificar Parcial 1
```
Estudiante: Juan Pérez
Parcial 1 (25%): 85
→ Contribuye: 85 × 0.25 = 21.25 puntos
```

### Ejemplo 2: Cálculo Completo
```
Estudiante: María García
- Parcial 1 (25%): 80 → 20.00
- Parcial 2 (25%): 90 → 22.50
- Trabajos (20%): 85 → 17.00
- Proyecto (20%): 95 → 19.00
- Participación (10%): 100 → 10.00
─────────────────────────────
Nota Final: 88.50 ✅ APROBADO
```

### Ejemplo 3: Estudiante Reprobado
```
Estudiante: Carlos López
- Parcial 1 (25%): 40 → 10.00
- Parcial 2 (25%): 45 → 11.25
- Trabajos (20%): 50 → 10.00
- Proyecto (20%): 55 → 11.00
- Participación (10%): 60 → 6.00
─────────────────────────────
Nota Final: 48.25 ❌ REPROBADO
```

---

## ⚠️ Validaciones

### Notas Válidas
- ✅ Rango: 0 a 100
- ✅ Decimales permitidos: `85.5`, `92.75`
- ✅ Enteros: `80`, `95`

### Notas Inválidas
- ❌ Menor a 0
- ❌ Mayor a 100
- ❌ Texto: `"bueno"`, `"excelente"`
- ❌ Vacío (se considera 0)

---

## 🔧 Solución de Problemas

### Problema: No veo estudiantes
**Solución**: 
1. Verifica que el grupo tenga estudiantes asignados
2. Contacta al administrador para agregar estudiantes

### Problema: No puedo guardar
**Solución**:
1. Verifica que todas las notas estén en el rango 0-100
2. Revisa tu conexión a internet
3. Recarga la página e intenta nuevamente

### Problema: El PDF no muestra caracteres especiales
**Solución**:
- El sistema ya está configurado para UTF-8
- Si persiste, reporta al administrador

### Problema: El Excel no abre correctamente
**Solución**:
1. Asegúrate de tener Excel 2010 o superior
2. O usa Google Sheets / LibreOffice Calc
3. El archivo es `.xlsx`, no `.xls`

---

## 📱 Uso en Móvil

El sistema es **responsive** y funciona en:
- 📱 Smartphones
- 📱 Tablets
- 💻 Laptops
- 🖥️ Desktops

**Recomendaciones para móvil**:
- Usa modo horizontal para mejor visualización
- La tabla tiene scroll horizontal
- Los botones son táctiles

---

## 🎨 Interfaz Visual

### Colores del Sistema
- 🔴 **Rojo FICCT** (#881F34): Encabezados y botones principales
- 🟢 **Verde**: Aprobados
- 🔴 **Rojo**: Reprobados
- 🔵 **Azul**: Nota final
- 🟡 **Amarillo**: Cambios pendientes

### Iconos
- 💾 Guardar
- 📄 PDF
- 📊 Excel
- ✅ Aprobado
- ❌ Reprobado

---

## 📞 Soporte

### Contacto
- **Email**: soporte@ficct.edu.bo
- **Teléfono**: +591 XXX XXXXX
- **Horario**: Lunes a Viernes, 8:00 - 18:00

### Reportar Problemas
1. Captura de pantalla del error
2. Descripción del problema
3. Pasos para reproducir
4. Enviar a soporte

---

## ✅ Checklist de Uso

Antes de finalizar, verifica:

- [ ] Seleccioné el grupo correcto
- [ ] Ingresé todas las notas
- [ ] Las notas están en el rango 0-100
- [ ] Revisé las notas finales
- [ ] Guardé los cambios
- [ ] Exporté el reporte (si es necesario)
- [ ] Verifiqué que se guardaron correctamente

---

## 🎓 Buenas Prácticas

### Para Docentes
1. **Guarda frecuentemente**: No esperes a ingresar todas las notas
2. **Verifica antes de guardar**: Revisa que las notas sean correctas
3. **Exporta respaldos**: Descarga Excel como respaldo
4. **Comunica a estudiantes**: Informa cuando las notas estén disponibles

### Para Administradores
1. **Configura criterios antes**: Define criterios de evaluación primero
2. **Asigna pesos correctos**: Verifica que sumen 100%
3. **Capacita docentes**: Asegúrate de que sepan usar el sistema
4. **Monitorea uso**: Revisa que se estén ingresando calificaciones

---

## 🚀 Próximas Funcionalidades

En desarrollo:
- 📧 Notificaciones por email a estudiantes
- 📊 Gráficos de rendimiento
- 📝 Comentarios por estudiante
- 🔄 Historial de cambios
- 📱 App móvil nativa

---

## 📚 Recursos Adicionales

### Documentación Técnica
- `CALIFICACIONES_COMPLETO.md`: Documentación técnica completa
- `CRITERIOS_EVALUACION_COMPLETADO.md`: Criterios de evaluación

### Videos Tutoriales
- 🎥 Cómo ingresar calificaciones (5 min)
- 🎥 Cómo exportar reportes (3 min)
- 🎥 Solución de problemas comunes (7 min)

---

**Última Actualización**: Diciembre 2024
**Versión del Sistema**: 1.0.0
**Estado**: ✅ Producción

---

## 🎉 ¡Listo para Usar!

El sistema está completamente funcional y listo para:
- ✅ Ingresar calificaciones manualmente
- ✅ Guardar de forma masiva
- ✅ Exportar a PDF con UTF-8
- ✅ Exportar a Excel con UTF-8
- ✅ Calcular notas finales automáticamente
- ✅ Ver estadísticas en tiempo real

**¡Comienza a calificar ahora!** 🚀
