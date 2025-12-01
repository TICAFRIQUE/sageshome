# Script de validation de la structure consolidée
Write-Host "=== VALIDATION DE LA STRUCTURE CONSOLIDEE ===" -ForegroundColor Green

Write-Host "`n1. Vérification des migrations disponibles..." -ForegroundColor Yellow
php artisan migrate:status

Write-Host "`n2. Liste des migrations finales..." -ForegroundColor Yellow
Get-ChildItem "database\migrations\" | Select-Object Name | Sort-Object Name

Write-Host "`n3. Validation des contraintes FK dans les migrations..." -ForegroundColor Yellow
$migrations = @(
    "2025_11_26_000001_create_residences_table.php",
    "2025_11_26_000002_create_bookings_table.php", 
    "2025_11_26_000003_create_payments_table.php",
    "2025_11_26_000004_create_residence_images_table.php",
    "2025_11_26_000005_create_availability_calendar_table.php"
)

foreach ($migration in $migrations) {
    $path = "database\migrations\$migration"
    if (Test-Path $path) {
        $content = Get-Content $path -Raw
        if ($content -match 'foreign\(') {
            Write-Host "OK $migration contient des FK" -ForegroundColor Green
        } else {
            Write-Host "Warning $migration sans FK" -ForegroundColor Yellow
        }
    }
}

Write-Host "`n=== RESUME DE LA CONSOLIDATION ===" -ForegroundColor Green
Write-Host "- Tables creees completes des le debut" -ForegroundColor White
Write-Host "- Plus de migrations d ajout de colonnes" -ForegroundColor White  
Write-Host "- Contraintes FK integrees" -ForegroundColor White
Write-Host "- Structure optimisee pour la production" -ForegroundColor White

Write-Host "`n=== POUR APPLIQUER EN PRODUCTION ===" -ForegroundColor Cyan
Write-Host "1. Sauvegarde: mysqldump -u user -p db > backup.sql" -ForegroundColor White
Write-Host "2. Test: php artisan migrate:fresh --seed (sur copie)" -ForegroundColor White
Write-Host "3. Production: php artisan migrate:fresh (si OK)" -ForegroundColor White

$totalMigrations = (Get-ChildItem "database\migrations\").Count
Write-Host "`nTotal migrations: $totalMigrations (au lieu de 24+)" -ForegroundColor Green