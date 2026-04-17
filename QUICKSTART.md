# 🚀 Inicio Rápido - OPB Visitas

## 🐳 Con Docker (Recomendado)

```bash
# 1. Ir al directorio del proyecto
cd /c/opb_base

# 2. Iniciar todos los servicios
docker-compose up -d --build

# 3. Esperar a que se inicialicen (30 segundos)
sleep 30

# 4. Verificar estado
docker-compose ps
```

**Acceso inmediato:**
- Frontend: http://localhost:5173
- Backend: http://localhost:8000
- Health Check: http://localhost:8000/health

**Ver logs:**
```bash
docker-compose logs -f backend  # Backend logs
docker-compose logs -f frontend # Frontend logs
```

**Detener:**
```bash
docker-compose down
```

---

## 💻 Sin Docker (Desarrollo Local)

### Terminal 1 - Backend (Laravel)
```bash
cd /c/opb_base/backend
php artisan serve
# Server: http://localhost:8000
```

### Terminal 2 - Frontend (React)
```bash
cd /c/opb_base/frontend
npm install  # Primera vez solamente
npm run dev
# Dev server: http://localhost:5173
```

---

## ✅ Requisitos Previos

- [x] PHP 8.3+ instalado
- [x] Node.js 20+ instalado
- [x] Composer instalado
- [x] Docker Desktop (para opción Docker)
- [x] `credenciales_bq.json` en `backend/`
- [x] Git configurado

---

## 📱 Acceso a la Aplicación

| Componente | URL | Puerto |
|-----------|-----|--------|
| Frontend (Aplicación) | http://localhost:5173 | 5173 |
| Backend (API) | http://localhost:8000 | 8000 |
| API Base | http://localhost:8000/api | - |
| Health Check | http://localhost:8000/health | - |

---

## 🧪 Pruebas Rápidas

### 1. Verificar API está funcionando
```bash
curl http://localhost:8000/health
# Esperado: {"status":"ok"}
```

### 2. Obtener lista de países
```bash
curl http://localhost:8000/api/countries
# Esperado: {"success":true,"data":[...]}
```

### 3. Abrir en navegador
```bash
# Frontend
open http://localhost:5173

# O en Windows
start http://localhost:5173
```

---

## 📊 Estructura de Pasos

El formulario tiene **9 pasos**:

| Paso | Contenido | Preguntas |
|------|-----------|-----------|
| 1 | País, Tienda, Formato, Email | - |
| 2 | Personal & Uniforme | 2 |
| 3 | Servicio al Cliente | 3 |
| 4 | Exhibición de Producto | 31 |
| 5 | Infraestructura | 25 |
| 6 | Bodega | 3 |
| 7 | Conocimiento ADOCKERS | 1 |
| 8 | Tecnología | 0 |
| 9 | Observaciones & Plan de Acción | - |

**Total**: 65 preguntas evaluadas

---

## 🔄 Flujo Típico de Desarrollo

### Primera vez
```bash
# 1. Clone repository
git clone <repo>

# 2. Entrar al directorio
cd opb-base

# 3. Docker setup
docker-compose up -d --build

# 4. Verificar salud
curl http://localhost:8000/health

# 5. Abrir navegador
http://localhost:5173
```

### Desarrollo continuo
```bash
# Mientras trabajas, los cambios se recargan automáticamente
# Frontend: Vite hot reload
# Backend: Laravel artisan serve auto-reload

# Ver cambios en tiempo real
docker-compose logs -f

# Si necesitas reiniciar un servicio
docker-compose restart backend
docker-compose restart frontend
```

### Después de cambios en dependencias
```bash
# Backend (composer.json)
docker-compose exec backend composer install
docker-compose restart backend

# Frontend (package.json)
docker-compose exec frontend npm install
docker-compose restart frontend
```

---

## 🐛 Problemas Comunes

### ❌ "Port 8000 already in use"
```bash
# Encuentra proceso usando puerto
lsof -i :8000

# Mata el proceso
kill -9 <PID>

# O en docker-compose, resetea todo
docker-compose down
docker system prune
```

### ❌ "Cannot connect to API"
```bash
# Verifica backend está corriendo
docker-compose ps
# Debería mostrar: backend - Up

# Mira logs del backend
docker-compose logs backend

# Reinicia backend
docker-compose restart backend
```

### ❌ "Frontend no actualiza"
```bash
# Limpia node_modules
docker-compose exec frontend rm -rf node_modules
docker-compose exec frontend npm install
docker-compose restart frontend
```

### ❌ "BigQuery connection error"
```bash
# Verifica credenciales existen
ls -la backend/credenciales_bq.json

# Verifica contenido es JSON válido
cat backend/credenciales_bq.json | jq .

# Verifica permisos en Google Cloud
gcloud projects get-iam-policy adoc-bi-prd
```

---

## 📚 Documentación Completa

- **README.md** - Descripción general y guía completa
- **DOCKER.md** - Guía detallada de Docker
- **ARCHITECTURE.md** - Decisiones arquitectónicas

---

## 🎯 Próximos Pasos

1. ✅ Backend funcionando
2. ✅ Frontend funcionando
3. ✅ Steps 2-9 implementados
4. ⏳ Conexión con ADOC (CRM)
5. ⏳ Testing y QA
6. ⏳ Deploy a Cloud Run

---

**Última actualización**: 2026-04-16  
**Versión**: 1.0.0
