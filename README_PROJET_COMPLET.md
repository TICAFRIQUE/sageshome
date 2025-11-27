# 🏨 Sages Home - Plateforme de Réservation de Résidences de Luxe

## 📋 Résumé du Projet

**Sages Home** est une plateforme de réservation en ligne complète développée avec Laravel, permettant aux clients de rechercher, consulter et réserver des résidences de luxe avec plusieurs options de paiement.

---

## ✅ Fonctionnalités Implémentées

### 🎨 **Interface Utilisateur**
- ✅ Design avec gradient doré (#F2D18A → #C29B32) et accents verts (#2F4A33)
- ✅ Typographie premium (Poppins + Inter)
- ✅ Interface responsive avec Bootstrap 5
- ✅ Navigation intuitive avec menu utilisateur

### 🏠 **Gestion des Résidences**
- ✅ Affichage des résidences avec images multiples
- ✅ Système de recherche par dates de disponibilité
- ✅ Pages de détails avec galerie d'images
- ✅ Calendrier de disponibilité intégré
- ✅ Descriptions et équipements détaillés

### 📅 **Système de Réservation**
- ✅ Recherche de disponibilité par plage de dates
- ✅ Calcul automatique des prix et nuitées
- ✅ Formulaire de réservation complet
- ✅ Gestion des statuts de réservation (pending, confirmed, cancelled)

### 💳 **Systèmes de Paiement**
- ✅ **Wave** : Simulation d'interface de paiement mobile
- ✅ **PayPal** : Interface de paiement internationale
- ✅ **Espèces** : Option de paiement à l'arrivée
- ✅ Confirmations de paiement automatiques

### 👤 **Authentification**
- ✅ Inscription et connexion utilisateurs
- ✅ Gestion de profil utilisateur
- ✅ Historique des réservations
- ✅ Déconnexion sécurisée

### 📧 **Notifications**
- ✅ Configuration pour envoi d'emails
- ✅ Templates d'emails de confirmation
- ✅ Notifications de statut de réservation

### 🔧 **Interface d'Administration**
- ✅ Dashboard administratif avec statistiques
- ✅ Gestion des résidences (CRUD complet)
- ✅ Gestion des réservations
- ✅ Suivi des paiements et revenus
- ✅ Navigation dédiée pour administrateurs

---

## 🗂️ **Structure de Base de Données**

### Tables Principales :
- **users** : Gestion des utilisateurs
- **residences** : Informations des résidences
- **residence_images** : Images multiples par résidence
- **availability_calendar** : Calendrier de disponibilité
- **bookings** : Réservations des clients
- **payments** : Transactions et paiements

---

## 🚀 **Technologies Utilisées**

- **Framework** : Laravel 11
- **Frontend** : Bootstrap 5 + CSS personnalisé
- **Base de données** : MySQL avec migrations
- **Authentification** : Laravel Breeze personnalisé
- **Gestion d'images** : Système de fichiers Laravel
- **IDs personnalisés** : IdGenerator pour identifiants uniques

---

## 🛠️ **Configuration et Installation**

### Prérequis :
- PHP 8.3+
- Composer
- MySQL/MariaDB
- Node.js (pour la compilation des assets)

### Installation :
```bash
# Clone du projet
git clone [repository]

# Installation des dépendances
composer install
npm install

# Configuration
cp .env.example .env
php artisan key:generate

# Base de données
php artisan migrate
php artisan db:seed

# Démarrage du serveur
php artisan serve
```

---

## 👥 **Comptes de Test**

### Administrateur (Système Backend) :
- **URL d'accès** : `http://localhost:8000/admin/login`
- **Email** : `admin.sages@gmail.com`
- **Mot de passe** : `password123`
- **Rôle** : `administrateur`
- **Accès** : Interface d'administration backend complète avec module Sages Home

### Développeur (si nécessaire) :
- Vous pouvez créer un compte développeur avec le rôle `developpeur` via l'interface admin
- Accès complet aux paramètres et configurations

### Client Test :
- Inscription disponible via l'interface frontend
- Accès aux réservations et historique sur le site principal

---

## 🎯 **Points Clés Résolus**

1. ✅ **Problème de table** : Correction de la relation AvailabilityCalendar avec nom de table explicite
2. ✅ **Design cohérent** : Implémentation complète de la charte graphique
3. ✅ **Paiements multiples** : Trois méthodes fonctionnelles (Wave, PayPal, Espèces)
4. ✅ **Interface admin** : Dashboard complet avec statistiques en temps réel
5. ✅ **Responsive design** : Compatible mobile et desktop

---

## 📈 **Fonctionnalités Avancées**

- **Recherche avancée** : Filtrage par dates, prix, type de résidence
- **Galerie d'images** : Gestion multiple d'images par résidence
- **Calculs automatiques** : Prix, taxes, durée de séjour
- **Statut en temps réel** : Suivi des réservations et paiements
- **Interface intuitive** : Navigation fluide et expérience utilisateur optimisée

---

## 🔐 **Sécurité**

- Authentification Laravel sécurisée
- Protection CSRF sur tous les formulaires
- Validation des données côté serveur
- Gestion des sessions utilisateur
- Accès administrateur conditionnel

---

## 🌟 **URL d'Accès**

- **Site principal** : http://localhost:8000
- **Administration** : http://localhost:8000/admin (après connexion admin)
- **API potentielle** : Structure préparée pour développement API

---

## 📞 **Support et Contact**

Plateforme développée pour **Sages Home** - Résidences de luxe au Sénégal
Interface complète de réservation avec paiements Wave, PayPal et espèces.

**Statut** : ✅ **PROJET COMPLET ET FONCTIONNEL**