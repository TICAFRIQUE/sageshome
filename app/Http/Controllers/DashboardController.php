<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class DashboardController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Affiche le tableau de bord principal du client
     */
    public function index()
    {
        $user = Auth::user();
        
        // Statistiques des réservations
        $bookingsStats = [
            'total' => $user->bookings()->count(),
            'pending' => $user->bookings()->where('status', 'pending')->count(),
            'confirmed' => $user->bookings()->where('status', 'confirmed')->count(),
            'completed' => $user->bookings()->where('status', 'completed')->count(),
            'cancelled' => $user->bookings()->where('status', 'cancelled')->count(),
        ];

        // Réservations récentes
        $recentBookings = $user->bookings()
            ->with(['residence', 'residence.residenceType', 'residence.images'])
            ->latest()
            ->limit(3)
            ->get();

        // Prochaine réservation
        $upcomingBooking = $user->bookings()
            ->with(['residence', 'residence.residenceType'])
            ->where('check_in', '>', Carbon::now())
            ->where('status', 'confirmed')
            ->orderBy('check_in', 'asc')
            ->first();

        // Calcul du montant total dépensé
        $totalSpent = $user->bookings()
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        return view('frontend.dashboard.index', compact(
            'user', 
            'bookingsStats', 
            'recentBookings', 
            'upcomingBooking', 
            'totalSpent'
        ));
    }

    /**
     * Affiche la liste des réservations du client
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->bookings()
            ->with(['residence', 'residence.residenceType', 'residence.images']);

        // Filtrage par statut
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Tri
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortBy, ['created_at', 'check_in', 'check_out', 'total_price'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->latest();
        }

        $bookings = $query->paginate(12)->withQueryString();

        // Statistiques pour les filtres
        $statusCounts = [
            'all' => $user->bookings()->count(),
            'pending' => $user->bookings()->where('status', 'pending')->count(),
            'confirmed' => $user->bookings()->where('status', 'confirmed')->count(),
            'completed' => $user->bookings()->where('status', 'completed')->count(),
            'cancelled' => $user->bookings()->where('status', 'cancelled')->count(),
        ];

        $breadcrumb = [
            ['name' => 'Mes Réservations', 'url' => '']
        ];

        return view('frontend.dashboard.bookings', compact(
            'bookings', 
            'statusCounts', 
            'breadcrumb'
        ));
    }

    /**
     * Affiche le détail d'une réservation
     */
    public function bookingShow(Booking $booking)
    {
        // Vérifier que la réservation appartient au client connecté
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['residence', 'residence.residenceType', 'residence.images']);

        $breadcrumb = [
            ['name' => 'Mes Réservations', 'url' => route('dashboard.bookings')],
            ['name' => 'Réservation #' . $booking->id, 'url' => '']
        ];

        return view('frontend.dashboard.booking-detail', compact('booking', 'breadcrumb'));
    }

    /**
     * Annule une réservation
     */
    public function cancelBooking(Booking $booking)
    {
        // Vérifier que la réservation appartient au client connecté
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Vérifier que la réservation peut être annulée
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return redirect()->back()->with('error', 'Cette réservation ne peut pas être annulée.');
        }

        // Vérifier que la date d'arrivée n'est pas passée
        if (Carbon::parse($booking->check_in)->isPast()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas annuler une réservation dont la date d\'arrivée est déjà passée.');
        }

        $booking->update(['status' => 'cancelled']);

        return redirect()->route('dashboard.bookings')
            ->with('success', 'Votre réservation a été annulée avec succès.');
    }

    /**
     * Affiche le profil du client
     */
    public function profile()
    {
        $user = Auth::user();
        
        $breadcrumb = [
            ['name' => 'Mon Profil', 'url' => '']
        ];

        return view('frontend.dashboard.profile', compact('user', 'breadcrumb'));
    }

    /**
     * Met à jour le profil du client
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
        ]);

        return redirect()->route('dashboard.profile')
            ->with('success', 'Votre profil a été mis à jour avec succès.');
    }

    /**
     * Met à jour le mot de passe du client
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('dashboard.profile')
            ->with('success', 'Votre mot de passe a été modifié avec succès.');
    }
}