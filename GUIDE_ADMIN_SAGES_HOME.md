# 🔐 Guide d'Administration - Sages Home (Intégré)

## ✅ **Système Intégré à votre Architecture Existante**

Le module Sages Home a été **complètement intégré** à votre système d'administration existant :
- **Middleware admin** : Utilise votre middleware `admin` existant
- **DashboardController** : Étendu pour inclure les fonctionnalités Sages Home
- **Contrôleurs backend** : Tous dans le namespace `backend` pour la cohérence
- **Routes protégées** : Toutes les routes Sages Home utilisent le middleware admin

## Connexion à l'Interface d'Administration

### 1. Accès à l'interface admin (inchangé)
```
URL: http://localhost:8000/admin/login
Email: admin.sages@gmail.com
Mot de passe: password123
Rôle: administrateur
```

### 2. Navigation vers Sages Home
Une fois connecté via votre système admin habituel :
1. Dans la sidebar gauche, cliquez sur **"SAGES HOME"**
2. Le menu se déploie avec les options :
   - **Dashboard** : Statistiques Sages Home spécifiques
   - **Résidences** : Gestion des propriétés
   - **Réservations** : Suivi des bookings
   - **Voir le site** : Accès au site public

## 🏗️ **Architecture Technique**

### Contrôleurs Utilisés :
- **`backend\DashboardController`** : Dashboard principal + méthode `sagesHomeDashboard()`
- **`backend\ResidenceController`** : Gestion des résidences
- **`backend\BookingController`** : Gestion des réservations
- **`backend\AdminController`** : Authentification (inchangé)

### Middleware :
- **Middleware `admin`** : Contrôle d'accès basé sur `Auth::user()->role !== 'client'`
- **Routes protégées** : Toutes les routes `/admin/sages-home/*` sont protégées

### Base de Données :
- **Tables Sages Home** : Intégrées à votre BDD existante
- **Pas de conflit** : Aucun impact sur vos tables existantes
- **Relations propres** : Tables indépendantes avec clés étrangères appropriées

## 📊 Dashboard Sages Home

### Statistiques affichées :
- **Total des résidences** : Nombre de propriétés disponibles
- **Total des réservations** : Nombre de bookings effectués
- **Revenus totaux** : Montant des paiements confirmés
- **Réservations récentes** : Les 10 dernières réservations
- **Statuts des réservations** : Répartition par statut

## 🏠 Gestion des Résidences

### Fonctionnalités disponibles :
- **Liste des résidences** : Vue d'ensemble de toutes les propriétés
- **Ajouter une résidence** : Création de nouvelles propriétés
- **Modifier une résidence** : Mise à jour des informations
- **Supprimer une résidence** : Retrait du catalogue
- **Gestion des images** : Upload et organisation des photos
- **Calendrier de disponibilité** : Gestion des périodes disponibles

## 📅 Gestion des Réservations

### Fonctionnalités disponibles :
- **Liste des réservations** : Toutes les réservations avec filtres
- **Détails d'une réservation** : Informations complètes du booking
- **Gestion des statuts** : 
  - `pending` : En attente de confirmation
  - `confirmed` : Réservation confirmée
  - `cancelled` : Réservation annulée
- **Confirmation des paiements** : Validation des transactions
- **Vue calendrier** : Planning des réservations

## 💳 Suivi des Paiements

### Méthodes de paiement supportées :
- **Wave** : Paiement mobile (Sénégal)
- **PayPal** : Paiement international
- **Espèces** : Paiement à l'arrivée

### Statuts des paiements :
- `pending` : En attente
- `completed` : Paiement confirmé
- `failed` : Échec du paiement

## 👥 Gestion des Utilisateurs (Existant)

Votre système d'administration existant permet déjà :
- Création d'utilisateurs admin
- Attribution de rôles (`administrateur`, `developpeur`)
- Gestion des permissions
- Profils utilisateurs

## 🛠️ Rôles et Permissions

### Rôles disponibles :
- **`administrateur`** : Accès complet à Sages Home
- **`developpeur`** : Accès technique et configuration
- **`superadmin`** : Accès total au système

### Permissions pour Sages Home :
Les utilisateurs avec les rôles `administrateur` et `developpeur` ont accès au module Sages Home via la sidebar.

## 📱 Accès Mobile

L'interface d'administration est responsive et accessible sur mobile/tablette via le même lien.

## 🔧 Configuration

### Base de données :
- Tables Sages Home intégrées à votre BDD existante
- Migrations exécutées automatiquement
- Seeders pour données de test

### Paramètres :
Le module Sages Home utilise votre système de paramètres existant pour :
- Configuration email
- Paramètres généraux
- Logo et branding

---

## 📞 Support Technique

Pour toute assistance technique ou configuration supplémentaire, référez-vous à votre système d'administration existant qui gère déjà :
- Logs système
- Configuration serveur
- Maintenance base de données
- Sauvegarde et restauration