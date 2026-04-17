# OPB Visitas - Sistema de Control de Visitas del Gerente País

## 📋 Descripción General

**Control de Visitas Gerente País** es una aplicación web moderna para registrar y evaluar visitas a tiendas ADOC. Implementada con arquitectura de microservicios containerizados.

### Stack Tecnológico
- **Backend**: Laravel 12 + PHP 8.3 (API RESTful)
- **Frontend**: React 18 + TypeScript (Vite)
- **Base de datos**: Google Cloud BigQuery
- **Deployment**: Docker & Google Cloud Run

### Características Principales
- ✅ **9 pasos** de formulario con auto-guardado (POST en Step 1, PATCH en Steps 2-9)
- ✅ **65 preguntas de evaluación** distribuidas en 7 áreas de inspección
- ✅ **Filtrado dinámico** por formato: ADOC, PAR2, HP, CAT, TNF, CG, Vans
- ✅ **Observaciones y Plan de Acción**: Step 9 para análisis y seguimiento
- ✅ **Diseño responsive**: Optimizado para mobile, tablet, desktop e iPad
- ✅ **Branding institucional**: Colores amarillo (#ffc300) y gris (#444)
- ✅ **BigQuery enterprise**: Almacenamiento seguro con autenticación por credenciales

## 🚀 Inicio Rápido

### Opción 1: Docker Compose (Recomendado)

```bash
# Desde la raíz del proyecto
docker-compose up -d --build

# Ver logs
docker-compose logs -f

# Detener servicios
docker-compose down
```

**Acceso inmediato:**
- **Frontend**: http://localhost:5173
- **Backend**: http://localhost:8000  
- **Health Check**: http://localhost:8000/health

### Opción 2: Desarrollo Local (Sin Docker)

**Terminal 1 - Backend:**
```bash
cd backend
php artisan serve
# Server: http://localhost:8000
```

**Terminal 2 - Frontend:**
```bash
cd frontend
npm install  # primera vez solamente
npm run dev
# Dev server: http://localhost:5173
```## 📁 Estructura del Proyecto

```
opb-base/
├── backend/                     # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   └── VisitController.php     # Endpoints API
│   │   └── Services/
│   │       └── BigQueryService.php     # BigQuery operations
│   ├── config/                  # Laravel config
│   ├── routes/
│   │   ├── api.php             # API routes (/api/*)
│   │   └── web.php             # Web routes
│   ├── bootstrap/              # Framework bootstrap
│   ├── public/index.php        # Web entry point
│   ├── .env                    # Backend environment
│   ├── artisan                 # Laravel CLI
│   ├── composer.json           # PHP dependencies
│   ├── composer.lock
│   └── Dockerfile              # Docker image definition
│
├── frontend/                    # React + Vite
│   ├── src/
│   │   ├── components/
│   │   │   ├── FormContainer.tsx       # Main orchestrator
│   │   │   ├── Step1.tsx               # Country/store/email selection
│   │   │   ├── StepForm.tsx            # Generic Steps 2-8 handler
│   │   │   ├── Step9.tsx               # Final observations & action plan
│   │   │   ├── QuestionCard.tsx        # Question component
│   │   │   ├── Header.tsx
│   │   │   ├── ProgressBar.tsx
│   │   │   └── ...other components
│   │   ├── hooks/
│   │   │   └── useVisitForm.ts         # State management hook
│   │   ├── utils/
│   │   │   └── questionsUtil.ts        # Question utilities
│   │   ├── services/
│   │   │   └── api.ts                  # HTTP client
│   │   ├── types/
│   │   │   └── index.ts                # TypeScript interfaces
│   │   └── main.tsx
│   ├── data/                   # (symlink from /data)
│   │   └── questions.json              # 112 survey questions
│   ├── public/                 # Static files
│   ├── index.html             # HTML entry
│   ├── tailwind.config.ts     # Theme & branding
│   ├── vite.config.ts         # Vite configuration
│   ├── tsconfig.json          # TypeScript config
│   ├── package.json           # Node dependencies
│   ├── .env.local             # Frontend environment
│   └── Dockerfile              # Docker image
│
├── data/
│   └── questions.json          # Base de preguntas (65 included)
│
├── docker-compose.yml          # Service orchestration
├── DOCKER.md                   # Docker detailed guide
├── README.md                   # This file
└── .gitignore                  # Git ignore rules
```

## 📊 Distribución de Preguntas

| Step | Área | Preguntas | Tema |
|------|------|-----------|------|
| 1 | Configuración | - | País, Tienda, Formato, Email |
| 2 | Área 1 | 2 | Personal & Uniforme |
| 3 | Área 2 | 3 | Servicio al Cliente |
| 4 | Área 3 | 31 | Exhibición de Producto |
| 5 | Área 4 | 25 | Infraestructura & Ambiente |
| 6 | Área 5 | 3 | Bodega |
| 7 | Área 6 | 1 | Conocimiento ADOCKERS |
| 8 | Área 7 | 0 | Tecnología (summary) |
| 9 | Final | - | Observaciones & Plan de Acción |

**Total**: 65 preguntas evaluadas + observaciones finales

## 📡 API Endpoints

### Step 1 - Crear Visita (POST)

```bash
POST /api/visits
Content-Type: application/json

{
  "country": "El Salvador",
  "format": "adoc",
  "store": "TiendaX",
  "visit_email": "evaluador@adockers.com",
  "bv_pais": "SV",
  "id_sugar_tienda": "12345",
  "store_email": "tienda@adockers.com",
  "ubicacion": "San Salvador"
}

Response 201:
{
  "success": true,
  "id_visita": "uuid-xxxxx",
  "created_at": "2026-04-16T..."
}
```

### Steps 2-9 - Actualizar Visita (PATCH)

```bash
PATCH /api/visits/{id_visita}
Content-Type: application/json

{
  "pregunta_1": true,
  "pregunta_3": true,
  "pregunta_5": "Observación de texto",
  "pregunta_21": false,
  "observations": "Resumen de observaciones generales...",
  "actionPlan": "Plan de mejora específico..."
}

Response 200:
{
  "success": true,
  "updated_at": "2026-04-16T..."
}
```

### Utilidades

```bash
# Health check
GET /health
Response: { "status": "ok" }

# Obtener países
GET /api/countries
Response: { "data": [{ "code": "SV", "country": "El Salvador" }, ...] }

# Obtener tiendas
GET /api/stores/{country}
Response: { "data": [{ "nombre": "H85", "ubicacion": "...", ... }, ...] }

# Obtener visita
GET /api/visits/{id_visita}
Response: { "data": { "id_visita": "...", "country": "...", ... } }
```

## 🔐 Variables de Entorno

### Backend (`backend/.env`)

```ini
APP_NAME=OPB-Visitas
APP_ENV=local                              # local|production
APP_DEBUG=true                             # true|false
APP_KEY=base64:1wbi4QnUVodcyH+C9D2K...    # Generado
APP_URL=http://localhost:8000

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug

# Database (BigQuery)
DB_CONNECTION=bigquery
BIGQUERY_PROJECT_ID=adoc-bi-prd
BIGQUERY_CREDENTIALS_PATH=credenciales_bq.json

# Cache & Sessions
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

# CORS
FRONTEND_URL=http://localhost:5173
SESSION_DOMAIN=localhost
SESSION_SECURE_COOKIES=false
```

### Frontend (`frontend/.env.local`)

```ini
VITE_API_URL=http://localhost:8000/api
```

## 🎨 Branding & Customization

### Colors

Located in `frontend/tailwind.config.ts`:

```typescript
colors: {
  brand: {
    yellow: '#ffc300',  // Primary
    dark: '#444',       // Secondary
  },
  success: '#22c55e',
  error: '#ef4444',
  warning: '#f59e0b'
}
```

### Typography

- **Headlines**: Font-bold sizes 2xl-3xl
- **Body**: Regular sizes sm-base
- **Accent**: Yellow (#ffc300) for highlights

## 🚢 Deployment

### Docker Compose (Local Development)

```bash
# Start all services
docker-compose up -d --build

# View logs
docker-compose logs -f backend
docker-compose logs -f frontend

# Stop services
docker-compose down

# Clean everything
docker-compose down -v
```

### Google Cloud Run

**Step 1: Build & Push Images**

```bash
# Configure gcloud
gcloud config set project adoc-bi-prd
gcloud auth login

# Backend
gcloud builds submit backend \
  --tag gcr.io/adoc-bi-prd/opb-backend:latest

# Frontend
gcloud builds submit frontend \
  --tag gcr.io/adoc-bi-prd/opb-frontend:latest
```

**Step 2: Deploy Services**

```bash
# Backend
gcloud run deploy opb-backend \
  --image gcr.io/adoc-bi-prd/opb-backend:latest \
  --platform managed \
  --region us-central1 \
  --memory 512Mi \
  --port 8000 \
  --set-env-vars "BIGQUERY_PROJECT_ID=adoc-bi-prd,APP_URL=https://opb-backend-xxx.a.run.app"

# Frontend
gcloud run deploy opb-frontend \
  --image gcr.io/adoc-bi-prd/opb-frontend:latest \
  --platform managed \
  --region us-central1 \
  --memory 256Mi \
  --port 5173 \
  --set-env-vars "VITE_API_URL=https://opb-backend-xxx.a.run.app/api"
```

## 🛠️ Development Guide

### Adding a New Step

1. **Create component** `frontend/src/components/Step{N}.tsx`
2. **Import** in `FormContainer.tsx`
3. **Add to render** logic with step mapping
4. **Update API** endpoints if needed
5. **Test** full flow with docker-compose

### Adding Questions

1. **Update** `data/questions.json`
2. Questions auto-filter by:
   - `incluir === '1'`
   - Format field (adoc, par2, hp, cat, tnf, cg, Vans)
   - Area (id_area)
3. **Reload** frontend to see new questions

### BigQuery Schema

Expected tables in project `adoc-bi-prd`:

```sql
-- Main visits table
CREATE TABLE visits (
  id_visita STRING PRIMARY KEY,
  country STRING,
  format STRING,
  store STRING,
  visit_email STRING,
  bv_pais STRING,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
)

-- Response data
CREATE TABLE visit_responses (
  id STRING PRIMARY KEY,
  id_visita STRING,
  pregunta_id STRING,
  respuesta STRING,
  created_at TIMESTAMP
)

-- Metadata
CREATE TABLE dim_area (
  id_area INTEGER,
  nombre STRING,
  descripcion STRING
)
```

## 🧪 Testing

### Frontend Tests (Future)
```bash
cd frontend
npm run test
npm run test:coverage
```

### Backend Tests (Future)
```bash
cd backend
php artisan test
```

### Manual Testing Checklist
- [ ] Step 1: Country/store selection
- [ ] Step 2-8: Question answering
- [ ] Step 9: Observations & action plan
- [ ] Format filtering works correctly
- [ ] All saves succeed
- [ ] Response persists on page reload
- [ ] Mobile layout responsive

## 📱 Browser Support

- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## 🆘 Troubleshooting

### Docker Issues

```bash
# Port already in use
lsof -i :8000  # Find process
kill -9 <PID>  # Kill it

# Rebuild without cache
docker-compose build --no-cache

# Reset everything
docker-compose down -v
docker system prune
```

### BigQuery Connection Failed

```bash
# Check credentials file
file backend/credenciales_bq.json
cat backend/credenciales_bq.json | jq . 

# Verify permissions
gcloud projects get-iam-policy adoc-bi-prd

# Test connection
cd backend
php artisan tinker
# >>> $service = new \App\Services\BigQueryService(); // Test
```

### Frontend Not Connecting to API

```bash
# Check backend is running
curl http://localhost:8000/health

# Check CORS is configured
curl -i -X OPTIONS http://localhost:8000/api/visits

# Check .env.local in frontend
cat frontend/.env.local
```

### Build Failures

```bash
# Frontend
cd frontend
rm -rf node_modules dist
npm ci  # Clean install
npm run build

# Backend
cd backend
composer install --no-interaction
php artisan migrate
```

## 📞 Support & Documentation

- **Docker Guide**: See `DOCKER.md`
- **Architecture**: See `ARCHITECTURE.md`
- **API Docs**: See endpoints above
- **Questions Mapping**: See `data/questions.json`

---

**Last Updated**: 2026-04-16  
**Version**: 1.0.0  
**Status**: ✅ Production Ready
