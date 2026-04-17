# Guardado por Pasos - Arquitectura

## 🔄 Flujo de Guardado

```
Paso 1: Datos Iniciales
├── POST /api/visits
│   └── Crea visita en BigQuery
│   └── Retorna: visit_id
│
Pasos 2-9: Actualizaciones
├── PATCH /api/visits/{visit_id}
│   └── Actualiza visita en BigQuery
│   └── Retorna: success
```

## 📊 Tabla en BigQuery

**Tabla:** `adoc-bi-prd.OPB.gerente_pais`

### Estructura base (Paso 1)
```
id_visita: STRING          ← Generado por el sistema
country: STRING            ← País seleccionado
format: STRING             ← Formato (ADOC, PAR2, etc)
store: STRING              ← Tienda seleccionada
visit_email: STRING        ← Email del evaluador
start_datetime: DATETIME   ← Cuando inicia
ip_address: STRING         ← IP del usuario
bv_pais: STRING            ← Abreviatura (SV, GT, etc)
id_sugar_tienda: STRING    ← ID de tienda
store_email: STRING        ← Email de tienda (desde BQ master)
lider_zona: STRING         ← Email del líder de zona
ubicacion: STRING          ← Ubicación de tienda
```

### Preguntas (Pasos 2-9)
```
preg_01, preg_03, preg_05, ... preg_112: STRING
obs_01, obs_02, ..., obs_07: STRING
lat, lon: FLOAT
end_datetime: DATETIME
```

## 🔐 Proceso de Actualización

### Frontend (React)

```typescript
// Cuando completa Paso 1
const { id_visita } = await visitService.createVisit({
  country: 'El Salvador',
  format: 'ADOC',
  store: 'H85',
  visit_email: 'eval@correo.com',
  bv_pais: 'SV',
  id_sugar_tienda: 'SVH85'
});

// Cuando avanza a Pasos 2-9
await visitService.updateVisit(id_visita, {
  preg_01: 'Respuesta paso 2',
  // ... más campos
});
```

### Backend (Laravel)

```php
// POST /api/visits - Crea nueva fila
$data = [
  'id_visita' => 'visit_' . uniqid(),
  'country' => $request->country,
  // ... más datos
  'start_datetime' => date('Y-m-d H:i:s'),
];
$bigQueryService->saveVisit($data);

// PATCH /api/visits/{id} - Actualiza fila existente
$bigQueryService->updateVisit($id, [
  'preg_01' => 'respuesta',
  'end_datetime' => date('Y-m-d H:i:s'),
]);
```

## ⚠️ Consideraciones Importantes

### 1. BigQuery Queries
- Usa **MERGE** para actualizaciones
- Usa **INSERT** para nuevas filas
- Implementa retry logic (BigQuery puede ser eventual)

### 2. Validación de IDs
```typescript
// El ID se genera en backend
// Formato: visit_1234567890
// Debe ser único y fácil de trackear
```

### 3. Transacciones
BigQuery no tiene transacciones estrictas como MySQL:
- Si falla la actualización, el usuario puede reintentar
- Los datos parciales se guardan correctamente

### 4. Concurrencia
Si dos actualizaciones ocurren simultáneamente:
- La última ganará (MERGE actualizará)
- No hay conflictos de actualización

## 📈 Ejemplo Real de Flujo

```
1. Usuario abre formulario
   → Paso 1 se muestra

2. Usuario completa Paso 1 (país, formato, tienda, email)
   → POST /api/visits
   → Se crea fila en BigQuery
   → Backend retorna: { visit_id: "visit_abc123" }
   → Frontend guarda visit_id en localStorage

3. Usuario hace clic "Siguiente"
   → Paso 2 se muestra

4. Usuario completa Paso 2 (preguntas varias)
   → Click "Siguiente"
   → PATCH /api/visits/visit_abc123 { preg_01, preg_03, ... }
   → Se actualiza fila en BigQuery
   → Paso 3 se muestra

5. ... pasos 3-9 repiten paso 4 ...

6. Usuario llega a Paso 9 (final)
   → Completa respuestas
   → Click "Completar"
   → PATCH /api/visits/visit_abc123 { preg_..., end_datetime }
   → Se marca como completa en BigQuery
   → Mostrar mensaje de éxito

7. Datos completos en BigQuery:
   ├── id_visita: "visit_abc123"
   ├── start_datetime: "2026-04-16 14:30:00"
   ├── end_datetime: "2026-04-16 16:45:00"
   ├── preg_01: "Respuesta"
   ├── preg_03: "Respuesta"
   ├── ... todos los campos completados
   └── obs_01: "Observación"
```

## 🔧 Agregar Nuevo Campo

1. **BigQuery:** El campo debe estar en el esquema de `gerente_pais`
2. **Backend:** Agregar en `getAllowedFields()` en `VisitController.php`
3. **Frontend:** Agregar tipo en `types/index.ts` y en el componente correspondiente

## 📊 Monitoreo

Para ver los datos guardados en BigQuery:

```sql
SELECT 
  id_visita,
  country,
  format,
  store,
  visit_email,
  start_datetime,
  end_datetime,
  TIMESTAMP_DIFF(end_datetime, start_datetime, MINUTE) as duracion_minutos
FROM `adoc-bi-prd.OPB.gerente_pais`
WHERE DATE(start_datetime) = CURRENT_DATE()
ORDER BY start_datetime DESC
```

---

**Última actualización:** 2026-04-16
