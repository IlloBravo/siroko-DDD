#!/bin/bash

echo "🚀 Iniciando la configuración del entorno..."

echo "📦 Instalando dependencias de Composer..."
composer install

echo "🔑 Generando clave de aplicación..."
php artisan key:generate

echo "🗄️ Configurando la base de datos SQLite..."
touch database/database.sqlite

if [ ! -f .env ]; then
    echo "📝 Creando archivo .env..."
    cp .env.example .env
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
    sed -i 's/DB_DATABASE=.*/DB_DATABASE=database\/database.sqlite/' .env
fi

echo "⚙️ Ejecutando migraciones y seeders..."
php artisan migrate && php artisan db:seed

echo "🌐 Iniciando el servidor de desarrollo en http://127.0.0.1:8000..."
php artisan serve
