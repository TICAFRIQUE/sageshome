<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Residence;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class BookingController extends Controller
{
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
        $taxAmount = $priceInfo['total_price'] * 0.10; // 10% de taxes
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
        $taxAmount = $priceInfo['total_price'] * 0.10;
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
            'country' => 'Sénégal', // Valeur par défaut
            // Alias des dates et montants
            'check_in_date' => $request->check_in,
            'check_out_date' => $request->check_out,
            'guests_count' => $request->guests,
            'subtotal_amount' => $priceInfo['total_price'],
            'total_amount' => $finalAmount,
        ]);

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
        // Simulation de redirection vers Wave
        // Dans une vraie implémentation, vous utiliseriez l'API Wave
        return view('frontend.payment.wave', compact('payment'));
    }

    private function redirectToPaypal($payment)
    {
        // Simulation de redirection vers PayPal
        // Dans une vraie implémentation, vous utiliseriez l'API PayPal
        return view('frontend.payment.paypal', compact('payment'));
    }

    public function confirmPayment(Payment $payment)
    {
        // Cette route sera appelée après un paiement réussi
        $payment->markAsCompleted();
        $payment->booking->confirm();

        // Envoyer un email de confirmation
        // Mail::to($payment->booking->user)->send(new BookingConfirmation($payment->booking));

        return redirect()->route('booking.confirmation', $payment->booking)
            ->with('success', 'Votre paiement a été traité avec succès.');
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