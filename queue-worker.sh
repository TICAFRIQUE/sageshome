#!/bin/bash

###############################################################################
# Script de gestion du worker de queue - Hébergement mutualisé
# Sans accès root/sudo
###############################################################################

APP_DIR="/home4/scisalyq/sageshome.ci"
PHP_PATH="/usr/local/bin/php"
PID_FILE="$APP_DIR/storage/queue-worker.pid"
LOG_FILE="$APP_DIR/storage/logs/queue-worker.log"

# Fonction pour démarrer le worker
start_worker() {
    if [ -f "$PID_FILE" ]; then
        PID=$(cat "$PID_FILE")
        if ps -p $PID > /dev/null 2>&1; then
            echo "Worker déjà en cours d'exécution (PID: $PID)"
            return 1
        else
            echo "Fichier PID obsolète, nettoyage..."
            rm -f "$PID_FILE"
        fi
    fi

    echo "Démarrage du worker de queue..."
    cd "$APP_DIR"
    
    # Démarrer le worker en arrière-plan
    nohup $PHP_PATH artisan queue:work database \
        --sleep=3 \
        --tries=3 \
        --max-time=3600 \
        --timeout=60 \
        >> "$LOG_FILE" 2>&1 &
    
    # Sauvegarder le PID
    echo $! > "$PID_FILE"
    echo "Worker démarré avec succès (PID: $(cat $PID_FILE))"
}

# Fonction pour arrêter le worker
stop_worker() {
    if [ ! -f "$PID_FILE" ]; then
        echo "Aucun worker en cours d'exécution"
        return 1
    fi

    PID=$(cat "$PID_FILE")
    if ps -p $PID > /dev/null 2>&1; then
        echo "Arrêt du worker (PID: $PID)..."
        kill $PID
        sleep 2
        
        # Forcer si toujours actif
        if ps -p $PID > /dev/null 2>&1; then
            echo "Arrêt forcé..."
            kill -9 $PID
        fi
        
        rm -f "$PID_FILE"
        echo "Worker arrêté avec succès"
    else
        echo "Worker non actif, nettoyage du PID"
        rm -f "$PID_FILE"
    fi
}

# Fonction pour redémarrer le worker
restart_worker() {
    echo "Redémarrage du worker..."
    stop_worker
    sleep 2
    start_worker
}

# Fonction pour vérifier le statut
status_worker() {
    if [ ! -f "$PID_FILE" ]; then
        echo "❌ Worker non démarré"
        return 1
    fi

    PID=$(cat "$PID_FILE")
    if ps -p $PID > /dev/null 2>&1; then
        echo "✅ Worker en cours d'exécution (PID: $PID)"
        ps -p $PID -o pid,etime,cmd
        return 0
    else
        echo "❌ Worker arrêté (PID obsolète: $PID)"
        rm -f "$PID_FILE"
        return 1
    fi
}

# Menu principal
case "$1" in
    start)
        start_worker
        ;;
    stop)
        stop_worker
        ;;
    restart)
        restart_worker
        ;;
    status)
        status_worker
        ;;
    logs)
        tail -f "$LOG_FILE"
        ;;
    *)
        echo "Usage: $0 {start|stop|restart|status|logs}"
        echo ""
        echo "Commandes disponibles:"
        echo "  start   - Démarrer le worker de queue"
        echo "  stop    - Arrêter le worker de queue"
        echo "  restart - Redémarrer le worker de queue"
        echo "  status  - Vérifier l'état du worker"
        echo "  logs    - Afficher les logs en temps réel"
        exit 1
        ;;
esac

exit 0
