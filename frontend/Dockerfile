# Frontend: React + Vite Application
# Build stage
FROM node:20-alpine AS builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci

# Copy source code
COPY . .

# Build application
RUN npm run build

# Production stage
FROM node:20-alpine

WORKDIR /app

# Install serve for development/testing
RUN npm install -g serve

# Copy built files from builder
COPY --from=builder /app/dist /app/dist
COPY --from=builder /app/package*.json ./

# Install only production dependencies
RUN npm ci --omit=dev

# Expose port
EXPOSE 5173

# Start development server for Cloud Run
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "5173"]
