#!/bin/bash
# Reset senha MySQL
sudo systemctl stop mariadb
sleep 3
sudo mysqld_safe --skip-grant-tables --skip-networking &
sleep 5
sudo mysql -e "FLUSH PRIVILEGES; ALTER USER 'root'@'localhost' IDENTIFIED BY 'MateriaisRoot_2024'; FLUSH PRIVILEGES;"
sleep 2
sudo pkill -f mysqld_safe
sleep 2
sudo systemctl start mariadb
sleep 3
sudo mysql -u root -p'MateriaisRoot_2024' -e "SELECT 1, VERSION();"
