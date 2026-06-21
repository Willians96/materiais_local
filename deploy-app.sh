#!/bin/bash
set -e

# === Clone repo ===
cd ~
if [ ! -d app ]; then
  git clone https://github.com/Willians96/materiais_local.git app
fi
cd app

# === Configurar .env ===
cat > .env <<EOF
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=materiais_local
DB_USER=materiais
DB_PASS=Materiais@2024
APP_ENV=development
APP_BASE_PATH=/new/
APP_TIMEZONE=America/Sao_Paulo
EOF
chmod 600 .env
echo "=== .env configurado ==="
ls -la .env

# === Composer install ===
composer install --no-dev --optimize-autoloader 2>&1 | tail -5

# === Apache vhost ===
sudo tee /etc/apache2/sites-available/materiais.conf > /dev/null <<'EOF'
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /home/materiais/app
    <Directory /home/materiais/app>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/materiais-error.log
    CustomLog ${APACHE_LOG_DIR}/materiais-access.log combined
    php_admin_value upload_max_filesize 50M
    php_admin_value post_max_size 50M
    php_admin_value memory_limit 256M
</VirtualHost>
EOF

sudo a2ensite materiais.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2

# === Permissões ===
sudo chown -R materiais:www-data /home/materiais/app
sudo chmod -R 755 /home/materiais/app
sudo chmod -R 770 /home/materiais/app/tmp
sudo chmod -R 770 /home/materiais/app/public/uploads 2>/dev/null || true
sudo mkdir -p /home/materiais/app/tmp/sessions
sudo mkdir -p /home/materiais/app/public/uploads
sudo chown -R materiais:www-data /home/materiais/app/tmp/sessions /home/materiais/app/public/uploads
sudo chmod -R 770 /home/materiais/app/tmp/sessions /home/materiais/app/public/uploads
echo "=== Permissões aplicadas ==="

# === Teste Apache ===
echo "=== APACHE STATUS ==="
sudo systemctl status apache2 --no-pager | head -5
