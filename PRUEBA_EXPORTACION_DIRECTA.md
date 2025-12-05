# 🧪 Prueba Directa de Exportación

## ✅ Archivos Creados

1. **public/test-export.html** - Página de prueba con botones
2. **routes/test-export.php** - Rutas de prueba simplificadas

---

## 🚀 Cómo Probar

### Opción 1: Página de Prueba HTML

1. Abre tu navegador
2. Ve a: `http://127.0.0.1:8001/test-export.html`
3. Haz clic en cualquier botón de exportación
4. El archivo debería descargarse automáticamente

### Opción 2: URLs Directas de Prueba

#### PDF de Prueba
```
http://127.0.0.1:8001/test/pdf/1
```

#### Excel de Prueba
```
http://127.0.0.1:8001/test/excel/1
```

### Opción 3: URLs de Producción

#### PDF Real
```
http://127.0.0.1:8001/api/grades/group/1/export-pdf
```

#### Excel Real
```
http://127.0.0.1:8001/api/grades/group/1/export-excel
```

---

## 📋 Datos de Prueba

Las rutas de prueba (`/test/pdf` y `/test/excel`) usan estos datos:

### Estudiantes
1. Juan Pérez (2021001) - Nota Final: 88.75
2. María García (2021002) - Nota Final: 83.75
3. Carlos López (2021003) - Nota Final: 78.75

### Criterios
1. Parcial 1 (25%)
2. Parcial 2 (25%)
3. Trabajos (20%)
4. Proyecto (20%)
5. Participación (10%)

---

## 🔍 Verificar Resultados

### Si Funciona
- ✅ Se descarga el archivo
- ✅ El archivo se abre correctamente
- ✅ Los datos se ven bien
- ✅ Los caracteres especiales (ñ, á, é) se ven correctamente

### Si No Funciona
1. Abre la consola del navegador (F12)
2. Ve a la pestaña "Console"
3. Copia el error completo
4. Ve a la pestaña "Network"
5. Busca la petición que falló
6. Copia la respuesta

---

## 🛠️ Comandos Ejecutados

```bash
# Limpiar caché
php artisan route:clear
php artisan cache:clear

# Verificar rutas
php artisan route:list --path=test
php artisan route:list --path=grades
```

---

## 📝 Diferencias entre Rutas

### Rutas de Prueba (`/test/*`)
- ✅ Usan datos hardcodeados
- ✅ No requieren base de datos
- ✅ Siempre funcionan
- ✅ Útiles para debugging

### Rutas de Producción (`/api/grades/*`)
- ✅ Usan datos reales de la BD
- ✅ Requieren grupos y estudiantes
- ✅ Más completas
- ✅ Para uso real

---

## 🎯 Próximos Pasos

1. **Probar rutas de prueba** (`/test/pdf/1` y `/test/excel/1`)
   - Si funcionan → El problema está en los datos de la BD
   - Si no funcionan → El problema está en las librerías

2. **Si las rutas de prueba funcionan**:
   - Verificar que existan grupos en la BD
   - Verificar que existan estudiantes
   - Verificar que existan criterios de evaluación

3. **Si las rutas de prueba NO funcionan**:
   - Verificar instalación de librerías
   - Verificar permisos de storage
   - Verificar logs de Laravel

---

## 🔧 Troubleshooting

### Error: "Class 'Barryvdh\DomPDF\Facade\Pdf' not found"
```bash
composer require barryvdh/laravel-dompdf
php artisan config:clear
```

### Error: "Class 'Maatwebsite\Excel\Facades\Excel' not found"
```bash
composer require maatwebsite/excel
php artisan config:clear
```

### Error: "Permission denied"
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Error: "View not found"
```bash
php artisan view:clear
php artisan cache:clear
```

---

## 📊 Verificar Instalación

```bash
# Verificar librerías instaladas
composer show | findstr /i "excel dompdf"

# Debería mostrar:
# barryvdh/laravel-dompdf
# dompdf/dompdf
# maatwebsite/excel
```

---

## ✅ Checklist

- [ ] Servidor corriendo (`php artisan serve`)
- [ ] Navegador abierto
- [ ] Probé `/test-export.html`
- [ ] Probé `/test/pdf/1`
- [ ] Probé `/test/excel/1`
- [ ] PDF se descargó
- [ ] Excel se descargó
- [ ] Archivos se abren correctamente
- [ ] UTF-8 funciona

---

## 🎉 Resultado Esperado

### PDF
- Archivo: `calificaciones_prueba_2024-12-05.pdf`
- Tamaño: ~50-100 KB
- Contenido: Tabla con 3 estudiantes y 5 criterios

### Excel
- Archivo: `calificaciones_prueba_2024-12-05.xlsx`
- Tamaño: ~10-20 KB
- Contenido: Hoja con datos completos

---

**Fecha**: Diciembre 5, 2024
**Estado**: ✅ LISTO PARA PROBAR
**Versión**: 1.0.2
