# Siroko-DDD Carrito de Compras API

Este repositorio implementa una API de carrito de compras siguiendo los principios de **Domain-Driven Design (DDD)** y **Arquitectura Hexagonal** en **Laravel**.

## Configuración del Proyecto

### Requisitos Previos

- **PHP** (>= 8.2)
- **Composer** (gestor de dependencias de PHP)
- **SQLite** (base de datos para pruebas locales)
- **Laravel** (11)

### Instalación Automática

Para configurar el proyecto automáticamente, ejecuta el siguiente script en la consola de tu IDE:

```
./setup.sh
```

El script se encargará de:

1. Instalar dependencias con `composer install`.
2. Configurar la base de datos SQLite.
3. Generar la clave de aplicación de Laravel.
4. Ejecutar las migraciones y seeders para poblar la base de datos.
5. Ejecutar las migraciones y seeders para el entorno de tests
6. Lanza los tests

Por defecto, el servidor se ejecutará en `http://127.0.0.1:8000`.

Accede a la ruta principal para ver los productos disponibles:

## Funcionalidades Implementadas

### Endpoints Principales

1. **Ver todos los productos disponibles:**
   ```
   GET /all-products-available
   ```

2. **Ver tu carrito:**
   ```
   GET /{cartId}/cart/view
   ```
   
3. **Página de Gracias:**
   ```
   GET /{cartId}/thank-you
   ```

4. **Añadir productos al carrito:**
   ```
   POST /cart/{cartId}/add-cart-item
   ```

5. **Actualizar cantidad de un producto en el carrito:**
   ```
   PUT /cart/{cartId}/update-cart
   ```

6. **Eliminar producto del carrito:**
   ```
   DELETE /cart/{cartId}/remove-cart-item/{cartItemId}
   ```

7. **Recuento de productos en el carrito:**
   ```
   POST /cart/{cartId}/cart-items/count
   ```

8. **Finalizar compra del carrito:**
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
│   ├── Domain/             # Entidades, repositorios y excepciones
│   └── Http/               # Controladores
│   └── Infrastructure/     # Repositorios Eloquent
│   └── Models/             # Modelo de las entidades
├── database/
│   └── migrations/         # Migraciones de la base de datos
│   └── seeders/            # Seeders necesarios para la prueba
│   └── factories/          # Factorias necesarias para tests
├── resources/              # Vistas y traducciones necesarias para la prueba
├── routes/
│   └── web.php             # Definición de rutas para las vistas
│   └── api.php             # Definición de rutas para la API
├── tests/                  # Pruebas funcionales
├── setup.sh                # Script de configuración automática
└── README.md               # Documentación del proyecto
```

**Desarrollado por:** David Bravo de Arce  
**Fecha de entrega:** 16/12/2024