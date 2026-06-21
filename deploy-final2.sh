#!/bin/bash
set -e

# === Adiciona materiais no grupo docker ===
sudo usermod -aG docker materiais
newgrp docker <<'EOF'
echo "docker group OK"
EOF

# === Para containers evo (com sudo) ===
echo "=== Containers rodando ANTES ==="
sudo docker ps --format '{{.Names}}' | wc -l
sudo docker ps --format '{{.Names}}' | grep -E 'evo|traefik|dockge|evolution' | xargs -r sudo docker stop 2>&1 | tail -3
echo "=== Containers rodando DEPOIS ==="
sudo docker ps --format '{{.Names}}' | wc -l

# === Composer update (resolve versão travada) ===
cd /home/materiais/app
rm -f composer.lock
rm -rf vendor
composer update --no-dev --optimize-autoloader 2>&1 | tail -8

# === Sobe Apache ===
sudo systemctl start apache2
sleep 2
echo "=== Apache status ==="
sudo systemctl status apache2 --no-pager | head -8
echo "=== Teste HTTP ==="
curl -I http://localhost/ 2>&1 | head -3
echo "---"
curl -I http://localhost/new/ 2>&1 | head -3
echo "---"
curl -I http://localhost/signin/ 2>&1 | head -3
