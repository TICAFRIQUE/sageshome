<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ManageQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:manage {action : start|stop|restart|status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gérer le worker de queue (start, stop, restart, status)';

    protected $pidFile;
    protected $logFile;

    public function __construct()
    {
        parent::__construct();
        $this->pidFile = storage_path('queue-worker.pid');
        $this->logFile = storage_path('logs/queue-worker.log');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'start':
                return $this->startWorker();
            case 'stop':
                return $this->stopWorker();
            case 'restart':
                return $this->restartWorker();
            case 'status':
                return $this->checkStatus();
            default:
                $this->error("Action invalide. Utilisez : start, stop, restart ou status");
                return 1;
        }
    }

    /**
     * Démarrer le worker
     */
    protected function startWorker()
    {
        // Vérifier si un worker est déjà en cours
        if (file_exists($this->pidFile)) {
            $pid = (int) file_get_contents($this->pidFile);
            if ($this->isProcessRunning($pid)) {
                $this->warn("Worker déjà en cours d'exécution (PID: $pid)");
                return 1;
            } else {
                $this->info("Fichier PID obsolète détecté, nettoyage...");
                @unlink($this->pidFile);
            }
        }

        $this->info("Démarrage du worker de queue...");

        // Construire la commande
        $phpBinary = PHP_BINARY;
        $artisan = base_path('artisan');
        $logFile = $this->logFile;

        // Commande pour démarrer le worker en arrière-plan
        if (PHP_OS_FAMILY === 'Windows') {
            // Windows - utilise start /B pour arrière-plan
            $command = sprintf(
                'start /B %s %s queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=60 > %s 2>&1',
                escapeshellarg($phpBinary),
                escapeshellarg($artisan),
                escapeshellarg($logFile)
            );
            pclose(popen($command, 'r'));
            
            // Sur Windows, difficile de récupérer le PID, on utilise un marqueur
            file_put_contents($this->pidFile, getmypid());
        } else {
            // Linux/Unix
            $command = sprintf(
                'nohup %s %s queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=60 >> %s 2>&1 & echo $!',
                $phpBinary,
                $artisan,
                $logFile
            );
            
            $output = [];
            exec($command, $output);
            $pid = trim(end($output));
            
            if (!empty($pid) && is_numeric($pid)) {
                file_put_contents($this->pidFile, $pid);
                $this->info("Worker démarré avec succès (PID: $pid)");
            } else {
                $this->error("Erreur lors du démarrage du worker");
                $this->error("Commande : $command");
                return 1;
            }
        }

        Log::info('Queue worker démarré via commande artisan', ['pid' => $pid ?? 'unknown']);
        $this->info("✅ Worker démarré avec succès");
        $this->info("📄 Logs : $logFile");
        
        return 0;
    }

    /**
     * Arrêter le worker
     */
    protected function stopWorker()
    {
        if (!file_exists($this->pidFile)) {
            $this->warn("Aucun worker en cours d'exécution");
            return 1;
        }

        $pid = (int) file_get_contents($this->pidFile);

        if (!$this->isProcessRunning($pid)) {
            $this->warn("Worker non actif (PID obsolète: $pid), nettoyage...");
            @unlink($this->pidFile);
            return 1;
        }

        $this->info("Arrêt du worker (PID: $pid)...");

        if (PHP_OS_FAMILY === 'Windows') {
            // Windows
            exec("taskkill /PID $pid /F 2>&1", $output, $result);
        } else {
            // Linux/Unix
            posix_kill($pid, SIGTERM);
            sleep(2);
            
            // Forcer si toujours actif
            if ($this->isProcessRunning($pid)) {
                $this->warn("Arrêt forcé du processus...");
                posix_kill($pid, SIGKILL);
            }
        }

        @unlink($this->pidFile);
        Log::info('Queue worker arrêté via commande artisan');
        $this->info("✅ Worker arrêté avec succès");
        
        return 0;
    }

    /**
     * Redémarrer le worker
     */
    protected function restartWorker()
    {
        $this->info("Redémarrage du worker...");
        $this->stopWorker();
        sleep(2);
        return $this->startWorker();
    }

    /**
     * Vérifier le statut du worker
     */
    protected function checkStatus()
    {
        if (!file_exists($this->pidFile)) {
            $this->error("❌ Worker non démarré");
            $this->info("\n💡 Pour démarrer : php artisan queue:manage start");
            return 1;
        }

        $pid = (int) file_get_contents($this->pidFile);

        if ($this->isProcessRunning($pid)) {
            $this->info("✅ Worker en cours d'exécution");
            $this->table(
                ['PID', 'Fichier Log'],
                [[$pid, $this->logFile]]
            );

            // Afficher les dernières lignes du log
            if (file_exists($this->logFile)) {
                $this->info("\n📄 Dernières lignes du log :");
                $lines = array_slice(file($this->logFile), -10);
                foreach ($lines as $line) {
                    $this->line(trim($line));
                }
            }

            return 0;
        } else {
            $this->error("❌ Worker arrêté (PID obsolète: $pid)");
            @unlink($this->pidFile);
            $this->info("\n💡 Pour démarrer : php artisan queue:manage start");
            return 1;
        }
    }

    /**
     * Vérifier si un processus est en cours d'exécution
     */
    protected function isProcessRunning($pid)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            exec("tasklist /FI \"PID eq $pid\" 2>NUL", $output);
            return count($output) > 1;
        } else {
            return posix_kill($pid, 0);
        }
    }
}
