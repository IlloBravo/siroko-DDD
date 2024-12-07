# 🚀 **siroko-DDD**

### Siroko Code Challenge

API desarrollada en **Laravel** para gestionar un carrito de compras en una plataforma e-commerce de productos deportivos.

---

## 📂 **Estructura del Proyecto**

- **Framework**: Laravel
- **Arquitectura**: Hexagonal
- **Patrón de Diseño**: Domain-Driven Design (DDD)
- **Entorno**: Laravel Artisan y WSL (Linux)

---

## 📚 **Endpoints de la API**

| **Método** | **Ruta**                | **Descripción**                    |
|------------|-------------------------|------------------------------------|
| `POST`    | `/cart/products`        | Añadir un producto al carrito      |
| `PUT`     | `/cart/products/{id}`   | Actualizar un producto del carrito |
| `DELETE`  | `/cart/products/{id}`   | Eliminar un producto del carrito   |
| `GET`     | `/cart/products/count`  | Obtener el número total de productos |
| `POST`    | `/cart/checkout`        | Confirmar la compra del carrito    |

---

## ⚙️ **Casos de Uso**

1. **Añadir Producto**  
   Permite agregar un producto con su cantidad al carrito.

2. **Actualizar Producto**  
   Actualiza la cantidad de un producto existente en el carrito.

3. **Eliminar Producto**  
   Elimina un producto específico del carrito.

4. **Obtener Total de Productos**  
   Devuelve el número total de productos en el carrito.

5. **Confirmar Compra**  
   Finaliza la compra y vacía el carrito.

---

## 🧩 **Entidades y Agregados**

### **Entidad Principal:**
- `Cart` (Carrito)

### **Entidades Relacionadas:**
- `Product` (Producto)

### **Agregado:**
- Un carrito agrupa múltiples productos.
