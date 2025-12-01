# Script de vérification pour la production (Windows PowerShell)
# À exécuter avant de déployer en production

Write-Host "=== VÉRIFICATION DES MIGRATIONS POUR LA PRODUCTION ===" -ForegroundColor Green

Write-Host "`n1. Vérification du statut des migrations..." -ForegroundColor Yellow
php artisan migrate:status

Write-Host "`n2. Test des migrations en mode dry-run..." -ForegroundColor Yellow
php artisan migrate --pretend

Write-Host "`n3. Vérification de la cohérence de la base de données..." -ForegroundColor Yellow
$checkScript = @"
`$tables = ['users', 'residences', 'bookings', 'payments', 'residence_types'];
foreach (`$tables as `$table) {
    if (Schema::hasTable(`$table)) {
        echo "✓ Table `$table existe\n";
    } else {
        echo "✗ Table `$table manquante\n";
    }
}

// Vérifier les contraintes FK
`$fks = DB::select('SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME IS NOT NULL AND TABLE_SCHEMA = DATABASE()');
echo "Contraintes de clés étrangères: " . count(`$fks) . "\n";
"@

php artisan tinker --execute="$checkScript"

Write-Host "`n=== COMMANDES RECOMMANDÉES POUR LA PRODUCTION ===" -ForegroundColor Green
Write-Host "1. Sauvegarde: mysqldump -u username -p database_name > backup_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql" -ForegroundColor Cyan
Write-Host "2. Migration: php artisan migrate --step" -ForegroundColor Cyan
Write-Host "3. Vérification: php artisan migrate:status" -ForegroundColor Cyan
Write-Host "4. Rollback si problème: php artisan migrate:rollback --step=1" -ForegroundColor Cyan

Write-Host "`n=== ORDRE OPTIMAL DES MIGRATIONS ===" -ForegroundColor Green
Write-Host "Voir le fichier MIGRATIONS_GUIDE.md pour l'ordre détaillé" -ForegroundColor White

Write-Host "`n=== RESUME DES CORRECTIONS APPORTEES ===" -ForegroundColor Green
Write-Host "- Suppression des migrations en doublon" -ForegroundColor Green
Write-Host "- Correction des IDs utilisateurs vides" -ForegroundColor Green
Write-Host "- Reorganisation de l'ordre des migrations" -ForegroundColor Green
Write-Host "- Ajout de verifications de coherence" -ForegroundColor Green
Write-Host "- Correction des contraintes de cles etrangeres" -ForegroundColor Green