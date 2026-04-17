# OPB Visitas - Docker Setup Guide

## Project Structure

```
opb-base/
├── backend/              # Laravel API application
│   ├── app/             # Application code
│   ├── config/          # Configuration files
│   ├── routes/          # API routes
│   ├── public/          # Public assets
│   ├── storage/         # Logs and cache
│   ├── bootstrap/       # Framework bootstrap
│   ├── Dockerfile       # Backend image definition
│   ├── artisan          # Laravel CLI
│   ├── composer.json    # PHP dependencies
│   └── .env             # Backend environment
├── frontend/            # React + Vite application
│   ├── src/            # React components and hooks
│   ├── public/         # Static assets
│   ├── Dockerfile      # Frontend image definition
│   ├── package.json    # Node dependencies
│   ├── vite.config.ts  # Vite configuration
│   └── tsconfig.json   # TypeScript configuration
├── docker-compose.yml   # Multi-service orchestration
├── .env                # Root environment (optional)
└── .gitignore          # Git ignore patterns
```

## Prerequisites

- Docker Desktop installed (includes Docker and docker-compose)
- Git for version control

## Quick Start

### 1. Start all services with Docker Compose

```bash
# From the root directory (opb-base/)
docker-compose up -d --build

# View logs
docker-compose logs -f

# Stop services
docker-compose down
```

### 2. Access applications

- **Backend API**: http://localhost:8000
  - Health check: http://localhost:8000/health
  - Countries: http://localhost:8000/api/countries
  
- **Frontend**: http://localhost:5173

### 3. Verify services are running

```bash
# Check service status
docker-compose ps

# View backend logs
docker-compose logs backend

# View frontend logs
docker-compose logs frontend
```

## Development without Docker

### Backend (Terminal 1)

```bash
cd backend
php artisan serve
# Server runs on http://localhost:8000
```

### Frontend (Terminal 2)

```bash
cd frontend
npm install  # if first time
npm run dev
# Dev server runs on http://localhost:5173
```

## Environment Variables

### Backend (.env)
```
APP_NAME=OPB-Visitas
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_CONNECTION=bigquery
BIGQUERY_PROJECT_ID=adoc-bi-prd
BIGQUERY_KEY_FILE=/path/to/credentials.json
SESSION_DRIVER=file
CACHE_DRIVER=file
LOG_CHANNEL=stack
```

### Frontend (.env.local)
```
VITE_API_URL=http://localhost:8000/api
```

## Docker Commands

### Build images
```bash
docker-compose build
```

### View logs
```bash
docker-compose logs backend
docker-compose logs frontend
docker-compose logs -f  # follow mode
```

### Execute commands in containers
```bash
# Run artisan commands
docker-compose exec backend php artisan tinker

# Run npm commands
docker-compose exec frontend npm install
```

### Clean up
```bash
# Stop and remove containers
docker-compose down

# Remove unused images
docker image prune

# Full cleanup (WARNING: removes all images/volumes)
docker-compose down -v
```

## Deployment to Cloud Run

The Dockerfiles are optimized for Google Cloud Run:

### Backend
- Uses PHP 8.3-FPM
- Listens on port 8000
- Includes health check endpoint
- Environment variables: APP_URL, DB_CONNECTION, BIGQUERY_PROJECT_ID

### Frontend
- Multi-stage build (reduces image size)
- Node.js 20-alpine base image
- Vite dev server on port 5173
- Environment: VITE_API_URL pointing to backend

### Deploy to Cloud Run

```bash
# Build and push backend
gcloud builds submit backend --tag gcr.io/PROJECT_ID/opb-backend
gcloud run deploy opb-backend --image gcr.io/PROJECT_ID/opb-backend \
  --platform managed \
  --region us-central1 \
  --allow-unauthenticated \
  --set-env-vars="BIGQUERY_PROJECT_ID=adoc-bi-prd"

# Build and push frontend
gcloud builds submit frontend --tag gcr.io/PROJECT_ID/opb-frontend
gcloud run deploy opb-frontend --image gcr.io/PROJECT_ID/opb-frontend \
  --platform managed \
  --region us-central1 \
  --allow-unauthenticated \
  --set-env-vars="VITE_API_URL=https://opb-backend-xxxxx.a.run.app/api"
```

## Troubleshooting

### Port already in use
```bash
# Find and kill process using port
lsof -i :8000  # or :5173
kill -9 <PID>
```

### Permission denied in storage
```bash
docker-compose exec backend chmod -R 777 storage bootstrap/cache
```

### Clear cache/logs
```bash
docker-compose exec backend rm -rf storage/logs/* storage/framework/cache/data/*
```

### Rebuild without cache
```bash
docker-compose build --no-cache
```

## Architecture

```
┌─────────────────────────────────────────────────┐
│          Browser (localhost:5173)               │
│         React + TypeScript Frontend             │
└────────────────┬────────────────────────────────┘
                 │ HTTP/API (localhost:8000/api)
┌────────────────▼────────────────────────────────┐
│       Laravel API (localhost:8000)              │
│    RESTful Backend with BigQuery Service        │
└────────────────┬────────────────────────────────┘
                 │ Google Cloud Client
┌────────────────▼────────────────────────────────┐
│        Google Cloud BigQuery                    │
│      (Production: adoc-bi-prd)                  │
└─────────────────────────────────────────────────┘
```

## Next Steps

1. Map all 112 survey questions to Steps 2-9
2. Implement React components for each step
3. Test complete form flow with real BigQuery data
4. Set up CI/CD pipeline for automated deployment
5. Configure Cloud Run secrets for BigQuery credentials
