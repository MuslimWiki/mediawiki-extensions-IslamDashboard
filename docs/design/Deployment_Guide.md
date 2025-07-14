# Deployment and Upgrade Guide

## Version: 0.3.1

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Containerization](#containerization)
3. [Installation](#installation)
4. [Upgrade Process](#upgrade-process)
5. [Database Migrations](#database-migrations)
6. [Configuration Management](#configuration-management)
7. [Rollback Procedures](#rollback-procedures)
8. [Monitoring](#monitoring)
9. [Troubleshooting](#troubleshooting)

## Prerequisites

### Server Requirements
- PHP 7.4-8.1
- MediaWiki 1.43+
- MySQL 5.7+ / MariaDB 10.3+ / PostgreSQL 10+
- Composer 2.0+
- Node.js 16+ (for building assets)

### Permissions
- Web server write access to:
  - `extensions/IslamDashboard/`
  - `cache/`
  - `images/` (if storing files)

## Containerization

### Docker Setup

#### 1. Dockerfile
```dockerfile
# Base image with PHP and required extensions
FROM mediawiki:1.43

# Install system dependencies
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    git \
    unzip \
    nodejs \
    npm && \
    rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-enable opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html/extensions/IslamDashboard

# Copy extension files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install and build frontend assets
RUN npm install && \
    npm run build && \
    npm cache clean --force

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/healthz || exit 1
```

#### 2. Docker Compose
```yaml
version: '3.8'

services:
  mediawiki:
    build: .
    ports:
      - "8080:80"
    volumes:
      - mediawiki-images:/var/www/html/images
      - mediawiki-skins:/var/www/html/skins
      - mediawiki-extensions:/var/www/html/extensions
    environment:
      - MEDIAWIKI_SERVER=http://localhost:8080
      - MEDIAWIKI_DB_HOST=db
      - MEDIAWIKI_DB_USER=wikiuser
      - MEDIAWIKI_DB_PASSWORD=wikipass
      - MEDIAWIKI_DB_NAME=wikidb
    depends_on:
      - db
    restart: unless-stopped

  db:
    image: mariadb:10.6
    environment:
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: wikidb
      MYSQL_USER: wikiuser
      MYSQL_PASSWORD: wikipass
    volumes:
      - db-data:/var/lib/mysql
    restart: unless-stopped

  redis:
    image: redis:alpine
    command: redis-server --requirepass your_redis_password
    ports:
      - "6379:6379"
    volumes:
      - redis-data:/data
    restart: unless-stopped

volumes:
  mediawiki-images:
  mediawiki-skins:
  mediawiki-extensions:
  db-data:
  redis-data:
```

### Kubernetes Deployment

#### 1. Deployment
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: islamdashboard
  labels:
    app: islamdashboard
spec:
  replicas: 3
  selector:
    matchLabels:
      app: islamdashboard
  template:
    metadata:
      labels:
        app: islamdashboard
    spec:
      containers:
      - name: islamdashboard
        image: your-registry/islamdashboard:1.0.0
        ports:
        - containerPort: 80
        envFrom:
        - configMapRef:
            name: islamdashboard-config
        - secretRef:
            name: islamdashboard-secrets
        resources:
          requests:
            cpu: "100m"
            memory: "256Mi"
          limits:
            cpu: "500m"
            memory: "512Mi"
        livenessProbe:
          httpGet:
            path: /healthz
            port: 80
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /readiness
            port: 80
          initialDelaySeconds: 5
          periodSeconds: 5
```

#### 2. Service
```yaml
apiVersion: v1
kind: Service
metadata:
  name: islamdashboard-service
spec:
  selector:
    app: islamdashboard
  ports:
    - protocol: TCP
      port: 80
      targetPort: 80
  type: ClusterIP
```

#### 3. Horizontal Pod Autoscaler
```yaml
apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: islamdashboard-hpa
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: islamdashboard
  minReplicas: 2
  maxReplicas: 10
  metrics:
  - type: Resource
    resource:
      name: cpu
      target:
        type: Utilization
        averageUtilization: 70
```

### CI/CD Pipeline

#### 1. GitHub Actions Workflow
```yaml
name: Build and Deploy

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  build:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:5.7
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: test
        ports:
          - 3306:3306
        options: >-
          --health-cmd "mysqladmin ping"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 3

    steps:
    - uses: actions/checkout@v3
    
    - name: Set up Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '16'
        
    - name: Set up PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, xml, json, intl, gd, pdo_mysql
        
    - name: Install dependencies
      run: |
        composer install --no-interaction --prefer-dist --optimize-autoloader
        npm ci
        
    - name: Run tests
      run: |
        php vendor/bin/phpunit
        npm test
        
    - name: Build assets
      run: npm run build
      
    - name: Login to Docker Hub
      if: github.ref == 'refs/heads/main'
      uses: docker/login-action@v2
      with:
        username: ${{ secrets.DOCKER_HUB_USERNAME }}
        password: ${{ secrets.DOCKER_HUB_TOKEN }}
        
    - name: Build and push Docker image
      if: github.ref == 'refs/heads/main'
      uses: docker/build-push-action@v3
      with:
        context: .
        push: true
        tags: yourusername/islamdashboard:latest,yourusername/islamdashboard:${{ github.sha }}
        
    - name: Deploy to Kubernetes
      if: github.ref == 'refs/heads/main'
      uses: azure/k8s-deploy@v4
      with:
        namespace: production
        manifests: |
          k8s/deployment.yaml
          k8s/service.yaml
          k8s/hpa.yaml
        images: |
          yourusername/islamdashboard:${{ github.sha }}
        imagepullsecrets: |
          docker-registry-credentials
```

### Local Development with Docker

#### 1. Development Environment
```yaml
# docker-compose.dev.yml
version: '3.8'

services:
  app:
    build:
      context: .
      target: development
    volumes:
      - .:/var/www/html/extensions/IslamDashboard
      - /var/www/html/extensions/IslamDashboard/node_modules
    ports:
      - "8080:80"
    environment:
      - XDEBUG_MODE=debug
      - XDEBUG_CONFIG="client_host=host.docker.internal"
    depends_on:
      - db

  db:
    image: mariadb:10.6
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: wikidb
      MYSQL_USER: wikiuser
      MYSQL_PASSWORD: wikipass
    ports:
      - "3306:3306"
    volumes:
      - db-data:/var/lib/mysql

  adminer:
    image: adminer
    restart: always
    ports:
      - 8081:8080

volumes:
  db-data:
```

#### 2. Development Commands
```bash
# Start development environment
docker-compose -f docker-compose.dev.yml up -d

# Run tests
docker-compose -f docker-compose.dev.yml exec app composer test
docker-compose -f docker-compose.dev.yml exec app npm test

# Debug with Xdebug
# Configure your IDE to listen on port 9003
```

## Installation

### 1. Install the Extension
```bash
# Clone the repository
git clone https://github.com/your-repo/IslamDashboard.git /path/to/w/extensions/IslamDashboard

# Install PHP dependencies
cd /path/to/w/extensions/IslamDashboard
composer install --no-dev

# Build frontend assets
npm install
npm run build
```

### 2. Enable the Extension
Add to `LocalSettings.php`:
```php
wfLoadExtension( 'IslamDashboard' );

// Recommended settings
$wgIslamDashboardEnableFeatureX = true;
$wgIslamDashboardApiRateLimit = 100;
```

### 3. Run Database Updates
```bash
php maintenance/update.php
```

## Upgrade Process

### 1. Pre-Upgrade Checklist
- [ ] Backup database
- [ ] Backup `LocalSettings.php`
- [ ] Check extension compatibility
- [ ] Notify users (if needed)

### 2. Upgrade Steps
```bash
# Pull latest changes
cd /path/to/w/extensions/IslamDashboard
git pull

# Update dependencies
composer install --no-dev
npm install
npm run build

# Run database updates
cd /path/to/w
php maintenance/update.php

# Clear caches
php maintenance/rebuildLocalisationCache.php
php maintenance/runJobs.php
```

## Database Migrations

### Migration Files
Store in `sql/` with naming convention:
`patches/patch-<timestamp>_<description>.sql`

### Example Migration
```sql
-- patches/patch-20230714_add_widget_prefs.sql
CREATE TABLE IF NOT EXISTS /*_*/islamdashboard_user_prefs (
    up_user INT UNSIGNED NOT NULL,
    up_widget_id VARBINARY(255) NOT NULL,
    up_config BLOB,
    up_timestamp BINARY(14) NOT NULL DEFAULT '',
    PRIMARY KEY (up_user, up_widget_id)
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/up_user ON /*_*/islamdashboard_user_prefs (up_user);
```

### Running Migrations
```php
// In maintenance script
$updater = DatabaseUpdater::newForDb( $db, $shared, $maintenance );
$updater->addExtensionUpdate( [ 'addTable', 'islamdashboard_user_prefs', 
    __DIR__ . '/sql/patches/patch-20230714_add_widget_prefs.sql', true ] );
```

## Configuration Management

### Environment Variables
```bash
# .env
ISLAM_DASHBOARD_API_KEY=your-api-key
DEBUG_MODE=false
```

### Configuration Files
- `extension.json`: Default settings
- `LocalSettings.php`: Override defaults
- `.env`: Sensitive/instance-specific settings

## Rollback Procedures

### Code Rollback
```bash
# Revert to previous version
cd /path/to/w/extensions/IslamDashboard
git checkout tags/v0.3.0

# Rebuild assets
npm run build

# Run database rollback if needed
php maintenance/update.php
```

### Database Rollback
```sql
-- Example rollback for a table creation
DROP TABLE IF EXISTS /*_*/islamdashboard_user_prefs;
```

## Monitoring

### Key Metrics
- Dashboard load time
- API response times
- Error rates
- Database query performance

### Logging
```php
// Log messages
wfDebugLog( 'IslamDashboard', 'Widget loaded: ' . $widgetId );

// Error handling
try {
    // Risky operation
} catch ( Exception $e ) {
    wfLogWarning( 'Dashboard error: ' . $e->getMessage() );
}
```

## Troubleshooting

### Common Issues

#### Missing Dependencies
```bash
# Check installed dependencies
composer show | grep required
npm list

# Reinstall if needed
rm -rf vendor/
composer install --no-dev
```

#### JavaScript Errors
1. Clear browser cache
2. Check browser console
3. Verify asset paths
4. Check for conflicts with other extensions

#### Database Issues
```bash
# Check for failed updates
php maintenance/checkSchema.php

# Repair tables if needed
php maintenance/rebuildall.php
```

## Container Registry

### 1. Image Naming Convention
- `your-registry/islamdashboard:1.0.0` - Production release
- `your-registry/islamdashboard:latest` - Latest stable build
- `your-registry/islamdashboard:dev-<commit-hash>` - Development builds
- `your-registry/islamdashboard:pr-<number>` - Pull request previews

### 2. Security Scanning
```bash
# Scan for vulnerabilities
docker scan islamdashboard:latest

# Scan with Trivy
trivy image --severity CRITICAL your-registry/islamdashboard:latest

# Scan with Snyk
snyk container test your-registry/islamdashboard:latest --file=Dockerfile
```

### 3. Image Signing
```bash
# Sign the image
cosign sign --key cosign.key your-registry/islamdashboard:1.0.0

# Verify the signature
cosign verify --key cosign.pub your-registry/islamdashboard:1.0.0
```

## Version History
- **0.4.0**: Added containerization support
- **0.3.1**: Initial version
