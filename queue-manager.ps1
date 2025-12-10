# Script PowerShell pour gérer le worker de queue Laravel
param(
    [Parameter(Position=0)]
    [ValidateSet("start", "stop", "restart", "status")]
    [string]$Action = "start"
)

$WorkerName = "laravel_queue_worker"

function Start-QueueWorker {
    Write-Host "Démarrage du worker de queue..." -ForegroundColor Green
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "php artisan queue:work --tries=3 --timeout=90 --sleep=3 --max-jobs=1000" -WindowStyle Normal
    Write-Host "Worker démarré avec succès!" -ForegroundColor Green
}

function Stop-QueueWorker {
    Write-Host "Arrêt du worker de queue..." -ForegroundColor Yellow
    Get-Process | Where-Object {$_.ProcessName -eq "php" -and $_.CommandLine -like "*queue:work*"} | Stop-Process -Force
    Write-Host "Worker arrêté!" -ForegroundColor Yellow
}

function Restart-QueueWorker {
    Stop-QueueWorker
    Start-Sleep -Seconds 2
    Start-QueueWorker
}

function Show-QueueStatus {
    Write-Host "Vérification du statut du worker..." -ForegroundColor Cyan
    $processes = Get-Process | Where-Object {$_.ProcessName -eq "php"}
    if ($processes) {
        Write-Host "Workers PHP en cours d'exécution:" -ForegroundColor Green
        $processes | Format-Table Id, ProcessName, StartTime -AutoSize
    } else {
        Write-Host "Aucun worker en cours d'exécution." -ForegroundColor Red
    }
}

# Exécuter l'action demandée
switch ($Action) {
    "start" { Start-QueueWorker }
    "stop" { Stop-QueueWorker }
    "restart" { Restart-QueueWorker }
    "status" { Show-QueueStatus }
}
