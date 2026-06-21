# Deploy na VPS Hostinger

## Pré-requisitos

- Ubuntu 24.04 LTS (ou 22.04+)
- Acesso root via SSH
- Domínio apontando pro IP da VPS (ou usar IP direto)
- Certificado TLS (Let's Encrypt via certbot)

## Passo 1 — Endurecer SSH (ANTES de instalar qualquer coisa)

```bash
# Criar usuário não-root
adduser materiais
usermod -aG sudo materiais

# Copiar chave SSH do root pro novo user
mkdir -p /home/materiais/.ssh
cp -r ~/.ssh/authorized_keys /home/materiais/.ssh/
chown -R materiais:materiais /home/materiais/.ssh
chmod 700 /home/materiais/.ssh
chmod 600 /home/materiais/.ssh/authorized_keys

# Testar login como materiais ANTES de desabilitar root
# (em outra janela: ssh materiais@76.13.123.172)
```

Se o login como `materiais` funcionou:

```bash
# Editar /etc/ssh/sshd_config
nano /etc/ssh/sshd_config

# Mudar:
PermitRootLogin yes    →  PermitRootLogin no
PasswordAuthentication yes  →  PasswordAuthentication no
# (opcional) Port 22 → Port 2222

# Reiniciar SSH
systemctl restart sshd
```

## Passo 2 — Firewall (UFW)

```bash
ufw default deny incoming
ufw default allow outgoing
ufw allow 2222/tcp    # ou 22/tcp se manteve porta padrão
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable
ufw status verbose
```

## Passo 3 — Fail2ban

```bash
apt install fail2ban -y
systemctl enable fail2ban
systemctl start fail2ban
```

## Passo 4 — Instalar stack PHP

```bash
apt update && apt upgrade -y
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update

apt install -y \
    php8.2 \
    php8.2-cli \
    php8.2-mysql \
    php8.2-pdo \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-curl \
    php8.2-zip \
    php8.2-intl \
    php8.2-gd \
    libapache2-mod-php8.2 \
    apache2 \
    mariadb-server \
    composer \
    git \
    unzip

# Habilitar mod_rewrite do Apache
a2enmod rewrite headers ssl

# Testar PHP
php -v
```

## Passo 5 — Configurar MariaDB

```bash
mysql_secure_installation
# Responde:
#   Set root password? Yes (senha forte)
#   Remove anonymous users? Yes
#   Disallow root login remotely? Yes
#   Remove test database? Yes
#   Reload privilege tables? Yes

# Criar banco e usuário da aplicação
mysql -u root -p

CREATE DATABASE materiais_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'materiais'@'localhost' IDENTIFIED BY 'SenhaForte_!2024';
GRANT ALL PRIVILEGES ON materiais_local.* TO 'materiais'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Passo 6 — Clonar repo

```bash
# Como usuário materiais
sudo -u materiais -H bash -c '
  cd ~
  git clone https://github.com/Willians96/materiais_local.git app
  cd app
  cp .env.example .env
  # Editar .env com as credenciais MySQL criadas acima
  nano .env
  composer install --no-dev
'
```

## Passo 7 — Apache vhost

```bash
sudo tee /etc/apache2/sites-available/materiais.conf <<'EOF'
<VirtualHost *:80>
    ServerName materiais.exemplo.com.br
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

# Habilitar site
sudo a2ensite materiais.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

## Passo 8 — Permissões

```bash
sudo chown -R materiais:www-data /home/materiais/app
sudo chmod -R 755 /home/materiais/app
sudo chmod -R 770 /home/materiais/app/tmp
sudo chmod -R 770 /home/materiais/app/public/uploads
```

## Passo 9 — TLS (Let's Encrypt)

```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d materiais.exemplo.com.br
# Renova automático via cron/systemd
```

## Passo 10 — Backup automatizado

```bash
sudo tee /etc/cron.daily/materiais-backup <<'EOF'
#!/bin/bash
BACKUP_DIR=/home/materiais/backups
mkdir -p $BACKUP_DIR
DATA=$(date +%Y%m%d-%H%M)
mysqldump -u materiais -p'SenhaForte_!2024' materiais_local | gzip > $BACKUP_DIR/db-$DATA.sql.gz
tar -czf $BACKUP_DIR/app-$DATA.tar.gz -C /home/materiais app --exclude='app/tmp/sessions/*'
# Manter só últimos 7 backups
ls -tp $BACKUP_DIR/db-* | tail -n +8 | xargs -I {} rm -- {}
ls -tp $BACKUP_DIR/app-* | tail -n +8 | xargs -I {} rm -- {}
EOF
sudo chmod +x /etc/cron.daily/materiais-backup
```

## Testar

```bash
curl -I http://materiais.exemplo.com.br/new/
# Deve retornar 200 ou 302 (redirect pra /signin/)
```

## Atualizar depois (deploy)

```bash
sudo -u materiais -H bash -c '
  cd ~/app
  git pull
  composer install --no-dev
'
```

## URLs importantes

- **Site**: http://materiais.exemplo.com.br/new/
- **Login**: http://materiais.exemplo.com.br/new/signin/
- **Logs Apache**: `/var/log/apache2/materiais-*.log`
- **Sessões PHP**: `/home/materiais/app/tmp/sessions/`
