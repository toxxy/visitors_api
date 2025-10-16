# Visitors API — Deploy rápido (Hostinger)

Pasos mínimos para desplegar en Hostinger subdominio `visitors` sirviendo `/api`:

1) Subir la carpeta `api/` tal cual (incluye `vendor/`, `.htaccess`, `routes/`, `artisan`).

2) Copiar `.env.example` a `.env` y completar variables en el servidor:
```bash
cp .env.example .env
# edita DB_*, MAIL_*, APP_URL, APP_FRONTEND_URL
```

3) Crear carpetas de runtime y permisos:
```bash
mkdir -p storage/logs storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache
find storage -type d -exec chmod 775 {} +
find bootstrap/cache -type d -exec chmod 775 {} +
find storage -type f -exec chmod 664 {} + || true
chmod 755 artisan 2>/dev/null || true
# fallback temporal si hay Permission denied
# chmod -R 777 storage bootstrap/cache
```

4) Optimizar y (opcional) migrar:
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
# php artisan migrate --force
```

5) Healthcheck:
- GET `https://<tu-dominio>/api/health` debe responder `{status:"ok"}`.

Notas:
- Si el subdominio puede apuntar a `api/public` como document root, es preferible; si no, usa `.htaccess` en `api/` para redirigir a `public/` (incluido).
- Para logs en entornos con problemas de permisos, usa `LOG_CHANNEL=errorlog` en `.env` y vuelve a cachear config.
