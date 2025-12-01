# ORDRE CONSOLIDÉ DES MIGRATIONS - VERSION FINALE

## Migrations de base (ordre critique)
1. `0001_01_01_000000_create_users_table.php` - Table users complète avec profil
2. `0001_01_01_000001_create_cache_table.php` - Cache Laravel
3. `0001_01_01_000002_create_jobs_table.php` - Jobs Laravel
4. `2025_04_14_114341_create_modules_table.php` - Modules système
5. `2025_04_15_113500_create_media_table.php` - Médias
6. `2025_04_15_113620_create_permission_tables.php` - Permissions (Spatie)
7. `2025_04_15_120105_create_parametres_table.php` - Paramètres système

## Migrations core business (ordre critique)
8. `2025_11_25_235959_create_residence_types_table.php` - Types de résidences avec slug
9. `2025_11_26_000001_create_residences_table.php` - Résidences complètes avec géolocalisation et FK
10. `2025_11_26_000002_create_bookings_table.php` - Réservations complètes avec tous les champs
11. `2025_11_26_000003_create_payments_table.php` - Paiements complets avec champs Wave et FK
12. `2025_11_26_000004_create_residence_images_table.php` - Images avec FK
13. `2025_11_26_000005_create_availability_calendar_table.php` - Calendrier avec FK

## Migrations secondaires
14. `2025_12_01_090339_create_currency_rates_table.php` - Taux de change

## AVANTAGES DE CETTE ORGANISATION

### ✅ **Tables complètes dès le début**
- Chaque table est créée avec TOUS ses champs
- Plus de migrations d'ajout de colonnes
- Structure finale claire dès la création

### ✅ **Contraintes FK intégrées**
- Toutes les clés étrangères créées avec les tables
- Respect de l'ordre des dépendances
- Plus d'erreurs de contraintes

### ✅ **Types de données cohérents**
- `users.id` : string(15) dès le début
- `bookings.user_id` : string(15) pour correspondre
- Plus de conversions bigint -> string

### ✅ **Index optimisés**
- Tous les index créés avec les tables
- Optimisations de recherche intégrées

## DÉTAIL DES TABLES CONSOLIDÉES

### 🏠 **residences** (complète)
```sql
- id, name, slug, description, full_description
- residence_type_id (FK vers residence_types)
- capacity, price_per_night, amenities
- address, ville, commune (géolocalisation)
- latitude, longitude, google_maps_url
- is_available, is_featured, sort_order
- timestamps, soft deletes
```

### 📋 **bookings** (complète) 
```sql
- id, booking_number, user_id (string FK)
- residence_id (string FK)
- first_name, last_name, email, phone, country (client)
- check_in, check_out, check_in_date, check_out_date (dates + alias)
- guests, guests_count (nombre + alias)
- price_per_night, total_price, subtotal_amount (prix + alias)
- tax_amount, final_amount, total_amount
- status, payment_status
- special_requests, notes
- confirmed_at, cancelled_at, timestamps, soft deletes
```

### 💳 **payments** (complète)
```sql
- id, booking_id (FK), payment_reference
- payment_method, amount, status
- payment_details, transaction_id, processed_at
- payment_data (Wave JSON), completed_at (Wave)
- failure_reason, timestamps, soft deletes
```

### 👤 **users** (complète)
```sql
- id (string 15 chars), username, phone, email
- email_verified_at, password, avatar, role
- address, city, country (profil)
- remember_token, timestamps, soft deletes
```

## COMMANDES POUR MIGRATION PROPRE

### Fresh install (DEV/TEST uniquement)
```bash
php artisan migrate:fresh --seed
```

### Production (migration étape par étape)
```bash
php artisan migrate:status
php artisan migrate --step
```

### Rollback si nécessaire
```bash
php artisan migrate:rollback --step=1
```

## MIGRATIONS SUPPRIMÉES (consolidées)

- ❌ Tous les `add_*_to_*_table.php`
- ❌ Tous les `fix_*_table.php`
- ❌ Tous les `update_*_table.php`

**Résultat : 14 migrations au lieu de 24+ !** 🎉