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
    /**
     * Applique les filtres de la liste (hors statut) — partagé par la liste et l'export.
     */
    private function applyListFilters($query, Request $request)
    {
        if ($request->filled('payment_status')) {
            if ($request->payment_status === 'none') {
                $query->doesntHave('payments');
            } else {
                $query->whereHas('payment', fn ($q) => $q->where('status', $request->payment_status));
            }
        }

        if ($request->filled('residence_id')) {
            $query->where('residence_id', $request->residence_id);
        }

        // Période sur la date d'arrivée
        if ($request->filled('date_from')) {
            $query->whereDate('check_in_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('check_in_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%{$search}%"])
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function applySort($query, Request $request)
    {
        return match ($request->sort) {
            'check_in' => $query->orderBy('check_in_date'),
            'check_in_desc' => $query->orderByDesc('check_in_date'),
            'amount_desc' => $query->orderByDesc('total_amount'),
            default => $query->orderByDesc('created_at'),
        };
    }

    public function index(Request $request)
    {
        // Compteurs par statut (avec les autres filtres appliqués) pour les onglets
        $statusCounts = $this->applyListFilters(Booking::query(), $request)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $query = $this->applyListFilters(Booking::with(['residence.primaryImage', 'payment']), $request);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $perPage = in_array((int) $request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 25;
        $bookings = $this->applySort($query, $request)->paginate($perPage)->withQueryString();

        // Indicateurs rapides (ensemble des réservations)
        $kpis = [
            'pending' => Booking::where('status', 'pending')->count(),
            'arrivals_today' => Booking::whereIn('status', ['confirmed', 'pending'])->whereDate('check_in_date', today())->count(),
            'departures_today' => Booking::where('status', 'confirmed')->whereDate('check_out_date', today())->count(),
            'month_revenue' => Payment::where('status', 'completed')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
        ];

        $residences = Residence::orderBy('name')->get(['id', 'name']);

        return view('backend.pages.sages-home.bookings.index', compact('bookings', 'statusCounts', 'kpis', 'residences'));
    }

    /**
     * Export CSV (compatible Excel) des réservations avec les filtres en cours.
     */
    public function export(Request $request)
    {
        $query = $this->applyListFilters(Booking::with(['residence', 'payment']), $request);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $query = $this->applySort($query, $request);

        $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'cancelled' => 'Annulée', 'completed' => 'Terminée'];
        $paymentLabels = ['pending' => 'En attente', 'processing' => 'En cours', 'completed' => 'Payé', 'failed' => 'Échec'];
        $methodLabels = ['cash' => 'Espèces', 'wave' => 'Wave', 'paypal' => 'PayPal', 'bank_transfer' => 'Virement', 'credit_card' => 'Carte bancaire'];

        $filename = 'reservations_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($query, $statusLabels, $paymentLabels, $methodLabels) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
            fputcsv($out, [
                'Référence', 'Créée le', 'Client', 'E-mail', 'Téléphone', 'Résidence', 'Arrivée', 'Départ',
                'Nuits', 'Voyageurs', 'Montant (FCFA)', 'Statut', 'Paiement', 'Moyen de paiement', 'Demandes spéciales',
            ], ';');

            $query->chunk(500, function ($bookings) use ($out, $statusLabels, $paymentLabels, $methodLabels) {
                foreach ($bookings as $b) {
                    fputcsv($out, [
                        $b->booking_number,
                        $b->created_at?->format('d/m/Y H:i'),
                        trim($b->first_name . ' ' . $b->last_name),
                        $b->email,
                        $b->phone ? "=\"" . $b->phone . "\"" : "", // texte pour garder le 0 initial dans Excel
                        $b->residence?->name,
                        $b->check_in_date ? Carbon::parse($b->check_in_date)->format('d/m/Y') : '',
                        $b->check_out_date ? Carbon::parse($b->check_out_date)->format('d/m/Y') : '',
                        $b->nights,
                        $b->guests ?? $b->guests_count,
                        (int) round($b->total_amount),
                        $statusLabels[$b->status] ?? $b->status,
                        $b->payment ? ($paymentLabels[$b->payment->status] ?? $b->payment->status) : 'Aucun',
                        $b->payment ? ($methodLabels[$b->payment->payment_method] ?? $b->payment->payment_method) : '',
                        $b->special_requests,
                    ], ';');
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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
        $residences = Residence::orderBy('name')->get(['id', 'name']);

        // ---------- Planning (résidences × jours du mois) ----------
        try {
            $month = Carbon::createFromFormat('Y-m', (string) $request->month)->startOfMonth();
        } catch (\Throwable $e) {
            $month = now()->startOfMonth();
        }
        $monthEnd = $month->copy()->endOfMonth();
        $days = collect(range(0, $monthEnd->day - 1))->map(fn ($i) => $month->copy()->addDays($i));

        $bookings = Booking::where('status', '!=', 'cancelled')
            ->whereDate('check_in_date', '<=', $monthEnd)
            ->whereDate('check_out_date', '>', $month)
            ->orderBy('check_in_date')
            ->get(['id', 'booking_number', 'residence_id', 'first_name', 'last_name', 'check_in_date', 'check_out_date', 'status', 'total_amount']);

        // Pour chaque résidence : suite de cellules (vides ou réservations, avec colspan)
        $planning = [];
        foreach ($residences as $residence) {
            $items = $bookings->where('residence_id', $residence->id)->values();
            $cells = [];
            $nightsBooked = 0;
            $day = 0;
            $count = $days->count();
            while ($day < $count) {
                $date = $days[$day];
                $booking = $items->first(fn ($b) => Carbon::parse($b->check_in_date)->startOfDay()->lte($date)
                    && Carbon::parse($b->check_out_date)->startOfDay()->gt($date));
                if ($booking) {
                    $last = Carbon::parse($booking->check_out_date)->startOfDay()->subDay();
                    $endIndex = min($count - 1, $last->gt($monthEnd) ? $count - 1 : $last->day - 1);
                    $span = max(1, $endIndex - $day + 1);
                    $cells[] = [
                        'booking' => $booking,
                        'span' => $span,
                        'starts_before' => Carbon::parse($booking->check_in_date)->lt($month),
                        'ends_after' => Carbon::parse($booking->check_out_date)->startOfDay()->gt($monthEnd->copy()->startOfDay()->addDay()),
                    ];
                    $nightsBooked += $span;
                    $day += $span;
                } else {
                    $cells[] = ['booking' => null, 'span' => 1, 'date' => $date];
                    $day++;
                }
            }
            $planning[] = [
                'residence' => $residence,
                'cells' => $cells,
                'occupancy' => round($nightsBooked / $count * 100),
            ];
        }

        return view('backend.pages.sages-home.bookings.calendar', compact('residences', 'planning', 'days', 'month'));
    }

    public function calendarData(Request $request)
    {
        $query = Booking::with(['residence']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', '!=', 'cancelled');
        }

        if ($request->filled('residence_id')) {
            $query->where('residence_id', $request->residence_id);
        }

        // Réservations qui chevauchent la période affichée (paramètres FullCalendar start/end)
        $start = $request->start ?? $request->start_date;
        $end = $request->end ?? $request->end_date;
        if ($start) {
            $query->whereDate('check_out_date', '>', Carbon::parse($start));
        }
        if ($end) {
            $query->whereDate('check_in_date', '<', Carbon::parse($end));
        }

        $events = $query->get()->map(function ($booking) {
            return [
                'id' => $booking->id,
                'reference' => $booking->booking_number,
                'residence_id' => $booking->residence_id,
                'residence_name' => $booking->residence?->name,
                'guest_name' => trim($booking->first_name . ' ' . $booking->last_name),
                'check_in_date' => Carbon::parse($booking->check_in_date)->toDateString(),
                'check_out_date' => Carbon::parse($booking->check_out_date)->toDateString(),
                'status' => $booking->status,
                'total_amount' => $booking->total_amount,
            ];
        });

        return response()->json($events);
    }

    public function quickView(Booking $booking)
    {
        $booking->load(['residence', 'payment']);
        $in = Carbon::parse($booking->check_in_date);
        $out = Carbon::parse($booking->check_out_date);

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'reference' => $booking->booking_number,
                'residence_name' => $booking->residence?->name,
                'guest_name' => trim($booking->first_name . ' ' . $booking->last_name),
                'phone' => $booking->phone,
                'email' => $booking->email,
                'check_in_date' => $in->toDateString(),
                'check_out_date' => $out->toDateString(),
                'check_in_label' => ucfirst($in->translatedFormat('l j F Y')),
                'check_out_label' => ucfirst($out->translatedFormat('l j F Y')),
                'nights' => $booking->nights ?: $in->diffInDays($out),
                'guests_count' => $booking->guests_count ?? $booking->guests,
                'total_amount' => $booking->total_amount,
                'status' => $booking->status,
                'payment_status' => $booking->payment?->status,
                'payment_method' => $booking->payment?->payment_method,
                'special_requests' => $booking->special_requests,
                'show_url' => route('admin.bookings.show', $booking),
                'confirm_url' => $booking->status === 'pending' ? route('admin.bookings.update-status', $booking) : null,
            ],
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

    /**
     * Calcule toutes les données du compte d'exploitation (page + export).
     */
    private function buildReport(Request $request): array
    {
        $presets = [
            'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'this_quarter' => [now()->firstOfQuarter(), now()->lastOfQuarter()],
            'this_year' => [now()->startOfYear(), now()->endOfYear()],
            'last_year' => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
        ];

        $preset = $request->preset;
        if ($preset && isset($presets[$preset])) {
            [$start, $end] = $presets[$preset];
        } else {
            $start = Carbon::parse($request->start_date ?? now()->startOfMonth());
            $end = Carbon::parse($request->end_date ?? now()->endOfMonth());
            $preset = collect($presets)->search(fn ($p) => $p[0]->isSameDay($start) && $p[1]->isSameDay($end)) ?: null;
        }
        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        $filters = [
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'residence_id' => $request->residence_id,
            'status' => $request->status,
            'preset' => $preset,
        ];

        $query = Booking::with(['residence', 'payments'])
            ->whereBetween('check_in_date', [$filters['start_date'], $filters['end_date']]);
        if ($filters['residence_id']) {
            $query->where('residence_id', $filters['residence_id']);
        }
        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }
        $bookings = $query->orderBy('check_in_date')->get();

        // Réservations « valides » : confirmées ou terminées
        $valid = $bookings->whereIn('status', ['confirmed', 'completed']);
        $nightsOf = fn ($b) => (int) ($b->nights ?: Carbon::parse($b->check_in_date)->diffInDays($b->check_out_date));
        $collectedOf = fn ($items) => $items->flatMap(fn ($b) => $b->payments->where('status', 'completed'))->sum('amount');

        $days = (int) $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay()) + 1;
        $residenceCount = $filters['residence_id'] ? 1 : max(Residence::count(), 1);
        $totalNights = $valid->sum($nightsOf);
        $booked = $valid->sum('total_amount');
        $collected = $collectedOf($valid);

        $stats = [
            'total_bookings' => $bookings->count(),
            'confirmed_bookings' => $valid->count(),
            'pending_bookings' => $bookings->where('status', 'pending')->count(),
            'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
            'booked_revenue' => $booked,
            'total_revenue' => $collected,
            'remaining' => max($booked - $collected, 0),
            'pending_revenue' => $bookings->where('status', 'pending')->sum('total_amount'),
            'total_nights' => $totalNights,
            'occupancy' => min(100, round($totalNights / ($residenceCount * $days) * 100, 1)),
            'adr' => $totalNights > 0 ? round($booked / $totalNights) : 0,
            'avg_stay' => $valid->count() > 0 ? round($totalNights / $valid->count(), 1) : 0,
            'cancellation_rate' => $bookings->count() > 0 ? round($bookings->where('status', 'cancelled')->count() / $bookings->count() * 100, 1) : 0,
            'days' => $days,
            'residence_count' => $residenceCount,
        ];

        $residenceStats = $bookings->groupBy('residence_id')->map(function ($items) use ($nightsOf, $collectedOf, $days) {
            $ok = $items->whereIn('status', ['confirmed', 'completed']);
            $nights = $ok->sum($nightsOf);
            return [
                'residence' => $items->first()->residence,
                'total_bookings' => $items->count(),
                'confirmed_bookings' => $ok->count(),
                'cancelled_bookings' => $items->where('status', 'cancelled')->count(),
                'booked_revenue' => $ok->sum('total_amount'),
                'total_revenue' => $collectedOf($ok),
                'total_nights' => $nights,
                'occupancy' => min(100, round($nights / $days * 100, 1)),
            ];
        })->sortByDesc('booked_revenue');

        $paymentMethodStats = $valid->flatMap(fn ($b) => $b->payments->where('status', 'completed'))
            ->groupBy('payment_method')
            ->map(fn ($payments) => ['count' => $payments->count(), 'total' => $payments->sum('amount')])
            ->sortByDesc('total');

        return compact('filters', 'bookings', 'stats', 'residenceStats', 'paymentMethodStats');
    }

    public function report(Request $request)
    {
        $residences = Residence::orderBy('name')->get();

        return view('backend.pages.sages-home.bookings.report', array_merge(
            ['residences' => $residences],
            $this->buildReport($request)
        ));
    }

    /**
     * Export CSV (Excel) du compte d'exploitation : synthèse + détail.
     */
    public function reportExport(Request $request)
    {
        $data = $this->buildReport($request);
        $filters = $data['filters'];
        $stats = $data['stats'];
        $bookings = $data['bookings'];
        $residenceStats = $data['residenceStats'];
        $paymentMethodStats = $data['paymentMethodStats'];

        $statusLabels = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'cancelled' => 'Annulée', 'completed' => 'Terminée'];
        $methodLabels = ['cash' => 'Espèces', 'wave' => 'Wave', 'paypal' => 'PayPal', 'bank_transfer' => 'Virement', 'credit_card' => 'Carte bancaire'];
        $period = Carbon::parse($filters['start_date'])->format('d/m/Y') . ' au ' . Carbon::parse($filters['end_date'])->format('d/m/Y');
        $filename = 'compte_exploitation_' . $filters['start_date'] . '_' . $filters['end_date'] . '.csv';
        $num = fn ($v) => str_replace('.', ',', (string) $v);

        return response()->streamDownload(function () use ($filters, $stats, $bookings, $residenceStats, $paymentMethodStats, $statusLabels, $methodLabels, $period, $num) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            $row = function (array $cells) use ($out) {
                fputcsv($out, $cells, ';');
            };

            $row(["SAGES HOME — Compte d'exploitation"]);
            $row(['Période', $period]);
            if ($filters['residence_id']) {
                $row(['Résidence', optional(Residence::find($filters['residence_id']))->name]);
            }
            $row(['Généré le', now()->format('d/m/Y H:i')]);
            $row([]);

            $row(['SYNTHÈSE']);
            $row(['Réservations', $stats['total_bookings']]);
            $row(['Confirmées / terminées', $stats['confirmed_bookings']]);
            $row(['En attente', $stats['pending_bookings']]);
            $row(['Annulées', $stats['cancelled_bookings']]);
            $row(['Nuitées', $stats['total_nights']]);
            $row(["Taux d'occupation (%)", $num($stats['occupancy'])]);
            $row(['Prix moyen par nuit (FCFA)', $stats['adr']]);
            $row(['Durée moyenne de séjour (nuits)', $num($stats['avg_stay'])]);
            $row(["Chiffre d'affaires réservé (FCFA)", (int) $stats['booked_revenue']]);
            $row(['Encaissé (FCFA)', (int) $stats['total_revenue']]);
            $row(['Reste à encaisser (FCFA)', (int) $stats['remaining']]);
            $row([]);

            $row(['PAR RÉSIDENCE']);
            $row(['Résidence', 'Réservations', 'Confirmées', 'Annulées', 'Nuitées', 'Occupation (%)', 'CA réservé (FCFA)', 'Encaissé (FCFA)']);
            foreach ($residenceStats as $s) {
                $row([$s['residence']?->name, $s['total_bookings'], $s['confirmed_bookings'], $s['cancelled_bookings'], $s['total_nights'],
                    $num($s['occupancy']), (int) $s['booked_revenue'], (int) $s['total_revenue']]);
            }
            $row([]);

            $row(['PAR MOYEN DE PAIEMENT']);
            $row(['Moyen', 'Transactions', 'Montant (FCFA)']);
            foreach ($paymentMethodStats as $method => $s) {
                $row([$methodLabels[$method] ?? $method, $s['count'], (int) $s['total']]);
            }
            $row([]);

            $row(['DÉTAIL DES RÉSERVATIONS']);
            $row(['Référence', 'Client', 'Téléphone', 'Résidence', 'Arrivée', 'Départ', 'Nuits', 'Montant (FCFA)', 'Encaissé (FCFA)', 'Statut']);
            foreach ($bookings as $b) {
                $row([
                    $b->booking_number,
                    trim($b->first_name . ' ' . $b->last_name),
                    $b->phone ? "=\"" . $b->phone . "\"" : "", // texte pour garder le 0 initial dans Excel
                    $b->residence?->name,
                    Carbon::parse($b->check_in_date)->format('d/m/Y'),
                    Carbon::parse($b->check_out_date)->format('d/m/Y'),
                    $b->nights ?: Carbon::parse($b->check_in_date)->diffInDays($b->check_out_date),
                    (int) $b->total_amount,
                    (int) $b->payments->where('status', 'completed')->sum('amount'),
                    $statusLabels[$b->status] ?? $b->status,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * PDF de la liste des réservations (mêmes filtres que la liste).
     */
    public function exportPdf(Request $request)
    {
        $query = $this->applyListFilters(Booking::with(['residence', 'payment']), $request);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $bookings = $this->applySort($query, $request)->limit(2000)->get();

        $parts = [];
        if ($request->filled('status')) { $parts[] = ['pending' => 'En attente', 'confirmed' => 'Confirmées', 'completed' => 'Terminées', 'cancelled' => 'Annulées'][$request->status] ?? $request->status; }
        if ($request->filled('residence_id')) { $parts[] = optional(Residence::find($request->residence_id))->name; }
        if ($request->filled('date_from')) { $parts[] = 'dès le ' . Carbon::parse($request->date_from)->format('d/m/Y'); }
        if ($request->filled('date_to')) { $parts[] = 'jusqu\x27au ' . Carbon::parse($request->date_to)->format('d/m/Y'); }
        if ($request->filled('search')) { $parts[] = '« ' . $request->search . ' »'; }
        $filterSummary = implode(' · ', array_filter($parts));

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('backend.pdf.bookings', compact('bookings', 'filterSummary'))
            ->setPaper('a4', 'landscape')
            ->download('reservations_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Bon de réservation (une réservation).
     */
    public function bookingPdf(Booking $booking)
    {
        $booking->load(['residence', 'payments']);

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('backend.pdf.booking', compact('booking'))
            ->setPaper('a4')
            ->download('reservation_' . $booking->booking_number . '.pdf');
    }

    /**
     * Compte d'exploitation en PDF.
     */
    public function reportPdf(Request $request)
    {
        $data = $this->buildReport($request);
        $data['residenceName'] = $data['filters']['residence_id'] ? optional(Residence::find($data['filters']['residence_id']))->name : null;

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('backend.pdf.report', $data)
            ->setPaper('a4', 'landscape')
            ->download('compte_exploitation_' . $data['filters']['start_date'] . '_' . $data['filters']['end_date'] . '.pdf');
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
