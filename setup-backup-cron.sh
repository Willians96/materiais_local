#!/bin/bash
set -e

# === Backup diario (DB + arquivos, retém 7 dias) ===
BACKUP_DIR=/home/materiais/backups
mkdir -p $BACKUP_DIR

sudo tee /etc/cron.daily/materiais-backup > /dev/null <<EOF
#!/bin/bash
set -e
BACKUP_DIR=/home/materiais/backups
mkdir -p \$BACKUP_DIR
DATA=\$(date +%Y%m%d-%H%M)
mysqldump -u materiais -p'Materiais@2024' materiais_local | gzip > \$BACKUP_DIR/db-\$DATA.sql.gz
tar -czf \$BACKUP_DIR/app-\$DATA.tar.gz -C /home/materiais app --exclude='app/tmp/sessions/*' --exclude='app/vendor'
ls -tp \$BACKUP_DIR/db-* | tail -n +8 | xargs -r -I {} rm -- {}
ls -tp \$BACKUP_DIR/app-* | tail -n +8 | xargs -r -I {} rm -- {}
EOF

sudo chmod +x /etc/cron.daily/materiais-backup
echo "=== BACKUP CRON OK ==="
ls -la /etc/cron.daily/materiais-backup
echo ""
echo "=== TESTE (força um backup agora) ==="
sudo /etc/cron.daily/materiais-backup
ls -la $BACKUP_DIR/ | head -10
