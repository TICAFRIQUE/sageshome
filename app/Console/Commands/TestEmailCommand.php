<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\EmailService;
use Illuminate\Console\Command;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {type=client}';
    protected $description = 'Tester l\'envoi d\'emails avec le service Email (client ou admin)';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $type = $this->argument('type');
        
        // Récupérer une réservation pour le test
        $booking = Booking::with('residence')->latest()->first();
        
        if (!$booking) {
            $this->error('Aucune réservation trouvée pour le test');
            return 1;
        }

        $this->info('Test d\'envoi d\'email avec EmailService...');
        $this->info('Réservation: #' . $booking->booking_number);
        $this->info('Configuration SMTP:');
        $this->info('  Host: ' . env('MAIL_HOST'));
        $this->info('  Port: ' . env('MAIL_PORT'));
        $this->info('  Username: ' . env('MAIL_USERNAME'));
        $this->info('  Encryption: ' . env('MAIL_ENCRYPTION'));
        $this->newLine();

        try {
            if ($type === 'admin') {
                $this->info('Type: Email Admin (Notification)');
                $result = $this->emailService->sendNewBookingNotification($booking);
            } else {
                $this->info('Type: Email Client (Confirmation)');
                $result = $this->emailService->sendBookingConfirmation($booking);
            }

            $this->newLine();
            if ($result) {
                $this->success('✓ Email envoyé avec succès à ' . ($type === 'admin' ? env('MAIL_FROM_ADDRESS') : $booking->email));
            } else {
                $this->error('✗ Échec de l\'envoi de l\'email');
                return 1;
            }
            
            return 0;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('✗ Erreur lors de l\'envoi de l\'email:');
            $this->error($e->getMessage());
            
            return 1;
        }
    }

    private function success($message)
    {
        $this->getOutput()->writeln("<fg=green>$message</>");
    }
}
