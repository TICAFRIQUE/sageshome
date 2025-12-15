# 🚀 Guide de Déploiement Automatisé - Sages Home

## 📋 Vue d'ensemble

Ce guide explique comment mettre en place un déploiement automatisé pour l'application Sages Home sur le serveur de production.

**Chemin production :** `/home4/scisalyq/sageshome.ci`

## 🔧 Prérequis

### Sur le serveur de production

1. **SSH** configuré avec clé publique
2. **Git** installé
3. **PHP 8.2+** avec extensions requises
4. **Composer** installé globalement
5. **Supervisor** (optionnel mais recommandé pour les queues)
6. **Node.js & NPM** (si compilation d'assets)

### Sur votre machine de développement

1. **Git** configuré
2. Accès SSH au serveur
3. GitHub/GitLab configuré (pour CI/CD)

## 📦 Installation initiale

### 1. Configuration SSH

Sur votre machine locale :

```bash
# Générer une paire de clés SSH si nécessaire
ssh-keygen -t ed25519 -C "deploy@sageshome.ci"

# Copier la clé publique sur le serveur
ssh-copy-id scisalyq@votre-serveur.com

# Tester la connexion
ssh scisalyq@votre-serveur.com
```

### 2. Cloner le projet sur le serveur

```bash
# Se connecter au serveur
ssh scisalyq@votre-serveur.com

# Aller dans le répertoire
cd /home4/scisalyq

# Cloner (si pas déjà fait)
git clone git@github.com:votre-compte/sageshome.git sageshome.ci
cd sageshome.ci

# Installer les dépendances
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build

# Configurer l'environnement
cp .env.example .env
php artisan key:generate
nano .env  # Éditer avec les bonnes valeurs

# Permissions
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework storage/app

# Migrations
php artisan migrate --force

# Créer les dossiers nécessaires
mkdir -p /home4/scisalyq/backups
mkdir -p /home4/scisalyq/logs/supervisor
```

### 3. Rendre les scripts exécutables

```bash
cd /home4/scisalyq/sageshome.ci

# Rendre exécutables
chmod +x deploy.sh
chmod +x rollback.sh

# Tester le script de déploiement
./deploy.sh
```

## 🤖 Déploiement automatique avec GitHub Actions

### 1. Configuration des secrets GitHub

Dans votre dépôt GitHub, allez dans **Settings → Secrets and variables → Actions** et ajoutez :

- `SSH_PRIVATE_KEY` : Votre clé SSH privée (celle générée plus tôt)
- `SSH_HOST` : L'adresse de votre serveur (ex: `ssh.votre-serveur.com`)
- `SSH_USER` : Votre nom d'utilisateur SSH (ex: `scisalyq`)

### 2. Le workflow est déjà configuré

Le fichier `.github/workflows/deploy.yml` déploiera automatiquement :
- ✅ À chaque push sur la branche `main`
- ✅ Manuellement depuis l'onglet "Actions" de GitHub

### 3. Personnaliser le workflow

Éditez `.github/workflows/deploy.yml` selon vos besoins :

```yaml
# Déployer seulement sur des tags
on:
  push:
    tags:
      - 'v*'

# Ou seulement manuellement
on:
  workflow_dispatch:
```

## 🔄 Déploiement manuel

### Méthode 1 : Via SSH

```bash
# Depuis votre machine locale
ssh scisalyq@votre-serveur.com "cd /home4/scisalyq/sageshome.ci && bash deploy.sh"
```

### Méthode 2 : Directement sur le serveur

```bash
# Se connecter
ssh scisalyq@votre-serveur.com

# Déployer
cd /home4/scisalyq/sageshome.ci
./deploy.sh
```

### Méthode 3 : Via GitHub Actions (manuel)

1. Aller sur GitHub : **Actions** → **Deploy to Production**
2. Cliquer sur **Run workflow**
3. Confirmer

## 🔙 Rollback en cas de problème

Si un déploiement pose problème :

```bash
# Restaurer le dernier backup automatiquement
cd /home4/scisalyq/sageshome.ci
./rollback.sh

# Ou spécifier un backup précis
./rollback.sh backup-20251212-143000.tar.gz
```

## 📊 Configuration des Workers de Queue

> ⚠️ **Important** : Sur un hébergement mutualisé sans accès root (comme cPanel), utilisez la **Méthode 1** (Cron). Supervisor nécessite des privilèges root.

### Méthode 1 : Commande Artisan (Hébergement mutualisé - RECOMMANDÉ)

Cette méthode fonctionne sans accès root/sudo et est complètement intégrée à Laravel.

#### 1. Gérer le worker avec Artisan

```bash
cd /home4/scisalyq/sageshome.ci

# Démarrer le worker
php artisan queue:manage start

# Vérifier le statut
php artisan queue:manage status

# Redémarrer
php artisan queue:manage restart

# Arrêter
php artisan queue:manage stop
```

#### 2. Configurer le Cron pour surveillance automatique

Le worker s'arrête automatiquement après 1h (max-time=3600). Configurez un cron pour le relancer.

**Méthode A : Via script dédié (RECOMMANDÉ)**

```bash
# Rendre le script exécutable
cd /home4/scisalyq/sageshome.ci
chmod +x cron-queue-restart.sh

# Tester le script
./cron-queue-restart.sh

# Configurer le cron
crontab -e
```

Ajoutez :

```cron
# Redémarrer le worker de queue toutes les heures
0 * * * * /home4/scisalyq/sageshome.ci/cron-queue-restart.sh

# Laravel Scheduler (toutes les minutes)
* * * * * cd /home4/scisalyq/sageshome.ci && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Méthode B : Commande directe**

```bash
crontab -e
```

Ajoutez :

```cron
# Redémarrer le worker de queue toutes les heures
0 * * * * cd /home4/scisalyq/sageshome.ci && /usr/local/bin/php artisan queue:manage restart >> /home4/scisalyq/logs/cron-queue.log 2>&1

# Laravel Scheduler (toutes les minutes)
* * * * * cd /home4/scisalyq/sageshome.ci && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Via cPanel :**
1. Aller dans **Cron Jobs**
2. Ajouter une nouvelle tâche :
   - **Intervalle** : Toutes les heures (`0 * * * *`)
   - **Commande** : `/home4/scisalyq/sageshome.ci/cron-queue-restart.sh`

**Vérifier les logs du cron :**

```bash
tail -f /home4/scisalyq/logs/cron-queue.log
```

#### 3. Voir les logs du worker

```bash
# Voir les logs en temps réel
tail -f storage/logs/queue-worker.log

# Voir les dernières lignes
tail -20 storage/logs/queue-worker.log
```

### Méthode 2 : Supervisor (VPS/Serveur dédié uniquement)

⚠️ Nécessite un accès root/sudo. N'utilisez cette méthode que si vous avez un VPS ou serveur dédié.

#### 1. Installer le fichier de configuration

```bash
# Copier la config
sudo cp /home4/scisalyq/sageshome.ci/supervisor-sageshome.conf /etc/supervisor/conf.d/

# Adapter les chemins si nécessaire
sudo nano /etc/supervisor/conf.d/supervisor-sageshome.conf

# Recharger
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sageshome-worker:*
```

#### 2. Vérifier le statut

```bash
sudo supervisorctl status sageshome-worker:*
```

#### 3. Gérer les workers

```bash
# Redémarrer
sudo supervisorctl restart sageshome-worker:*

# Arrêter
sudo supervisorctl stop sageshome-worker:*

# Voir les logs
sudo tail -f /home4/scisalyq/logs/supervisor/sageshome-worker.log
```

## 🔍 Monitoring et logs

### Logs de déploiement

```bash
# Voir les dernières opérations
cat /home4/scisalyq/sageshome.ci/storage/logs/laravel.log | grep "Deployment"
```

### Logs applicatifs

```bash
# Logs Laravel en temps réel
tail -f /home4/scisalyq/sageshome.ci/storage/logs/laravel.log

# Logs des workers
tail -f /home4/scisalyq/logs/supervisor/sageshome-worker.log
```

### Vérifier l'état des queues

```bash
cd /home4/scisalyq/sageshome.ci
php artisan queue:monitor
php artisan queue:failed
```

## 🔐 Sécurité

### 1. Permissions des fichiers sensibles

```bash
chmod 600 /home4/scisalyq/sageshome.ci/.env
chmod 600 /home4/scisalyq/.ssh/id_ed25519
```

### 2. Exclure les fichiers sensibles du Git

Vérifier `.gitignore` :

```
.env
.env.backup
.env.production
*.log
storage/
vendor/
node_modules/
```

### 3. Protéger les scripts

```bash
chmod 700 /home4/scisalyq/sageshome.ci/deploy.sh
chmod 700 /home4/scisalyq/sageshome.ci/rollback.sh
```

## 📅 Tâches planifiées (Cron)

Pour les tâches Laravel Scheduler :

```bash
crontab -e
```

Ajouter :

```cron
* * * * * cd /home4/scisalyq/sageshome.ci && php artisan schedule:run >> /dev/null 2>&1
```

## 🚨 Dépannage

### Le déploiement échoue

1. **Vérifier les logs** :
```bash
cd /home4/scisalyq/sageshome.ci
cat storage/logs/laravel.log | tail -50
```

2. **Tester manuellement chaque étape** :
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan cache:clear
php artisan config:cache
```

3. **Rollback si nécessaire** :
```bash
./rollback.sh
```

### Les queues ne fonctionnent pas

#### Sur hébergement mutualisé (sans sudo)

1. **Vérifier que le worker tourne** :
```bash
cd /home4/scisalyq/sageshome.ci
php artisan queue:manage status
```

2. **Redémarrer le worker** :
```bash
php artisan queue:manage restart
```

3. **Vérifier les logs** :
```bash
tail -f storage/logs/queue-worker.log
```

4. **Voir les jobs échoués** :
```bash
php artisan queue:failed
php artisan queue:retry all
```

5. **Vérifier le cron** :
```bash
crontab -l  # Voir les crons configurés
```

#### Sur VPS/Serveur dédié (avec supervisor)

1. **Vérifier supervisor** :
```bash
sudo supervisorctl status
```

2. **Redémarrer les workers** :
```bash
sudo supervisorctl restart sageshome-worker:*
php artisan queue:restart
```

3. **Voir les jobs échoués** :
```bash
php artisan queue:failed
php artisan queue:retry all
```

### Problèmes de permissions

```bash
cd /home4/scisalyq/sageshome.ci
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs storage/framework storage/app
```

## ✅ Checklist de déploiement

Avant chaque déploiement :

- [ ] Tests passent en local
- [ ] `.env` est correctement configuré en production
- [ ] Migrations testées en développement
- [ ] Backup récent disponible
- [ ] Mode maintenance prévu si gros changements
- [ ] Workers de queue redémarrés après déploiement

Après le déploiement :

- [ ] Site accessible
- [ ] Logs sans erreurs critiques
- [ ] Queues fonctionnelles
- [ ] Emails envoyés correctement
- [ ] Paiements testés (sandbox)

## 📞 Support

En cas de problème :

1. Consulter les logs
2. Tester le rollback
3. Vérifier la documentation Laravel
4. Contacter l'équipe technique

---

**Dernière mise à jour :** 12 décembre 2025
