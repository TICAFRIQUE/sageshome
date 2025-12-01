#!/bin/bash

# Script de préparation pour la production
# À exécuter avant de deployer en production

echo "=== VÉRIFICATION DES MIGRATIONS POUR LA PRODUCTION ==="

echo "1. Vérification du statut des migrations..."
php artisan migrate:status

echo -e "\n2. Test des migrations en mode dry-run..."
php artisan migrate --pretend

echo -e "\n3. Vérification de la cohérence de la base de données..."
php artisan tinker --execute="
\$tables = ['users', 'residences', 'bookings', 'payments', 'residence_types'];
foreach (\$tables as \$table) {
    if (Schema::hasTable(\$table)) {
        echo \"✓ Table \$table existe\n\";
    } else {
        echo \"✗ Table \$table manquante\n\";
    }
}

// Vérifier les contraintes FK
\$fks = DB::select('SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME IS NOT NULL AND TABLE_SCHEMA = DATABASE()');
echo \"Contraintes de clés étrangères: \" . count(\$fks) . \"\n\";
"

echo -e "\n4. Vérification des seeders..."
php artisan db:seed --class=DatabaseSeeder --pretend 2>/dev/null || echo "Seeders non disponibles en mode pretend"

echo -e "\n=== COMMANDES RECOMMANDÉES POUR LA PRODUCTION ==="
echo "1. Sauvegarde: mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql"
echo "2. Migration: php artisan migrate --step"
echo "3. Vérification: php artisan migrate:status"
echo "4. Rollback si problème: php artisan migrate:rollback --step=1"

echo -e "\n=== ORDRE OPTIMAL DES MIGRATIONS ==="
echo "Voir le fichier MIGRATIONS_GUIDE.md pour l'ordre détaillé"