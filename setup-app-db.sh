#!/bin/bash
# Cria banco e user da app
sudo mysql -u root -p'MateriaisRoot_2024' <<'EOSQL'
CREATE DATABASE IF NOT EXISTS materiais_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'materiais'@'localhost' IDENTIFIED BY 'Materiais@2024';
GRANT ALL PRIVILEGES ON materiais_local.* TO 'materiais'@'localhost';
FLUSH PRIVILEGES;
EOSQL
echo "=== TESTE CONEXÃO COM USER DA APP ==="
mysql -u materiais -p'Materiais@2024' materiais_local -e "SELECT DATABASE(), USER(), VERSION();"
