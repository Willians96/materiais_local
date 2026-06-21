#!/bin/bash
set -e

# === Para evo-crm (NÃO remove, só stop) ===
echo "=== Parando evo-crm ==="
cd /opt/evo-crm 2>/dev/null || cd ~/evo-crm 2>/dev/null || find / -name "docker-compose*.yml" -path "*evo*" 2>/dev/null | head -3
# Para todos containers evo
docker ps --format '{{.Names}}' | grep -E 'evo|traefik|dockge' | xargs -r docker stop 2>&1 | tail -10
echo "Containers parados: $(docker ps --format '{{.Names}}' | wc -l) ainda rodando"
docker ps --format 'table {{.Names}}\\t{{.Status}}' | head -10

# === Corrige composer.json pra Twig 3.x ===
cd /home/materiais/app
# Backup
cp composer.json composer.json.bak
# Atualiza Twig pra aceitar versão atual
sed -i 's/"twig\/twig": "3.0"/"twig\/twig": "^3.0"/' composer.json
cat composer.json
echo "=== composer install ==="
composer install --no-dev --optimize-autoloader 2>&1 | tail -5

# === Sobe Apache ===
echo "=== Subindo Apache ==="
sudo a2ensite materiais.conf 2>&1 | tail -2
sudo a2dissite 000-default.conf 2>&1 | tail -2
sudo systemctl enable apache2
sudo systemctl start apache2
sleep 2
sudo systemctl status apache2 --no-pager | head -5
echo "=== TESTE HTTP ==="
curl -I http://localhost/ 2>&1 | head -5
curl -I http://localhost/new/ 2>&1 | head -5
