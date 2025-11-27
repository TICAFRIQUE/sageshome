<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Residence;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //index for dashboard
    public function index(Request $request)
    {
        return view('backend.pages.index');
    }

    // Dashboard Sages Home
    public function sagesHomeDashboard(Request $request)
    {
        // Statistiques générales pour le dashboard Sages Home
        $totalResidences = Residence::count();
        $totalBookings = Booking::count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        
        // Réservations récentes
        $recentBookings = Booking::with('residence')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Statistiques des réservations par statut
        $bookingStats = Booking::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        return view('backend.pages.sages-home.dashboard', compact(
            'totalResidences',
            'totalBookings', 
            'totalRevenue',
            'recentBookings',
            'bookingStats'
        ));
    }
}
