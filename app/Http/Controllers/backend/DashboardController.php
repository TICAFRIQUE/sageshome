<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Residence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //index for dashboard
    public function index(Request $request)
    {
        // Statistiques générales pour le dashboard
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

        // ========== REVENUS PAR PÉRIODE ==========
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        // Revenus globaux par période
        $revenueToday = Payment::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $revenueWeek = Payment::where('status', 'completed')
            ->whereDate('created_at', '>=', $startOfWeek)
            ->sum('amount');

        $revenueMonth = Payment::where('status', 'completed')
            ->whereDate('created_at', '>=', $startOfMonth)
            ->sum('amount');

        $revenueYear = Payment::where('status', 'completed')
            ->whereDate('created_at', '>=', $startOfYear)
            ->sum('amount');

        // ========== REVENUS PAR RÉSIDENCE PAR PÉRIODE ==========
        // Revenus par résidence - Aujourd'hui
        $revenueByResidenceToday = Payment::where('payments.status', 'completed')
            ->whereDate('payments.created_at', $today)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('residences', 'bookings.residence_id', '=', 'residences.id')
            ->select('residences.id', 'residences.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('residences.id', 'residences.name')
            ->orderBy('total', 'desc')
            ->get();

        // Revenus par résidence - Cette semaine
        $revenueByResidenceWeek = Payment::where('payments.status', 'completed')
            ->whereDate('payments.created_at', '>=', $startOfWeek)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('residences', 'bookings.residence_id', '=', 'residences.id')
            ->select('residences.id', 'residences.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('residences.id', 'residences.name')
            ->orderBy('total', 'desc')
            ->get();

        // Revenus par résidence - Ce mois
        $revenueByResidenceMonth = Payment::where('payments.status', 'completed')
            ->whereDate('payments.created_at', '>=', $startOfMonth)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('residences', 'bookings.residence_id', '=', 'residences.id')
            ->select('residences.id', 'residences.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('residences.id', 'residences.name')
            ->orderBy('total', 'desc')
            ->get();

        // Revenus par résidence - Cette année
        $revenueByResidenceYear = Payment::where('payments.status', 'completed')
            ->whereDate('payments.created_at', '>=', $startOfYear)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('residences', 'bookings.residence_id', '=', 'residences.id')
            ->select('residences.id', 'residences.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('residences.id', 'residences.name')
            ->orderBy('total', 'desc')
            ->get();

        return view('backend.pages.index', compact(
            'totalResidences',
            'totalBookings', 
            'totalRevenue',
            'recentBookings',
            'bookingStats',
            'revenueToday',
            'revenueWeek',
            'revenueMonth',
            'revenueYear',
            'revenueByResidenceToday',
            'revenueByResidenceWeek',
            'revenueByResidenceMonth',
            'revenueByResidenceYear'
        ));
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

        // ========== REVENUS PAR PÉRIODE ==========
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        // Revenus globaux par période
        $revenueToday = Payment::where('status', 'completed')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $revenueWeek = Payment::where('status', 'completed')
            ->whereDate('created_at', '>=', $startOfWeek)
            ->sum('amount');

        $revenueMonth = Payment::where('status', 'completed')
            ->whereDate('created_at', '>=', $startOfMonth)
            ->sum('amount');

        $revenueYear = Payment::where('status', 'completed')
            ->whereDate('created_at', '>=', $startOfYear)
            ->sum('amount');

        // ========== REVENUS PAR RÉSIDENCE PAR PÉRIODE ==========
        // Revenus par résidence - Aujourd'hui
        $revenueByResidenceToday = Payment::where('payments.status', 'completed')
            ->whereDate('payments.created_at', $today)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('residences', 'bookings.residence_id', '=', 'residences.id')
            ->select('residences.id', 'residences.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('residences.id', 'residences.name')
            ->orderBy('total', 'desc')
            ->get();

        // Revenus par résidence - Cette semaine
        $revenueByResidenceWeek = Payment::where('payments.status', 'completed')
            ->whereDate('payments.created_at', '>=', $startOfWeek)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('residences', 'bookings.residence_id', '=', 'residences.id')
            ->select('residences.id', 'residences.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('residences.id', 'residences.name')
            ->orderBy('total', 'desc')
            ->get();

        // Revenus par résidence - Ce mois
        $revenueByResidenceMonth = Payment::where('payments.status', 'completed')
            ->whereDate('payments.created_at', '>=', $startOfMonth)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('residences', 'bookings.residence_id', '=', 'residences.id')
            ->select('residences.id', 'residences.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('residences.id', 'residences.name')
            ->orderBy('total', 'desc')
            ->get();

        // Revenus par résidence - Cette année
        $revenueByResidenceYear = Payment::where('payments.status', 'completed')
            ->whereDate('payments.created_at', '>=', $startOfYear)
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('residences', 'bookings.residence_id', '=', 'residences.id')
            ->select('residences.id', 'residences.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('residences.id', 'residences.name')
            ->orderBy('total', 'desc')
            ->get();

        return view('backend.pages.sages-home.dashboard', compact(
            'totalResidences',
            'totalBookings', 
            'totalRevenue',
            'recentBookings',
            'bookingStats',
            'revenueToday',
            'revenueWeek',
            'revenueMonth',
            'revenueYear',
            'revenueByResidenceToday',
            'revenueByResidenceWeek',
            'revenueByResidenceMonth',
            'revenueByResidenceYear'
        ));
    }
}
