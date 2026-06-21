#!/bin/bash
set -e

# === UFW ===
sudo apt install -y ufw
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow 2222/tcp comment 'SSH customizado'
sudo ufw allow 80/tcp comment 'HTTP'
sudo ufw allow 443/tcp comment 'HTTPS'
# NÃO vamos abrir 22 (SSH padrão desabilitado)
echo "y" | sudo ufw enable
sudo ufw status verbose

# === Fail2ban ===
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
sudo systemctl status fail2ban --no-pager | head -5
