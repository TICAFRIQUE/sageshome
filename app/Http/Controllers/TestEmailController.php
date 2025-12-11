<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\EmailService;
use Illuminate\Http\Request;

class TestEmailController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function testEmail()
    {
        try {
            // Récupérer une réservation pour le test
            $booking = Booking::with('residence')->latest()->first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune réservation trouvée pour le test'
                ]);
            }

            // Envoyer l'email client
            $result = $this->emailService->sendBookingConfirmation($booking);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email de confirmation envoyé avec succès à ' . $booking->email,
                    'booking_number' => $booking->booking_number
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de l\'envoi de l\'email'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    public function testAdminEmail()
    {
        try {
            // Récupérer une réservation pour le test
            $booking = Booking::with('residence')->latest()->first();
            
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune réservation trouvée pour le test'
                ]);
            }

            // Envoyer l'email admin
            $result = $this->emailService->sendNewBookingNotification($booking);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email admin envoyé avec succès à ' . env('MAIL_FROM_ADDRESS'),
                    'booking_number' => $booking->booking_number
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de l\'envoi de l\'email'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }
}
