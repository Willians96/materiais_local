#!/bin/bash
set -e
BACKUP_DIR=/home/materiais/backups
mkdir -p "$BACKUP_DIR"
DATA=`date +%Y%m%d-%H%M`
mysqldump -u materiais -p'Materiais@2024' materiais_local | gzip > "$BACKUP_DIR/db-$DATA.sql.gz"
tar -czf "$BACKUP_DIR/app-$DATA.tar.gz" -C /home/materiais app --exclude='app/tmp/sessions/*' --exclude='app/vendor' 2>/dev/null
ls -tp "$BACKUP_DIR/db-"* 2>/dev/null | tail -n +8 | xargs -r -I {} rm -- {}
ls -tp "$BACKUP_DIR/app-"* 2>/dev/null | tail -n +8 | xargs -r -I {} rm -- {}
echo "BACKUP_OK: $DATA"
