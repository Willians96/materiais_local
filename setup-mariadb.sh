#!/bin/bash
set -e

# Define senha root
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED BY 'MateriaisRoot_!2024';"
mysql -e "DELETE FROM mysql.user WHERE User='';"
mysql -e "DROP DATABASE IF EXISTS test;"
mysql -e "DELETE FROM mysql.db WHERE Db='test' OR Db='test\_%';"
mysql -e "FLUSH PRIVILEGES;"

# Cria banco e user da aplicação
mysql -e "CREATE DATABASE IF NOT EXISTS materiais_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'materiais'@'localhost' IDENTIFIED BY 'Materiais@2024';"
mysql -e "GRANT ALL PRIVILEGES ON materiais_local.* TO 'materiais'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "=== TESTE CONEXÃO ==="
mysql -u materiais -p'Materiais@2024' materiais_local -e "SELECT DATABASE(), USER(), VERSION();"
