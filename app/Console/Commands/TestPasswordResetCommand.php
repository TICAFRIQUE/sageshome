<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EmailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestPasswordResetCommand extends Command
{
    protected $signature = 'password:test-reset {email?}';
    protected $description = 'Tester l\'envoi d\'email de réinitialisation de mot de passe';

    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    public function handle()
    {
        $email = $this->argument('email');
        
        if (!$email) {
            // Prendre le premier utilisateur
            $user = User::first();
            if (!$user) {
                $this->error('Aucun utilisateur trouvé dans la base de données');
                return 1;
            }
            $email = $user->email;
        } else {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error('Aucun utilisateur trouvé avec l\'email: ' . $email);
                return 1;
            }
        }

        $this->info('Test d\'envoi d\'email de réinitialisation de mot de passe...');
        $this->info('Utilisateur: ' . $user->username);
        $this->info('Email: ' . $email);
        $this->newLine();

        try {
            // Générer un token unique
            $token = Str::random(64);

            // Supprimer les anciens tokens
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            // Créer un nouveau token
            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            // Envoyer l'email
            $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);
            
            $result = $this->emailService->send(
                $email,
                'TEST - Réinitialisation de votre mot de passe - Sages Home',
                'emails.reset-password',
                [
                    'user' => $user,
                    'resetLink' => $resetLink,
                    'token' => $token
                ]
            );

            $this->newLine();
            if ($result) {
                $this->success('✓ Email envoyé avec succès !');
                $this->info('Lien de réinitialisation:');
                $this->line($resetLink);
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
