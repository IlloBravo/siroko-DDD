# Siroko-DDD Carrito de Compras API

Este repositorio implementa una API de carrito de compras siguiendo los principios de **Domain-Driven Design (DDD)** y **Arquitectura Hexagonal** en **Laravel**.

## Configuración del Proyecto

### Requisitos Previos

- **PHP** (>= 8.1)
- **Composer** (gestor de dependencias de PHP)
- **SQLite** (base de datos para pruebas locales)
- **Laravel** (>= 9.x)

### Instalación Automática

Para configurar el proyecto automáticamente, ejecuta el siguiente script en la consola de tu IDE:

```
./setup.sh
```

El script se encargará de:

1. Instalar dependencias con `composer install`.
2. Configurar la base de datos SQLite.
3. Ejecutar las migraciones y seeders para poblar la base de datos.
4. Generar la clave de aplicación de Laravel.

Por defecto, el servidor se ejecutará en `http://127.0.0.1:8000`.

Accede a la ruta principal para ver los productos disponibles:

```
http://127.0.0.1:8000/all-products-available
```

## Funcionalidades Implementadas

### Endpoints Principales

1. **Ver todos los productos disponibles:**
   ```
   GET /all-products-available
   ```

2. **Ver todos los carritos creados:**
   ```
   GET /all-carts-created
   ```

3. **Añadir productos al carrito:**
   ```
   POST /cart/{cartId}/add-product
   ```

4. **Actualizar cantidad de un producto en el carrito:**
   ```
   PUT /cart/{cartId}/update-product/{productId}
   ```

5. **Eliminar producto del carrito:**
   ```
   DELETE /cart/{cartId}/remove-product/{productId}
   ```

6. **Finalizar compra del carrito:**
   ```
   POST /cart/{cartId}/checkout
   ```

### Principios Aplicados

- **DDD (Domain-Driven Design)**
- **Arquitectura Hexagonal**
- **Principios SOLID**

## Pruebas

### Ejecutar Pruebas Unitarias

Para ejecutar las pruebas unitarias y de integración:

```
php artisan test
```

Asegúrate de que la base de datos de pruebas esté configurada en `.env.testing` si se usa una configuración separada.

## Estructura del Proyecto

```plaintext
siroko-DDD/
├── app/
│   ├── Application/        # Casos de uso
│   ├── Domain/             # Entidades y repositorios
│   └── Http/               # Controladores
│   └── Infrastructure/     # Repositorios Eloquent y adaptadores
├── database/
│   └── migrations/         # Migraciones de la base de datos
│   └── seeders/            # Seeders necesarios para la prueba
├── resources/              # Vistas y traducciones necesarias para la prueba
├── routes/
│   └── web.php             # Definición de rutas para las vistas
│   └── api.php             # Definición de rutas para la API
├── tests/
│   └── Feature/            # Pruebas funcionales
├── setup.sh                # Script de configuración automática
└── README.md               # Documentación del proyecto
```

**Desarrollado por:** David Bravo de Arce  
**Fecha de entrega:** 12/12/2024