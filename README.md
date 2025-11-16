# 🕯️ Tienda de Velas - Velas de Fantasía

Aplicación web CRUD para gestionar productos de una tienda de velas artesanales, desarrollada con **PHP 8.2**, **PostgreSQL** y **Docker**.

---

## 📋 Descripción

**Velas de Fantasía** es una tienda especializada en velas aromáticas inspiradas en temática de la Tierra Media. La aplicación permite:

- ✅ Listar, crear, editar y eliminar productos
- 🔐 Autenticación con roles (ADMIN, USER)
- 🖼️ Gestión de imágenes de productos
- 🔍 Búsqueda por nombre o descripción y filtrado por categoría y fragancia
- 🎨 Interfaz desarrollada con Bootstrap 5

---

## 🛠️ Tecnologías

| Tecnología | Versión | Propósito |
|-----------|---------|----------|
| PHP | 8.2 | Backend |
| PostgreSQL | 12 | Base de datos |
| Docker | Latest | Containerización |
| Bootstrap | 5.3.3 | Frontend |
| Composer | Latest | Gestor de dependencias |
| Ramsey/UUID | 4.7 | Generación de UUIDs |
| vlucas/phpdotenv | 5.6 | Variables de entorno |


---

## 📦 Estructura del Proyecto

```
proyecto-php/
├── src/
│   ├── config/
│   │   └── Config.php          # Configuración global (BD, rutas)
│   ├── models/
│   │   ├── Producto.php
│   │   ├── Categoria.php
│   │   ├── Fragancia.php
│   │   ├── User.php
│   │   └── Rol.php
│   ├── services/
│   │   ├── ProductosService.php
│   │   ├── CategoriasService.php
│   │   ├── FraganciasService.php
│   │   ├── UsersService.php
│   │   ├── RolesServices.php
│   │   └── SessionService.php
│   ├── uploads/                # Almacenamiento de imágenes
│   ├── header.php              # Encabezado HTML
│   ├── footer.php              # Pie de página HTML
│   ├── index.php               # Listado de productos
│   ├── create.php              # Crear producto
│   ├── details.php             # Detalles del producto
│   ├── update.php              # Editar producto
│   ├── update-image.php        # Actualizar imagen
│   ├── delete.php              # Eliminar producto
│   ├── login.php               # Autenticación
│   └── logout.php              # Cerrar sesión
├── database/
│   └── init.sql                # Script de inicialización BD
├── vendor/                     # Dependencias Composer
├── docker-compose.yml          # Orquestación de contenedores
├── Dockerfile                  # Construcción imagen PHP
├── composer.json               # Dependencias del proyecto
├── .env                        # Variables de entorno
└── README.md                   # Este archivo
```

---

## 🚀 Instalación y Ejecución

