# Lexxis Backend

Backend Laravel de Lexxis. Expone la API consumida por la SPA de frontend, gestiona el dominio de catálogo y fabricación 3D, y proporciona un panel de administración Blade para operar productos, usuarios, pedidos y trabajos de impresión.

## Stack verificado

- Laravel 12.
- PHP definido en `composer.json` como `^8.2`.
- Laravel Sanctum para autenticación API con token Bearer y soporte de cookies stateful.
- MariaDB 11 en Laravel Sail/Docker. El servicio se llama `mysql` por compatibilidad con Sail.
- Redis, Mailpit, Meilisearch y Selenium están configurados en `compose.yaml`.
- Blade y Vite para el panel web/admin de Laravel.

## Requisitos

- PHP y Composer compatibles con las dependencias del proyecto.
- Node.js y npm para assets Vite del backend Laravel.
- Docker si se trabaja con Laravel Sail.
- Base de datos local configurada en `.env`.

Nota: en `compose.yaml` Sail usa el runtime `8.5`. Si se ejecuta fuera de Sail, conviene comprobar la versión real exigida por las dependencias instaladas.

## Instalación local

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
```

Con Sail:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

Los seeders deben ejecutarse solo en entorno local:

```bash
./vendor/bin/sail artisan db:seed
```

## Variables de entorno relevantes

No incluir secretos reales en el repositorio.

- `APP_URL`: URL pública o local del backend.
- `APP_DEBUG`: debe ser `false` en producción.
- `FRONTEND_URL`: origen del frontend.
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`: conexión de base de datos.
- `SANCTUM_STATEFUL_DOMAINS`: dominios stateful permitidos por Sanctum.
- `SESSION_DOMAIN`, `SESSION_SECURE_COOKIE`: configuración de sesión/cookies.
- `FILESYSTEM_DISK`: disco de almacenamiento usado por Laravel.

## Comandos habituales

```bash
composer install
npm install
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan test
./vendor/bin/sail artisan route:list --path=api
./vendor/bin/sail artisan route:list --path=admin
```

También existe el script Composer:

```bash
composer test
```

## Autenticación

La API usa Laravel Sanctum. En `routes/api.php` existen endpoints de registro, login tradicional y `POST /api/token-login`.

El flujo usado por el frontend es `token-login`, que devuelve:

- `token`;
- `token_type: Bearer`;
- datos básicos del usuario.

El logout API está protegido por `auth:sanctum` y revoca el token actual cuando es un token persistente de Sanctum. También conserva la limpieza de sesión web si existe sesión.

`token-login` aplica rate limit de 5 intentos por minuto por email e IP.

## Entidades principales

El dominio actual incluye:

- usuarios;
- direcciones;
- productos;
- variantes de producto;
- imágenes de producto y variantes;
- materiales;
- diseños personalizados;
- archivos imprimibles;
- análisis de archivos imprimibles;
- trabajos de impresión;
- carritos e items de carrito;
- pedidos e items de pedido;
- configuración de precios.

## API pública

La API pública incluye catálogo:

- productos;
- detalle de producto;
- variantes;
- opciones de configurador.

La zona autenticada incluye:

- perfil de usuario;
- archivos 3D;
- trabajos de impresión;
- diseños personalizados;
- carrito;
- checkout y pedidos.

## Panel de administración

El panel admin está definido en `routes/web.php` bajo `/admin` y protegido por `auth` y middleware `admin`.

Permite gestionar:

- productos;
- variantes;
- imágenes;
- diseños personalizados;
- materiales;
- archivos imprimibles;
- trabajos de impresión;
- usuarios;
- direcciones;
- carritos;
- pedidos.

El dashboard admin muestra métricas de usuarios, pedidos, ingresos, archivos 3D, trabajos pendientes de revisión, resumen por estado, modelos más vendidos, últimos pedidos y accesos rápidos.

## Seeders demo

Los seeders actuales preparan datos locales de demostración sin hacer truncate de productos:

- usuarios demo y admin demo;
- direcciones;
- productos demo limitados a los slugs previstos;
- variantes;
- diseños personalizados;
- archivos y trabajos de impresión demo;
- pedidos demo;
- materiales y configuración de precios.

Los productos demo se crean o reutilizan por slug. Las imágenes de producto no dependen de los seeders y deben gestionarse manualmente desde el panel.

## Despliegue y almacenamiento

El proyecto contempla backend en Railway y frontend en Vercel. En producción:

- `APP_DEBUG=false`;
- `APP_URL` debe apuntar a la URL pública del backend;
- los dominios/orígenes del frontend deben estar configurados para CORS/Sanctum.

El almacenamiento local de Railway no es persistente entre redeploys o reinicios. Para imágenes y archivos 3D persistentes hace falta S3, Cloudinary o un volumen persistente.

## Limitaciones conocidas

- No se ha verificado integración con una pasarela de pago real. El checkout crea pedidos con `payment_status` y `payment_method`, pero no procesa pagos externos.
- La persistencia externa de archivos/imágenes queda pendiente de configuración.
