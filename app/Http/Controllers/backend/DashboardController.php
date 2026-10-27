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
    public function index(Request $request)
    {
        $today = Carbon::today();
        $startOfMonth = now()->startOfMonth();
        // Même période le mois dernier (pour une comparaison juste)
        $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthSameDay = now()->subMonthNoOverflow();

        $validStatuses = ['confirmed', 'completed'];
        $paid = fn () => Payment::where('status', 'completed');

        // ---------- Indicateurs clés ----------
        $revenueMonth = $paid()->whereBetween('created_at', [$startOfMonth, now()])->sum('amount');
        $revenueLastMonth = $paid()->whereBetween('created_at', [$lastMonthStart, $lastMonthSameDay])->sum('amount');

        $bookingsMonth = Booking::whereBetween('created_at', [$startOfMonth, now()])->count();
        $bookingsLastMonth = Booking::whereBetween('created_at', [$lastMonthStart, $lastMonthSameDay])->count();

        $totalResidences = Residence::count();
        $occupiedToday = Booking::whereIn('status', $validStatuses)
            ->whereDate('check_in_date', '<=', $today)
            ->whereDate('check_out_date', '>', $today)
            ->distinct('residence_id')
            ->count('residence_id');

        $trend = fn ($now, $before) => $before > 0 ? round(($now - $before) / $before * 100) : null;

        $kpis = [
            'revenue_month' => $revenueMonth,
            'revenue_trend' => $trend($revenueMonth, $revenueLastMonth),
            'bookings_month' => $bookingsMonth,
            'bookings_trend' => $trend($bookingsMonth, $bookingsLastMonth),
            'occupied_today' => $occupiedToday,
            'total_residences' => $totalResidences,
            'occupancy_today' => $totalResidences > 0 ? round($occupiedToday / $totalResidences * 100) : 0,
            'pending' => Booking::where('status', 'pending')->count(),
            'to_collect' => Booking::whereIn('status', ['pending', 'confirmed'])
                ->whereDoesntHave('payments', fn ($q) => $q->where('status', 'completed'))
                ->sum('total_amount'),
            'revenue_today' => $paid()->whereDate('created_at', $today)->sum('amount'),
            'revenue_year' => $paid()->whereBetween('created_at', [now()->startOfYear(), now()])->sum('amount'),
        ];

        // ---------- Mouvements du jour ----------
        $arrivals = Booking::with('residence')
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('check_in_date', $today)
            ->orderBy('check_in_date')->get();

        $departures = Booking::with('residence')
            ->whereIn('status', $validStatuses)
            ->whereDate('check_out_date', $today)
            ->get();

        $upcoming = Booking::with('residence')
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('check_in_date', '>', $today)
            ->whereDate('check_in_date', '<=', $today->copy()->addDays(7))
            ->orderBy('check_in_date')
            ->take(8)->get();

        // ---------- Réservations récentes ----------
        $recentBookings = Booking::with(['residence', 'payment'])
            ->latest()
            ->take(8)
            ->get();

        // ---------- Encaissements des 12 derniers mois (1 requête) ----------
        $from = now()->subMonths(11)->startOfMonth();
        $monthly = Payment::where('status', 'completed')
            ->where('created_at', '>=', $from)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, SUM(amount) as total, COUNT(*) as nb")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $bookingsMonthly = Booking::where('created_at', '>=', $from)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as nb")
            ->groupBy('ym')
            ->pluck('nb', 'ym');

        $chart = ['labels' => [], 'revenue' => [], 'bookings' => []];
        for ($i = 0; $i < 12; $i++) {
            $month = $from->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $chart['labels'][] = ucfirst($month->translatedFormat('M y'));
            $chart['revenue'][] = (int) ($monthly[$key] ?? 0);
            $chart['bookings'][] = (int) ($bookingsMonthly[$key] ?? 0);
        }

        // ---------- Répartition par statut ----------
        $bookingStats = Booking::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status');

        // ---------- Top résidences du mois (CA réservé) ----------
        $topResidences = Booking::whereIn('bookings.status', $validStatuses)
            ->whereBetween('check_in_date', [$startOfMonth->toDateString(), now()->endOfMonth()->toDateString()])
            ->join('residences', 'bookings.residence_id', '=', 'residences.id')
            ->select('residences.id', 'residences.name', DB::raw('SUM(bookings.total_amount) as total'), DB::raw('COUNT(*) as nb'))
            ->groupBy('residences.id', 'residences.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('backend.pages.index', compact(
            'kpis',
            'arrivals',
            'departures',
            'upcoming',
            'recentBookings',
            'chart',
            'bookingStats',
            'topResidences'
        ));
    }

    // Ancienne URL du tableau de bord Sages Home : même contenu
    public function sagesHomeDashboard(Request $request)
    {
        return $this->index($request);
    }
}
