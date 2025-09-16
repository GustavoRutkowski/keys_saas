#!/bin/sh
set -e

# Caminhos: /backend/vendor/autoload.php + composer.json + composer.lock
VENDOR_AUTOLOAD="/var/www/html/backend/vendor/autoload.php"
COMPOSER_JSON="/var/www/html/composer.json"
COMPOSER_LOCK="/var/www/html/composer.lock"

# Se o autoload não existir ou composer.lock for mais novo que autoload, instala
if [ ! -f "$VENDOR_AUTOLOAD" ] || [ "$COMPOSER_LOCK" -nt "$VENDOR_AUTOLOAD" ]; then
    echo "[entrypoint] vendor ausente ou desatualizado. Rodando composer install..."
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress
    # Ajusta proprietário para www-data (evita problemas de permissão)
    chown -R www-data:www-data /var/www/html/backend/vendor || true
else
    echo "[entrypoint] vendor encontrado — pulando composer install."
fi

# exec para o entrypoint padrão da imagem PHP
exec docker-php-entrypoint apache2-foreground
