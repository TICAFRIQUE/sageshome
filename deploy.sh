#!/bin/bash

###############################################################################
# Script de déploiement automatisé - Sages Home
# Chemin production: /home4/scisalyq/sageshome.ci
###############################################################################

set -e  # Arrêter en cas d'erreur

# Configuration
APP_DIR="/home4/scisalyq/sageshome.ci"
REPO_URL="https://github.com/TICAFRIQUE/sageshome.git"  # À modifier https://github.com/TICAFRIQUE/sageshome.git
BRANCH="prod" # Branche à déployer
PHP_PATH="/usr/local/bin/php"  # Adapter selon votre serveur
BACKUP_DIR="/home4/scisalyq/backups"

# Couleurs pour les logs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction de log
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
# 1. PRÉ-DÉPLOIEMENT
###############################################################################

log "=== Début du déploiement ==="

# Créer le dossier de backup si nécessaire
mkdir -p "$BACKUP_DIR"

# Vérifier que le répertoire existe
if [ ! -d "$APP_DIR" ]; then
    error "Le répertoire $APP_DIR n'existe pas !"
    exit 1
fi

cd "$APP_DIR"

# Sauvegarder la version actuelle
BACKUP_NAME="backup-$(date +'%Y%m%d-%H%M%S').tar.gz"
log "Création de la sauvegarde : $BACKUP_NAME"
tar -czf "$BACKUP_DIR/$BACKUP_NAME" \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='storage/logs' \
    --exclude='.git' \
    . 2>/dev/null || warning "Erreur lors de la création du backup"

# Garder seulement les 5 derniers backups
cd "$BACKUP_DIR"
ls -t backup-*.tar.gz | tail -n +6 | xargs -r rm
cd "$APP_DIR"

###############################################################################
# 2. ACTIVER LE MODE MAINTENANCE
###############################################################################

log "Activation du mode maintenance..."
$PHP_PATH artisan down --retry=60 --secret="deployment-token-$(date +%s)"

###############################################################################
# 3. MISE À JOUR DU CODE
###############################################################################

log "Mise à jour du code depuis Git..."

# Sauvegarder les modifications locales si nécessaire
if ! git diff-index --quiet HEAD --; then
    warning "Modifications locales détectées, stash..."
    git stash
fi

# Pull du code
git fetch origin
git reset --hard origin/$BRANCH || {
    error "Échec du pull Git"
    $PHP_PATH artisan up
    exit 1
}

###############################################################################
# 4. MISE À JOUR DES DÉPENDANCES
###############################################################################

log "Mise à jour des dépendances Composer..."
composer install --no-dev --optimize-autoloader --no-interaction || {
    error "Échec de composer install"
    $PHP_PATH artisan up
    exit 1
}

###############################################################################
# 5. MIGRATIONS ET OPTIMISATIONS
###############################################################################

log "Exécution des migrations..."
$PHP_PATH artisan migrate --force || {
    error "Échec des migrations"
    $PHP_PATH artisan up
    exit 1
}

log "Nettoyage du cache..."
$PHP_PATH artisan cache:clear
$PHP_PATH artisan config:clear
$PHP_PATH artisan route:clear
$PHP_PATH artisan view:clear

log "Optimisation..."
$PHP_PATH artisan config:cache
$PHP_PATH artisan route:cache
$PHP_PATH artisan view:cache
$PHP_PATH artisan optimize

###############################################################################
# 6. COMPILATION DES ASSETS
###############################################################################

if [ -f "package.json" ]; then
    log "Compilation des assets..."
    npm ci --production || warning "npm ci échoué"
    npm run build || warning "npm run build échoué"
fi

###############################################################################
# 7. PERMISSIONS
###############################################################################

log "Ajustement des permissions..."
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework storage/app

###############################################################################
# 8. REDÉMARRAGE DES SERVICES
###############################################################################

log "Redémarrage des workers de queue..."
$PHP_PATH artisan queue:restart

# Si vous utilisez supervisor
if command -v supervisorctl &> /dev/null; then
    log "Redémarrage de Supervisor..."
    supervisorctl restart sageshome-worker:* || warning "Supervisor non configuré ou non disponible"
fi

###############################################################################
# 9. VÉRIFICATIONS POST-DÉPLOIEMENT
###############################################################################

log "Vérifications post-déploiement..."

# Tester que l'application répond
if $PHP_PATH artisan inspire &>/dev/null; then
    log "✓ Laravel fonctionne correctement"
else
    error "Laravel ne répond pas !"
fi

# Vérifier les permissions
if [ -w "storage/logs" ]; then
    log "✓ Permissions OK"
else
    warning "Problème de permissions sur storage/logs"
fi

###############################################################################
# 10. DÉSACTIVER LE MODE MAINTENANCE
###############################################################################

log "Désactivation du mode maintenance..."
$PHP_PATH artisan up

###############################################################################
# FINALISATION
###############################################################################

log "=== Déploiement terminé avec succès ==="
log "Version actuelle : $(git rev-parse --short HEAD)"
log "Backup disponible : $BACKUP_DIR/$BACKUP_NAME"
log ""
log "Pour accéder en mode maintenance : /{token}"
log "Pour voir les logs : tail -f storage/logs/laravel.log"

# Envoyer une notification (optionnel)
# curl -X POST "https://votre-webhook.com/deploy" -d "status=success&version=$(git rev-parse --short HEAD)"

exit 0
