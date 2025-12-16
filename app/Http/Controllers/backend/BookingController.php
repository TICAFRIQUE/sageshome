<?php

namespace App\Http\Controllers\backend;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Residence;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        return view('backend.pages.sages-home.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['residence', 'payment']);

        //mettre à jour seen_at
        if (is_null($booking->seen_at)) {
            $booking->update(['seen_at' => now()]);
        }
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
                'reference' => $booking->booking_number,
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

    // function to get new bookings (not seen yet)
    public function getNewBookings()
    {
        $newBookings = Booking::with(['residence'])->where('created_at', '>=', now()->subMinutes(2))
            ->whereNull('seen_at')
            ->orderBy('created_at', 'desc')->get();

        // $newBookings = Booking::with(['residence'])
        //     ->where('status', 'pending')
        //     ->where('created_at', '>=', now()->subDay())
        //     ->whereNull('seen_at')
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        return response()->json([
            'count' => $newBookings->count(),
            'bookings' => $newBookings->map(function ($booking) {
                return [
                    'reference' => $booking->booking_number,
                    'guest_name' => $booking->first_name . ' ' . $booking->last_name,
                    'residence_name' => $booking->residence->name,
                    'created_at' => $booking->created_at->diffForHumans(),
                    'total_amount' => number_format($booking->total_amount, 0, ',', ' ') . ' FCFA',
                    'show_url' => route('admin.bookings.show', $booking->id),
                    'quick_view_url' => route('admin.bookings.quick-view', $booking->id),
                ];
            })
        ]);
    }






    public function markAsSeen(Request $request)
    {
        // Marquer toutes les réservations non vues comme vues
        Booking::whereNull('seen_at')
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subDay())
            ->update(['seen_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function report(Request $request)
    {
        $residences = Residence::orderBy('name')->get();
        
        // Récupérer les paramètres de filtre
        $filters = [
            'start_date' => $request->start_date ?? now()->startOfMonth()->format('Y-m-d'),
            'end_date' => $request->end_date ?? now()->endOfMonth()->format('Y-m-d'),
            'residence_id' => $request->residence_id,
            'status' => $request->status,
        ];

        // Construire la requête
        $query = Booking::with(['residence', 'payments'])
            ->whereBetween('check_in_date', [$filters['start_date'], $filters['end_date']]);

        if ($filters['residence_id']) {
            $query->where('residence_id', $filters['residence_id']);
        }

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        $bookings = $query->orderBy('check_in_date')->get();

        // Statistiques globales
        $stats = [
            'total_bookings' => $bookings->count(),
            'confirmed_bookings' => $bookings->where('status', 'confirmed')->count(),
            'pending_bookings' => $bookings->where('status', 'pending')->count(),
            'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
            'total_revenue' => $bookings->where('status', 'confirmed')
                ->flatMap(fn($b) => $b->payments->where('status', 'completed'))
                ->sum('amount'),
            'pending_revenue' => $bookings->where('status', 'pending')->sum('total_amount'),
            'total_nights' => $bookings->where('status', 'confirmed')->sum(function($booking) {
                return Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date);
            }),
        ];

        // Statistiques par résidence
        $residenceStats = $bookings->groupBy('residence_id')->map(function ($bookings, $residenceId) {
            $residence = $bookings->first()->residence;
            $confirmedBookings = $bookings->where('status', 'confirmed');
            
            return [
                'residence' => $residence,
                'total_bookings' => $bookings->count(),
                'confirmed_bookings' => $confirmedBookings->count(),
                'total_revenue' => $confirmedBookings->flatMap(fn($b) => $b->payments->where('status', 'completed'))->sum('amount'),
                'total_nights' => $confirmedBookings->sum(function($booking) {
                    return Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date);
                }),
            ];
        });

        // Statistiques par moyen de paiement
        $paymentMethodStats = $bookings->where('status', 'confirmed')
            ->flatMap(function($booking) {
                return $booking->payments->where('status', 'completed');
            })
            ->groupBy('payment_method')
            ->map(function($payments, $method) {
                return [
                    'count' => $payments->count(),
                    'total' => $payments->sum('amount'),
                ];
            });

        return view('backend.pages.sages-home.bookings.report', compact(
            'residences',
            'filters',
            'bookings',
            'stats',
            'residenceStats',
            'paymentMethodStats'
        ));
    }

    public function destroy(Booking $booking)
    {
        try {
            // Supprimer d'abord les paiements associés
            $booking->payments()->delete();
            
            // Supprimer la réservation
            $booking->delete();

            return redirect()->route('admin.bookings.index')
                ->with('success', 'La réservation a été supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression de la réservation.');
        }
    }

    public function create()
    {
        return view('backend.pages.sages-home.bookings.create');
    }

    public function getAvailableResidences(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        // Récupérer toutes les résidences disponibles
        $residences = Residence::where('is_available', true)->get();

        // Filtrer celles qui ne sont pas réservées pour ces dates
        $availableResidences = $residences->filter(function ($residence) use ($checkIn, $checkOut) {
            $conflictingBookings = Booking::where('residence_id', $residence->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                        ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<=', $checkIn)
                              ->where('check_out_date', '>=', $checkOut);
                        });
                })->exists();

            return !$conflictingBookings;
        });

        $nights = $checkIn->diffInDays($checkOut);

        return response()->json([
            'residences' => $availableResidences->map(function ($residence) use ($nights) {
                $total = $residence->price_per_night * $nights;
                
                return [
                    'id' => $residence->id,
                    'name' => $residence->name,
                    'price_per_night' => $residence->price_per_night,
                    'total' => $total,
                ];
            })->values(),
            'nights' => $nights,
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'residence_id' => 'required|exists:residences,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $residence = Residence::findOrFail($request->residence_id);
        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        // Vérifier les réservations existantes qui se chevauchent
        $conflictingBookings = Booking::where('residence_id', $request->residence_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<=', $checkIn)
                          ->where('check_out_date', '>=', $checkOut);
                    });
            })->count();

        $nights = $checkIn->diffInDays($checkOut);
        $total = $residence->price_per_night * $nights;

        return response()->json([
            'available' => $conflictingBookings === 0,
            'nights' => $nights,
            'price_per_night' => $residence->price_per_night,
            'total' => $total,
            'message' => $conflictingBookings > 0 
                ? 'Cette résidence n\'est pas disponible pour ces dates.' 
                : 'Résidence disponible !'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'residence_id' => 'required|exists:residences,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'guests' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,wave,paypal,bank_transfer,credit_card',
            'payment_status' => 'required|in:completed,pending',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $residence = Residence::findOrFail($validated['residence_id']);
            $checkIn = Carbon::parse($validated['check_in']);
            $checkOut = Carbon::parse($validated['check_out']);
            
            // Vérifier à nouveau la disponibilité
            $conflictingBookings = Booking::where('residence_id', $validated['residence_id'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($checkIn, $checkOut) {
                    $query->whereBetween('check_in_date', [$checkIn, $checkOut])
                        ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                        ->orWhere(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<=', $checkIn)
                              ->where('check_out_date', '>=', $checkOut);
                        });
                })->exists();

            if ($conflictingBookings) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Cette résidence n\'est plus disponible pour ces dates.');
            }

            // Calculer les montants
            $nights = $checkIn->diffInDays($checkOut);
            $total = $residence->price_per_night * $nights;

            // Créer la réservation
            $booking = Booking::create([
                'residence_id' => $validated['residence_id'],
                'user_id' => Auth::id(), // L'admin qui crée la réservation
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'country' => $validated['country'] ?? null,
                'check_in' => '14:00:00',
                'check_out' => '12:00:00',
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'guests' => $validated['guests'],
                'guests_count' => $validated['guests'],
                'nights' => $nights,
                'price_per_night' => $residence->price_per_night,
                'subtotal_amount' => $total,
                'tax_amount' => 0,
                'total_amount' => $total,
                'total_price' => $total,
                'final_amount' => $total,
                'status' => 'confirmed', // Réservation confirmée directement
                'special_requests' => $validated['special_requests'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'confirmed_at' => now(),
            ]);

            // Créer le paiement
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $total,
                'payment_method' => $validated['payment_method'],
                'status' => $validated['payment_status'],
                'currency' => 'XOF',
                'transaction_id' => 'ADMIN-' . strtoupper(uniqid()),
            ]);

            return redirect()->route('admin.bookings.show', $booking)
                ->with('success', 'Réservation créée avec succès !');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
}
