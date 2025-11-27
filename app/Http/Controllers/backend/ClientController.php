<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereHas('roles', function($q) {
                $q->where('name', 'client');
            })
            ->withCount(['bookings'])
            ->with(['bookings' => function($q) {
                $q->latest()->limit(3);
            }]);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNotNull('deleted_at')->withTrashed();
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistiques
        $stats = [
            'total' => User::whereHas('roles', function($q) {
                $q->where('name', 'client');
            })->count(),
            'active' => User::whereHas('roles', function($q) {
                $q->where('name', 'client');
            })->whereNull('deleted_at')->count(),
            'new_this_month' => User::whereHas('roles', function($q) {
                $q->where('name', 'client');
            })->whereMonth('created_at', now()->month)->count(),
            'with_bookings' => User::whereHas('roles', function($q) {
                $q->where('name', 'client');
            })->has('bookings')->count(),
        ];

        return view('backend.pages.sages-home.clients.index', compact('clients', 'stats'));
    }

    public function show(User $client)
    {
        $client->load(['bookings.residence', 'bookings.payments']);

        // Statistiques du client
        $analytics = [
            'total_bookings' => $client->bookings->count(),
            'confirmed_bookings' => $client->bookings->where('status', 'confirmed')->count(),
            'cancelled_bookings' => $client->bookings->where('status', 'cancelled')->count(),
            'pending_bookings' => $client->bookings->where('status', 'pending')->count(),
            'total_amount' => $client->bookings->where('status', 'confirmed')->sum('total_amount') ?? 0,
        ];

        return view('backend.pages.sages-home.clients.show', compact('client', 'analytics'));
    }

    public function create()
    {
        return view('backend.pages.sages-home.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
        ]);

        // Assigner le rôle client
        $user->assignRole('client');

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    public function edit(User $client)
    {
        return view('backend.pages.sages-home.clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $client->update($data);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Client modifié avec succès.');
    }

    public function destroy(User $client)
    {
        // Soft delete
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client désactivé avec succès.');
    }

    public function restore(User $client)
    {
        $client->restore();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client réactivé avec succès.');
    }

    public function export(Request $request)
    {
        $clients = User::whereHas('roles', function($q) {
                $q->where('name', 'client');
            })
            ->withCount('bookings')
            ->get();

        $filename = 'clients_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nom', 'Email', 'Téléphone', 'Ville', 'Pays', 'Réservations', 'Date d\'inscription']);

            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->username,
                    $client->email,
                    $client->phone ?? 'N/A',
                    $client->city ?? 'N/A',
                    $client->country ?? 'N/A',
                    $client->bookings_count,
                    $client->created_at->format('d/m/Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}