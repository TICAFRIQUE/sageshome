<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['residence.primaryImage', 'payment']);

        // Filtres
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_status) {
            $query->whereHas('payment', function($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('backend.pages.sages-home.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['residence', 'payment']);
        return view('backend.pages.sages-home.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        // Log ou notifications ici si nécessaire
        
        return redirect()->back()->with('success', 'Statut de la réservation mis à jour avec succès');
    }

    public function confirmPayment(Request $request, Booking $booking)
    {
        $payment = $booking->payment;
        
        if (!$payment) {
            return redirect()->back()->with('error', 'Aucun paiement trouvé pour cette réservation');
        }

        $payment->update(['status' => 'completed']);
        
        // Confirmer automatiquement la réservation si le paiement est confirmé
        if ($booking->status === 'pending') {
            $booking->update(['status' => 'confirmed']);
        }

        return redirect()->back()->with('success', 'Paiement confirmé avec succès');
    }

    public function calendar(Request $request)
    {
        $residences = \App\Models\Residence::orderBy('name')->get();
        return view('backend.pages.sages-home.bookings.calendar', compact('residences'));
    }

    public function calendarData(Request $request)
    {
        $query = Booking::with(['residence'])
            ->where('status', '!=', 'cancelled');

        // Filtres
        if ($request->residence_id) {
            $query->where('residence_id', $request->residence_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->start_date) {
            $query->where('check_in_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('check_out_date', '<=', $request->end_date);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'residence_name' => $booking->residence->name,
                'guest_name' => $booking->first_name . ' ' . $booking->last_name,
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $booking->check_out_date,
                'status' => $booking->status,
                'total_amount' => $booking->total_amount
            ];
        });

        return response()->json($events);
    }

    public function quickView(Booking $booking)
    {
        $booking->load(['residence']);
        
        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'residence_name' => $booking->residence->name,
                'guest_name' => $booking->first_name . ' ' . $booking->last_name,
                'phone' => $booking->phone,
                'email' => $booking->email,
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $booking->check_out_date,
                'guests_count' => $booking->guests_count,
                'total_amount' => $booking->total_amount,
                'status' => $booking->status
            ]
        ]);
    }

    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Cette réservation ne peut pas être confirmée');
        }

        $booking->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Réservation confirmée avec succès');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'Cette réservation est déjà annulée');
        }

        $booking->update(['status' => 'cancelled']);

        // Marquer le paiement comme échoué si nécessaire
        if ($booking->payments()->exists()) {
            $booking->payments()->update(['status' => 'failed']);
        }

        return redirect()->back()->with('success', 'Réservation annulée avec succès');
    }
}