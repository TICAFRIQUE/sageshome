#!/bin/bash

###############################################################################
# Script pour le cron - Redémarrage automatique du worker de queue
# À utiliser dans crontab pour surveillance automatique
###############################################################################

# Configuration
APP_DIR="/home4/scisalyq/sageshome.ci"
LOG_DIR="/home4/scisalyq/logs"
CRON_LOG="$LOG_DIR/cron-queue.log"

# Créer le dossier de logs si nécessaire
mkdir -p "$LOG_DIR"

# Fonction de log avec timestamp
log() {
    echo "[$(date +'%Y-%m-%d %H:%M:%S')] $1" >> "$CRON_LOG"
}

log "=== Début de l'exécution du cron ==="

# Aller dans le répertoire de l'application
cd "$APP_DIR" || {
    log "ERREUR : Impossible d'accéder au répertoire $APP_DIR"
    exit 1
}

# Vérifier que l'application existe
if [ ! -f "artisan" ]; then
    log "ERREUR : Fichier artisan introuvable dans $APP_DIR"
    exit 1
fi

# Détecter le chemin PHP
if command -v php >/dev/null 2>&1; then
    PHP_BIN=$(command -v php)
else
    PHP_BIN="/usr/local/bin/php"
fi

log "Utilisation de PHP : $PHP_BIN"
log "Répertoire : $(pwd)"

# Redémarrer le worker
log "Redémarrage du worker de queue..."
$PHP_BIN artisan queue:manage restart >> "$CRON_LOG" 2>&1

EXIT_CODE=$?

if [ $EXIT_CODE -eq 0 ]; then
    log "✅ Worker redémarré avec succès"
else
    log "❌ Erreur lors du redémarrage (code: $EXIT_CODE)"
fi

# Vérifier le statut
log "Vérification du statut..."
$PHP_BIN artisan queue:manage status >> "$CRON_LOG" 2>&1

log "=== Fin de l'exécution du cron ==="
echo "" >> "$CRON_LOG"

exit $EXIT_CODE
