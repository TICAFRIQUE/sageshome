<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Residence;
use Illuminate\Http\Request;
use App\Services\EmailService;
use App\Services\WavePaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class BookingController extends Controller
{
    protected $wavePaymentService;
    protected $emailService;

    public function __construct(WavePaymentService $wavePaymentService, EmailService $emailService)
    {
        $this->wavePaymentService = $wavePaymentService;
        $this->emailService = $emailService;
    }

    public function create(Residence $residence, Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1|max:' . $residence->capacity,
        ]);

        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $guests = $request->guests;

        // Vérifier la disponibilité
        if (!$residence->isAvailableForDates($checkIn, $checkOut)) {
            return back()->with('error', 'Cette résidence n\'est pas disponible pour les dates sélectionnées.');
        }

        $priceInfo = $residence->calculateTotalPrice($checkIn, $checkOut);
        $taxAmount = $priceInfo['total_price'] * 0; // 10% de taxes
        $finalAmount = $priceInfo['total_price'] + $taxAmount;

        return view('frontend.booking.create', compact(
            'residence',
            'checkIn',
            'checkOut',
            'guests',
            'priceInfo',
            'taxAmount',
            'finalAmount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'residence_id' => 'required|exists:residences,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $residence = Residence::findOrFail($request->residence_id);

        // Vérifier à nouveau la disponibilité
        if (!$residence->isAvailableForDates($request->check_in, $request->check_out)) {
            return back()->with('error', 'Cette résidence n\'est plus disponible pour les dates sélectionnées.');
        }

        $priceInfo = $residence->calculateTotalPrice($request->check_in, $request->check_out);
        $taxAmount = $priceInfo['total_price'] * 0;
        $finalAmount = $priceInfo['total_price'] + $taxAmount;

        // Récupérer les informations de l'utilisateur connecté
        $user = Auth::user();

        // Créer la réservation
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'residence_id' => $request->residence_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'guests' => $request->guests,
            'nights' => $priceInfo['nights'],
            'price_per_night' => $priceInfo['price_per_night'],
            'total_price' => $priceInfo['total_price'],
            'tax_amount' => $taxAmount,
            'final_amount' => $finalAmount,
            'special_requests' => $request->special_requests,
            // Nouvelles colonnes requises
            'first_name' => $user->username ?? 'Client', // Utiliser username comme nom complet
            'last_name' => '', // Laisser vide car username contient le nom complet
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'country' => 'Cote d\'Ivoire', // Valeur par défaut
            // Alias des dates et montants
            'check_in_date' => $request->check_in,
            'check_out_date' => $request->check_out,
            'guests_count' => $request->guests,
            'subtotal_amount' => $priceInfo['total_price'],
            'total_amount' => $finalAmount,
        ]);

        // Envoyer les emails de notification avec le service Email
        try {
            // Email au client
            $this->emailService->sendBookingConfirmation($booking);
            
            // Email à l'admin
            $this->emailService->sendNewBookingNotification($booking);
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email réservation: ' . $e->getMessage());
        }

        return redirect()->route('booking.payment', $booking)->with('success', 'Votre réservation a été créée avec succès.');
    }

    public function payment(Booking $booking)
    {
        // Vérifier que l'utilisateur peut accéder à cette réservation
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load('residence');

        return view('frontend.booking.payment', compact('booking'));
    }

    public function processPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|in:wave,paypal,cash',
        ]);

        // Vérifier que l'utilisateur peut accéder à cette réservation
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Créer le paiement
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => $request->payment_method,
            'amount' => $booking->final_amount,
            'status' => $request->payment_method === 'cash' ? 'pending' : 'processing',
        ]);

        // Traitement selon la méthode de paiement
        switch ($request->payment_method) {
            case 'cash':
                // Pour le paiement en espèces, on marque la réservation comme confirmée
                // mais le paiement reste en attente
                $booking->confirm();
                return redirect()->route('booking.confirmation', $booking)
                    ->with('success', 'Votre réservation est confirmée. Le paiement en espèces sera effectué à l\'arrivée.');

            case 'wave':
                // Rediriger vers l'interface Wave
                return $this->redirectToWave($payment);

            case 'paypal':
                // Rediriger vers l'interface PayPal
                return $this->redirectToPaypal($payment);
        }
    }

    private function redirectToWave($payment)
    {
        // URLs de retour
        $successUrl = route('payment.confirm', ['payment' => $payment->id]);
        $errorUrl = route('booking.payment', ['booking' => $payment->booking_id]) . '?error=wave_payment_failed';
        
        // Référence client pour le suivi (format: payment_ID)
        $clientReference = 'payment_' . $payment->id;

        // Créer la session de checkout Wave
        $result = $this->wavePaymentService->createCheckoutSession(
            $payment->amount,
            $successUrl,
            $errorUrl,
            $clientReference
        );

        if ($result['success']) {
            $checkoutSession = $result['data'];
            
            // Sauvegarder l'ID de session Wave pour le suivi
            $payment->update([
                'transaction_id' => $checkoutSession['id'] ?? null,
                'payment_data' => json_encode($checkoutSession),
            ]);

            // Rediriger vers Wave
            if (isset($checkoutSession['wave_launch_url'])) {
                return redirect()->away($checkoutSession['wave_launch_url']);
            } else {
                return redirect()->route('booking.payment', $payment->booking)
                    ->with('error', 'Erreur: URL de paiement Wave non disponible.');
            }
        } else {
            // Erreur lors de la création de la session
            return redirect()->route('booking.payment', $payment->booking)
                ->with('error', 'Erreur lors de l\'initialisation du paiement Wave: ' . $result['error']);
        }
    }

    private function redirectToPaypal($payment)
    {
        // Simulation de redirection vers PayPal
        // Dans une vraie implémentation, vous utiliseriez l'API PayPal
        return view('frontend.payment.paypal', compact('payment'));
    }

    public function confirmPayment(Request $request, Payment $payment)
    {
        // Reconnecter automatiquement l'utilisateur si nécessaire (pour le retour Wave depuis desktop)
        if (!Auth::check() && $payment->booking->user_id) {
            Auth::loginUsingId($payment->booking->user_id);
            \Log::info('Utilisateur reconnecté automatiquement après paiement Wave', [
                'user_id' => $payment->booking->user_id,
                'payment_id' => $payment->id
            ]);
        }

        // Si le paiement est déjà complété, rediriger directement vers la confirmation
        if ($payment->status === 'completed') {
            \Log::info('Paiement déjà complété, redirection directe', [
                'payment_id' => $payment->id,
                'status' => $payment->status
            ]);

            return redirect()->route('booking.confirmation', $payment->booking)
                ->with('success', 'Votre paiement a déjà été confirmé.');
        }

        // Vérifier si le paiement est via Wave et a un transaction_id
        if ($payment->payment_method === 'wave' && $payment->transaction_id) {
            // Log pour diagnostic
            \Log::info('Vérification paiement Wave', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'user_reconnected' => Auth::check(),
                'current_status' => $payment->status
            ]);

            // Vérifier le statut du paiement auprès de Wave
            $statusResult = $this->wavePaymentService->getPaymentStatus($payment->transaction_id);
            
            if ($statusResult['success']) {
                $sessionData = $statusResult['data'];
                
                // Vérifier si le paiement a réussi selon la structure Wave
                if (isset($sessionData['checkout_status']) && $sessionData['checkout_status'] === 'complete' && 
                    isset($sessionData['payment_status']) && $sessionData['payment_status'] === 'succeeded') {
                    // Mettre à jour les données de paiement avec les informations de Wave
                    $payment->update([
                        'status' => 'completed',
                        'payment_data' => json_encode($sessionData),
                        'completed_at' => now(),
                        'transaction_id' => $sessionData['transaction_id'] ?? $payment->transaction_id,
                    ]);
                    
                    // Confirmer la réservation
                    $payment->booking->update([
                        'status' => 'confirmed',
                        'payment_status' => 'paid',
                        'confirmed_at' => now(),
                    ]);

                    // Envoyer un email de confirmation
                    // Mail::to($payment->booking->user)->send(new BookingConfirmation($payment->booking));

                    return redirect()->route('booking.confirmation', $payment->booking)
                        ->with('success', 'Votre paiement Wave a été traité avec succès.');
                } else {
                    // Le paiement n'est pas encore complété ou a échoué
                    \Log::warning('Paiement Wave non confirmé', [
                        'checkout_status' => $sessionData['checkout_status'] ?? 'N/A',
                        'payment_status' => $sessionData['payment_status'] ?? 'N/A',
                        'payment_id' => $payment->id
                    ]);

                    $payment->update([
                        'status' => 'failed',
                        'payment_data' => json_encode($sessionData),
                    ]);
                    
                    return redirect()->route('booking.payment', $payment->booking)
                        ->with('error', 'Le paiement Wave a échoué ou n\'est pas encore confirmé.');
                }
            } else {
                // Si la session a expiré (erreur 404), vérifier si le paiement a été traité par webhook
                if (isset($statusResult['session_expired']) && $statusResult['session_expired']) {
                    \Log::info('Session Wave expirée, vérification via données locales', [
                        'payment_id' => $payment->id,
                        'payment_status' => $payment->status
                    ]);

                    // Recharger le paiement depuis la DB au cas où le webhook l'aurait mis à jour
                    $payment->refresh();

                    if ($payment->status === 'completed') {
                        \Log::info('Paiement déjà confirmé par webhook', [
                            'payment_id' => $payment->id
                        ]);

                        return redirect()->route('booking.confirmation', $payment->booking)
                            ->with('success', 'Votre paiement a été confirmé avec succès.');
                    }

                    // Si toujours pas confirmé après 5 secondes, le webhook devrait arriver bientôt
                    return redirect()->route('booking.payment', $payment->booking)
                        ->with('info', 'Votre paiement est en cours de vérification. La page se mettra à jour automatiquement.');
                }

                // Erreur lors de la vérification du statut
                \Log::error('Erreur vérification statut Wave', [
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'error' => $statusResult['error'] ?? 'Erreur inconnue'
                ]);

                return redirect()->route('booking.payment', $payment->booking)
                    ->with('error', 'Impossible de vérifier le statut du paiement Wave. Veuillez contacter le support.');
            }
        }

        // Traitement pour les autres méthodes de paiement (logique existante)
        $payment->markAsCompleted();
        $payment->booking->confirm();

        // Envoyer un email de confirmation
        // Mail::to($payment->booking->user)->send(new BookingConfirmation($payment->booking));

        return redirect()->route('booking.confirmation', $payment->booking)
            ->with('success', 'Votre paiement a été traité avec succès.');
    }

    /**
     * Webhook pour recevoir les notifications de paiement Wave
     */
    public function waveWebhook(Request $request)
    {
        try {
            // Récupérer les données du webhook
            $payload = $request->all();
            
            // Log du webhook pour debug
            \Log::info('Wave Webhook reçu:', $payload);
            
            // Récupérer les données de la session directement si c'est un checkout
            $sessionData = $payload;
            $sessionId = $sessionData['id'] ?? null;
            $clientReference = $sessionData['client_reference'] ?? null;
            $checkoutStatus = $sessionData['checkout_status'] ?? null;
            $paymentStatus = $sessionData['payment_status'] ?? null;
            
            if (!$sessionId && !$clientReference) {
                \Log::error('Wave Webhook: Session ID et client_reference manquants');
                return response()->json(['error' => 'Données manquantes'], 400);
            }
            
            // Trouver le paiement correspondant
            $payment = null;
            
            if ($clientReference && strpos($clientReference, 'payment_') === 0) {
                // Extraire l'ID du paiement depuis client_reference (format: payment_123)
                $paymentId = str_replace('payment_', '', $clientReference);
                $payment = Payment::find($paymentId);
            }
            
            // Si pas trouvé par client_reference, essayer par transaction_id
            if (!$payment && $sessionId) {
                $payment = Payment::where('transaction_id', $sessionId)->first();
            }
            
            if (!$payment) {
                \Log::error('Wave Webhook: Paiement non trouvé pour la session: ' . $sessionId . ' ref: ' . $clientReference);
                return response()->json(['error' => 'Paiement non trouvé'], 404);
            }
            
            // Vérifier si le paiement n'est pas déjà traité
            if ($payment->status === 'completed') {
                return response()->json(['status' => 'already_processed'], 200);
            }
            
            // Vérifier si le paiement a réussi
            if ($checkoutStatus === 'complete' && $paymentStatus === 'succeeded') {
                // Mettre à jour le paiement
                $payment->update([
                    'status' => 'completed',
                    'payment_data' => json_encode($sessionData),
                    'completed_at' => now(),
                    'transaction_id' => $sessionData['transaction_id'] ?? $payment->transaction_id,
                ]);
                
                // Confirmer la réservation
                $payment->booking->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'confirmed_at' => now(),
                ]);
                
                \Log::info('Paiement Wave confirmé via webhook pour payment ID: ' . $payment->id);
                
                // Envoyer un email de confirmation (optionnel)
                // Mail::to($payment->booking->user)->send(new BookingConfirmation($payment->booking));
                
                return response()->json(['status' => 'success'], 200);
            } else {
                // Le paiement a échoué ou est en attente
                $payment->update([
                    'status' => $paymentStatus === 'failed' ? 'failed' : 'pending',
                    'payment_data' => json_encode($sessionData),
                ]);
                
                \Log::warning('Paiement Wave non réussi via webhook: ' . $checkoutStatus . '/' . $paymentStatus);
                
                return response()->json(['status' => 'payment_not_completed'], 200);
            }
            
        } catch (\Exception $e) {
            \Log::error('Erreur Webhook Wave: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * API pour vérifier le statut d'un paiement
     */
    public function getPaymentStatus(Payment $payment)
    {
        // Rafraîchir depuis la base de données pour avoir les dernières données
        $payment->refresh();
        $payment->load('booking');

        return response()->json([
            'status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'amount' => $payment->amount,
            'booking_status' => $payment->booking->status,
            'completed_at' => $payment->completed_at ? $payment->completed_at->toIso8601String() : null,
        ]);
    }

    public function confirmation(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['residence', 'payments']);

        return view('frontend.booking.confirmation', compact('booking'));
    }

    public function myBookings()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $bookings = $user->bookings()
            ->with(['residence.primaryImage', 'residence.residenceType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('frontend.booking.my-bookings', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['residence.images', 'residence.residenceType', 'payments']);

        return view('frontend.booking.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Cette réservation ne peut plus être annulée.');
        }

        $booking->cancel('Annulée par le client');

        return back()->with('success', 'Votre réservation a été annulée.');
    }
}