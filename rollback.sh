#!/bin/bash

###############################################################################
# Script de rollback - Sages Home
# Restaure la dernière sauvegarde en cas de problème
###############################################################################

set -e

# Configuration
APP_DIR="/home4/scisalyq/sageshome.ci"
BACKUP_DIR="/home4/scisalyq/backups"
PHP_PATH="/usr/local/bin/php"

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

###############################################################################
# ROLLBACK
###############################################################################

log "=== Début du rollback ==="

# Vérifier qu'il y a des backups
if [ ! -d "$BACKUP_DIR" ] || [ -z "$(ls -A $BACKUP_DIR/backup-*.tar.gz 2>/dev/null)" ]; then
    error "Aucun backup disponible dans $BACKUP_DIR"
    exit 1
fi

# Lister les backups disponibles
log "Backups disponibles :"
ls -lht "$BACKUP_DIR"/backup-*.tar.gz | head -5

# Utiliser le dernier backup par défaut
LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/backup-*.tar.gz | head -1)

if [ -z "$1" ]; then
    log "Utilisation du dernier backup : $(basename $LATEST_BACKUP)"
    BACKUP_FILE="$LATEST_BACKUP"
else
    BACKUP_FILE="$BACKUP_DIR/$1"
    if [ ! -f "$BACKUP_FILE" ]; then
        error "Backup $BACKUP_FILE introuvable"
        exit 1
    fi
fi

# Confirmation
read -p "Confirmer le rollback avec $(basename $BACKUP_FILE) ? (y/N) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    log "Rollback annulé"
    exit 0
fi

# Mode maintenance
log "Activation du mode maintenance..."
cd "$APP_DIR"
$PHP_PATH artisan down --secret="rollback-token-$(date +%s)"

# Sauvegarde de l'état actuel avant rollback
EMERGENCY_BACKUP="$BACKUP_DIR/emergency-before-rollback-$(date +'%Y%m%d-%H%M%S').tar.gz"
log "Sauvegarde d'urgence : $EMERGENCY_BACKUP"
tar -czf "$EMERGENCY_BACKUP" \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='storage/logs' \
    --exclude='.git' \
    . 2>/dev/null

# Restauration
log "Restauration du backup..."
tar -xzf "$BACKUP_FILE" -C "$APP_DIR"

# Reinstaller les dépendances
log "Réinstallation des dépendances..."
cd "$APP_DIR"
composer install --no-dev --optimize-autoloader --no-interaction

# Clear cache
log "Nettoyage du cache..."
$PHP_PATH artisan cache:clear
$PHP_PATH artisan config:clear
$PHP_PATH artisan route:clear
$PHP_PATH artisan view:clear

# Recache
$PHP_PATH artisan config:cache
$PHP_PATH artisan route:cache
$PHP_PATH artisan view:cache

# Permissions
log "Ajustement des permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework storage/app

# Redémarrer les workers
log "Redémarrage des workers..."
$PHP_PATH artisan queue:restart

# Désactiver maintenance
log "Désactivation du mode maintenance..."
$PHP_PATH artisan up

log "=== Rollback terminé ==="
log "Version restaurée depuis : $(basename $BACKUP_FILE)"
log "Sauvegarde d'urgence créée : $EMERGENCY_BACKUP"

exit 0