### Requisitos Previos
- Docker y Docker Compose instalados
- Git (para clonar el repositorio)
- Navegador web moderno

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/LauraFmr/tienda-velas-php.git
   cd proyecto-php
   ```

2. **Configurar variables de entorno**
   
   El archivo `.env` ya está configurado con valores por defecto:
   ```env
   POSTGRES_DB=tienda_velas
   POSTGRES_USER=admin
   POSTGRES_PASSWORD=adminPassword123
   POSTGRES_HOST=postgres-db
   POSTGRES_PORT=5432
   APP_PORT=8080
   APP_BASE=http://localhost:8080
   ```

3. **Levantar los contenedores**
   ```bash
   docker-compose up -d --build
   ```

4. **Acceder a la aplicación**
   - **App principal:** http://localhost:8080
   - **Adminer (gestión BD):** http://localhost:8081
   - **Credenciales Adminer:**
     - System: PostgreSQL
     - Server: postgres-db
     - Username: admin
     - Password: adminPassword123
     - Database: tienda_velas

5. **Detener los contenedores**
   ```bash
   docker-compose down
   ```

---

## 👤 Usuarios Predefinidos

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| admin | Admin123456 | ADMIN |
| usuario | User123456 | USER |

**Nota:** El rol ADMIN tiene acceso a crear, editar y eliminar productos. El rol USER solo puede ver productos.

---

## 🔐 Autenticación y Autorización

### Flujo de Login
1. Accede a `/login.php`
2. Introduce username y contraseña
3. Si son correctos, se crea una sesión y se redirige a `/index.php`
4. La sesión caduca después de **1 hora de inactividad**

### Control de Roles
- **ADMIN:** Acceso a crear, editar, eliminar productos y subir imágenes
- **USER:** Solo lectura de productos

Las rutas protegidas redirigen automáticamente a `/index.php` si no tienes permisos.

---

## 📊 Base de Datos

### Tablas Principales

**usuarios**
- id (PK)
- username (UNIQUE)
- password (bcrypt)
- nombre, apellido, email
- is_deleted, created_at, updated_at

**productos**
- id (PK)
- uuid (UNIQUE)
- nombre, descripcion
- precio, stock
- imagen (URL)
- categoria_id (FK)
- fragancia_id (FK)
- is_deleted, created_at, updated_at

**categorias**
- id (PK)
- nombre, descripcion

**fragancias**
- id (PK)
- nombre, notas

**roles**
- id (PK)
- nombre (UNIQUE)
- descripcion

**usuarios_roles** (relación N:N)
- usuario_id (FK)
- rol_id (FK)

---

## 🖼️ Gestión de Imágenes

### Subir Imagen de Producto
1. Inicia sesión como ADMIN
2. Abre un producto → botón "Imagen"
3. Selecciona un JPG o PNG
4. La imagen se guarda en `src/uploads/` con nombre basado en UUID del producto
5. Se almacena la URL en la BD

### Especificaciones
- **Formatos soportados:** JPG, PNG
- **Tamaño máximo recomendado:** 3MB
- **Ubicación en servidor:** `/src/uploads/`
- **URL pública:** `/uploads/<uuid>.jpg|png`

---

## 🔍 Búsqueda y Filtrado

En `/index.php` puedes:
- **Buscar por nombre/descripción:** Campo "Buscar"
- **Filtrar por categoría:** Desplegable "Categorías"
- **Filtrar por fragancia:** Desplegable "Fragancias"

Los filtros se combinan con lógica AND en la consulta SQL.

---

## 📝 API / Servicios Internos

### ProductosService
- `findAllWithFilters(?string $q, ?string $catId, ?string $fragId): array`
- `findById(int $id): ?Producto`
- `save(array $data): int`
- `update(int $id, array $data): bool`
- `updateImage(int $id, ?string $url): bool`
- `deleteById(int $id): bool`

### UsersService
- `authenticate(string $username, string $password): User`
- `findUserByUsername(string $username): ?User`

### SessionService
- `login(array $userData): void`
- `logout(): void`
- `isLoggedIn(): bool`
- `user(): ?array`
- `hasRole(string $role): bool`

---

## 🐛 Posibles errores

### Problema: "Usuario o contraseña incorrectos"
**Solución:**
- Verifica que has introducido la contraseña exacta (sensible a mayúsculas/minúsculas)
- Comprueba en Adminer que el usuario existe en tabla `usuarios`
- Genera un nuevo hash bcrypt y actualiza en BD:
  ```bash
  docker exec -i php_app php -r "echo password_hash('TuContraseña123', PASSWORD_BCRYPT);"
  ```

### Problema: Imágenes no se cargan
**Solución:**
- Verifica que existen en `src/uploads/`
- Comprueba permisos: `docker exec php_app ls -l /var/www/html/src/uploads/`
- Asegúrate de que la URL en BD es correcta: `/uploads/<nombre_archivo>`

### Problema: "DB connection error"
**Solución:**
- Verifica que Postgres está funcionando: `docker-compose ps`
- Comprueba variables en `.env`
- Levanta de nuevo: `docker-compose down -v && docker-compose up -d --build`

---

## 📋 Lista de Características Implementadas

- ✅ CRUD completo de productos
- ✅ Autenticación con roles y sesiones
- ✅ Gestión de imágenes con UUID
- ✅ Búsqueda avanzada con filtros
- ✅ Interfaz responsive con Bootstrap
- ✅ Base de datos normalizada (PostgreSQL)
- ✅ Validación de permisos en backend
- ✅ Logout
- ✅ Timestamps automáticos (created_at, updated_at)
- ✅ Borrado lógico (is_deleted) para productos

---

## 📖 Docencia

Este proyecto ha sido desarrollado como parte del **2º curso de Desarrollo de Aplicaciones Web (DAW)** - módulo de **Desarrollo en Servidor**, durante la **1ª Evaluación (2025-2026)**.


---

## 📄 Licencia

Este proyecto es de uso educativo. Distribuido bajo licencia **Creative Commons Attribution NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0)**.

---

## 📞 Contacto

**Desarrolladora:** Laura Fernández del Moral Romero  
**GitHub:** [https://github.com/LauraFmr/tienda-velas-php]  


---

**Última actualización:** Noviembre 2025  
**Estado:** ✅ Funcional
