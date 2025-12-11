<?php

namespace App\Services;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Envoyer un email avec PHPMailer
     *
     * @param string|array $to Destinataire(s)
     * @param string $subject Sujet de l'email
     * @param string $view Nom de la vue Blade
     * @param array $data Données à passer à la vue
     * @param string|null $fromEmail Email de l'expéditeur (optionnel)
     * @param string|null $fromName Nom de l'expéditeur (optionnel)
     * @return bool
     */
    public function send($to, string $subject, string $view, array $data = [], ?string $fromEmail = null, ?string $fromName = null): bool
    {
        try {
            $mail = new PHPMailer(true);
            
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'ssl');
            $mail->Port = env('MAIL_PORT', 465);
            $mail->CharSet = 'UTF-8';

            // Expéditeur
            $fromEmail = $fromEmail ?? env('MAIL_FROM_ADDRESS');
            $fromName = $fromName ?? env('MAIL_FROM_NAME', 'Sages Home');
            $mail->setFrom($fromEmail, $fromName);

            // Destinataire(s)
            if (is_array($to)) {
                foreach ($to as $email) {
                    $mail->addAddress($email);
                }
            } else {
                $mail->addAddress($to);
            }

            // Contenu de l'email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = view($view, $data)->render();

            // Envoyer
            $mail->send();

            Log::info('Email envoyé avec succès', [
                'to' => $to,
                'subject' => $subject,
                'view' => $view
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Erreur envoi email PHPMailer', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Envoyer un email de confirmation de réservation au client
     *
     * @param \App\Models\Booking $booking
     * @return bool
     */
    public function sendBookingConfirmation($booking): bool
    {
        return $this->send(
            $booking->email,
            'Confirmation de votre réservation - Sages Home',
            'emails.booking-confirmation',
            ['booking' => $booking]
        );
    }

    /**
     * Envoyer un email de notification de nouvelle réservation à l'admin
     *
     * @param \App\Models\Booking $booking
     * @param string|null $adminEmail Email de l'admin (optionnel)
     * @return bool
     */
    public function sendNewBookingNotification($booking, ?string $adminEmail = null): bool
    {
        $adminEmail = $adminEmail ?? env('MAIL_FROM_ADDRESS', 'infos@sageshome.ci');

        return $this->send(
            $adminEmail,
            'Nouvelle réservation #' . $booking->booking_number,
            'emails.new-booking-admin',
            ['booking' => $booking]
        );
    }

    /**
     * Envoyer un email avec plusieurs destinataires
     *
     * @param array $recipients Liste des destinataires
     * @param string $subject
     * @param string $view
     * @param array $data
     * @return bool
     */
    public function sendToMultiple(array $recipients, string $subject, string $view, array $data = []): bool
    {
        return $this->send($recipients, $subject, $view, $data);
    }

    /**
     * Envoyer un email avec pièce jointe
     *
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $data
     * @param string|array $attachments Chemin(s) du/des fichier(s) à joindre
     * @return bool
     */
    public function sendWithAttachment($to, string $subject, string $view, array $data = [], $attachments = []): bool
    {
        try {
            $mail = new PHPMailer(true);
            
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'ssl');
            $mail->Port = env('MAIL_PORT', 465);
            $mail->CharSet = 'UTF-8';

            // Expéditeur
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', 'Sages Home'));

            // Destinataire
            $mail->addAddress($to);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = view($view, $data)->render();

            // Ajouter les pièces jointes
            if (is_array($attachments)) {
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment)) {
                        $mail->addAttachment($attachment);
                    }
                }
            } else {
                if (file_exists($attachments)) {
                    $mail->addAttachment($attachments);
                }
            }

            $mail->send();

            Log::info('Email avec pièce jointe envoyé', [
                'to' => $to,
                'attachments' => $attachments
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Erreur envoi email avec pièce jointe', [
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
