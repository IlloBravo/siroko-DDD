#!/bin/bash

echo "🚀 Iniciando la configuración del entorno..."

echo "📦 Instalando dependencias de Composer..."
composer install

echo "🔑 Generando clave de aplicación..."
php artisan key:generate

echo "🗄️ Configurando la base de datos SQLite..."
touch database/database.sqlite

# Crear el archivo .env si no existe
if [ ! -f .env ]; then
    echo "📝 Creando archivo .env..."
    cp .env.example .env
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
    sed -i 's/DB_DATABASE=.*/DB_DATABASE=database\/database.sqlite/' .env
fi

# Crear el archivo .env.testing si no existe
if [ ! -f .env.testing ]; then
    echo "📝 Creando archivo .env.testing..."
    cat > .env.testing <<EOL
APP_NAME=Laravel
APP_ENV=testing
APP_KEY=$(php artisan key:generate --show)
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

APP_LOCALE=es
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=es_ES

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=database/testing.sqlite

SESSION_DRIVER=array
QUEUE_CONNECTION=sync
CACHE_STORE=array
EOL
fi

echo "🗄️ Configurando la base de datos de test SQLite..."
touch database/testing.sqlite

echo "⚙️ Ejecutando migraciones y seeders..."
php artisan migrate:fresh --seed

echo "⚙️ Ejecutando migraciones y seeders para el entorno de test..."
php artisan migrate:fresh --seed --env=testing

echo "⚙️ Ejecutando tests..."
php artisan test

echo "🌐 Iniciando el servidor de desarrollo en http://127.0.0.1:8000..."
php artisan serve
