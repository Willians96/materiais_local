#!/bin/bash
set -e

# Apache (www-data) precisa de acesso ao /home/materiais/app
# Solução 1: chmod 755 no home (recomendado pra dev)
chmod 755 /home/materiais
chmod 755 /home/materiais/app
# Garante que www-data pode ler tudo
sudo chown -R materiais:www-data /home/materiais/app
sudo find /home/materiais/app -type d -exec chmod 755 {} \;
sudo find /home/materiais/app -type f -exec chmod 644 {} \;
# tmp e uploads precisam de write pro www-data
sudo chmod -R 775 /home/materiais/app/tmp
sudo chmod -R 775 /home/materiais/app/public/uploads 2>/dev/null || true
# .env só leitura pro owner
chmod 600 /home/materiais/app/.env

echo "=== TESTE HTTP ==="
curl -sS -o /dev/null -w 'GET /         HTTP %{http_code}\n' http://localhost/
curl -sS -o /dev/null -w 'GET /new/     HTTP %{http_code}\n' http://localhost/new/
curl -sS -o /dev/null -w 'GET /signin/  HTTP %{http_code}\n' http://localhost/new/signin/
