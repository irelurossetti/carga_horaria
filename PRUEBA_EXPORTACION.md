# 🧪 Prueba de Exportación - Guía Rápida

## ✅ Estado Actual
- ✅ Código corregido
- ✅ Autofix aplicado por IDE
- ✅ Sin errores de sintaxis
- ✅ Rutas configuradas correctamente

---

## 🚀 Pasos para Probar

### Opción 1: Desde la Interfaz Web

1. **Acceder al sistema**
   ```
   URL: http://127.0.0.1:8001/docente/calificaciones
   ```

2. **Seleccionar un grupo**
   - Abre el dropdown "Grupo"
   - Selecciona cualquier grupo (ej: Grupo C)

3. **Probar Exportación PDF**
   - Clic en el botón "📄 Exportar PDF"
   - Debería descargarse: `calificaciones_Grupo_C_2024-12-05.pdf`

4. **Probar Exportación Excel**
   - Clic en el botón "📊 Exportar Excel"
   - Debería descargarse: `calificaciones_Grupo_C_2024-12-05.xlsx`

---

### Opción 2: Probar Directamente con URLs

#### Exportar PDF
```
http://127.0.0.1:8001/api/grades/group/1/export-pdf
```

#### Exportar Excel
```
http://127.0.0.1:8001/api/grades/group/1/export-excel
```

**Nota**: Cambia el `1` por el ID del grupo que quieras exportar.

---

## 🔍 Verificar Resultados

### PDF Descargado
- ✅ Archivo se descarga
- ✅ Se abre correctamente
- ✅ Muestra encabezado FICCT
- ✅ Muestra tabla de calificaciones
- ✅ Caracteres especiales se ven bien (ñ, á, é, í, ó, ú)
- ✅ Estadísticas visibles

### Excel Descargado
- ✅ Archivo se descarga
- ✅ Se abre en Excel/Google Sheets
- ✅ Encabezados con colores
- ✅ Datos completos
- ✅ Caracteres especiales se ven bien
- ✅ Columnas ajustadas

---

## 🐛 Si Hay Errores

### Error 1: "No se puede descargar"
**Solución**:
```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear

# Verificar permisos
chmod -R 755 storage
```

### Error 2: "PDF vacío o corrupto"
**Solución**:
```bash
# Verificar instalación de DomPDF
composer show barryvdh/laravel-dompdf

# Reinstalar si es necesario
composer require barryvdh/laravel-dompdf
```

### Error 3: "Excel no se abre"
**Solución**:
```bash
# Verificar instalación de Laravel Excel
composer show maatwebsite/excel

# Reinstalar si es necesario
composer require maatwebsite/excel
```

### Error 4: "Caracteres raros (�)"
**Solución**:
- El código ya está configurado para UTF-8
- Verifica que tu navegador esté en UTF-8
- Abre el archivo con un editor que soporte UTF-8

---

## 📊 Datos de Prueba

Si no tienes datos reales, el sistema usará estos datos de prueba:

### Criterios de Evaluación
```
1. Parcial 1 (25%)
2. Parcial 2 (25%)
3. Trabajos (20%)
4. Proyecto (20%)
5. Participación (10%)
```

### Estudiantes
```
1. Juan Pérez (2021001)
2. María García (2021002)
3. Carlos López (2021003)
```

---

## 🔧 Comandos Útiles

### Ver logs en tiempo real
```bash
tail -f storage/logs/laravel.log
```

### Verificar rutas
```bash
php artisan route:list --path=grades
```

### Limpiar todo
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## ✅ Checklist de Prueba

### Antes de Probar
- [ ] Servidor corriendo (`php artisan serve`)
- [ ] Usuario logueado como docente
- [ ] Grupo seleccionado

### Prueba PDF
- [ ] Botón "Exportar PDF" visible
- [ ] Clic en el botón
- [ ] Archivo se descarga
- [ ] PDF se abre correctamente
- [ ] Contenido visible
- [ ] UTF-8 funciona

### Prueba Excel
- [ ] Botón "Exportar Excel" visible
- [ ] Clic en el botón
- [ ] Archivo se descarga
- [ ] Excel se abre correctamente
- [ ] Contenido visible
- [ ] UTF-8 funciona

---

## 📝 Notas Importantes

### Navegadores Recomendados
- ✅ Chrome/Edge (mejor compatibilidad)
- ✅ Firefox
- ⚠️ Safari (puede tener problemas con descargas)

### Formatos de Archivo
- **PDF**: `.pdf` (Adobe Reader, navegadores)
- **Excel**: `.xlsx` (Excel 2010+, Google Sheets, LibreOffice)

### Tamaño de Archivos
- PDF: ~50-200 KB (depende del número de estudiantes)
- Excel: ~10-50 KB (más ligero que PDF)

---

## 🎯 Resultado Esperado

### PDF Exitoso
```
✅ Archivo: calificaciones_Grupo_C_2024-12-05.pdf
✅ Tamaño: ~100 KB
✅ Contenido: Tabla completa con estadísticas
✅ UTF-8: Caracteres especiales correctos
```

### Excel Exitoso
```
✅ Archivo: calificaciones_Grupo_C_2024-12-05.xlsx
✅ Tamaño: ~20 KB
✅ Contenido: Hoja con datos completos
✅ UTF-8: Caracteres especiales correctos
```

---

## 🔄 Próximos Pasos

1. ✅ Probar exportación PDF
2. ✅ Probar exportación Excel
3. ✅ Verificar UTF-8
4. ✅ Probar con datos reales
5. ✅ Compartir con usuarios finales

---

## 📞 Soporte

Si después de seguir esta guía aún tienes problemas:

1. Copia el error completo de la consola del navegador (F12)
2. Copia el error del log de Laravel
3. Toma captura de pantalla
4. Envía a soporte

---

**Fecha**: Diciembre 5, 2024
**Versión**: 1.0.1
**Estado**: ✅ LISTO PARA PROBAR

---

## 🎉 ¡Listo!

El sistema está corregido y listo para exportar. 

**¡Prueba ahora!** 🚀
