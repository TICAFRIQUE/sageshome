<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Log;
use Exception;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre de tentatives
     */
    public $tries = 3;

    /**
     * Délai entre les tentatives (en secondes)
     */
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    /**
     * Timeout du job (en secondes)
     */
    public $timeout = 60;

    protected $to;
    protected $subject;
    protected $view;
    protected $data;
    protected $fromEmail;
    protected $fromName;
    protected $attachments;

    /**
     * Create a new job instance.
     */
    public function __construct($to, string $subject, string $view, array $data = [], ?string $fromEmail = null, ?string $fromName = null, array $attachments = [])
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->attachments = $attachments;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $mail = new PHPMailer(true);
            
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = config('mail.mailers.smtp.host');
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = config('mail.mailers.smtp.password');
            $mail->SMTPSecure = config('mail.mailers.smtp.encryption', 'ssl');
            $mail->Port = config('mail.mailers.smtp.port', 465);
            $mail->CharSet = 'UTF-8';

            // Expéditeur
            $fromEmail = $this->fromEmail ?? config('mail.from.address');
            $fromName = $this->fromName ?? config('mail.from.name', 'Sages Home');
            $mail->setFrom($fromEmail, $fromName);

            // Destinataire(s)
            if (is_array($this->to)) {
                foreach ($this->to as $email) {
                    $mail->addAddress($email);
                }
            } else {
                $mail->addAddress($this->to);
            }

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = $this->subject;
            $mail->Body = view($this->view, $this->data)->render();

            // Pièces jointes
            if (!empty($this->attachments)) {
                foreach ($this->attachments as $attachment) {
                    if (isset($attachment['path']) && file_exists($attachment['path'])) {
                        $mail->addAttachment(
                            $attachment['path'],
                            $attachment['name'] ?? basename($attachment['path'])
                        );
                    }
                }
            }

            // Envoyer
            $mail->send();

            Log::info('Email envoyé avec succès (Queue)', [
                'to' => $this->to,
                'subject' => $this->subject,
                'view' => $this->view,
                'attempt' => $this->attempts()
            ]);

        } catch (Exception $e) {
            Log::error('Erreur envoi email (Queue)', [
                'to' => $this->to,
                'subject' => $this->subject,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // Relancer le job si encore des tentatives
            if ($this->attempts() < $this->tries) {
                throw $e;
            }
        }
    }

    /**
     * Gérer l'échec du job après toutes les tentatives
     */
    public function failed(Exception $exception): void
    {
        Log::critical('Email échoué après toutes les tentatives', [
            'to' => $this->to,
            'subject' => $this->subject,
            'view' => $this->view,
            'error' => $exception->getMessage()
        ]);

        // Optionnel : Notifier l'admin par un autre canal (Slack, etc.)
    }
}
