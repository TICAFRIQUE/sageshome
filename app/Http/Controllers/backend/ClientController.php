<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PasswordGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    private function clientsQuery()
    {
        // Clients inscrits sur le site (colonne role) ou créés avec le rôle Spatie
        return User::where(function ($q) {
            $q->where('role', 'client')
                ->orWhereHas('roles', fn ($r) => $r->where('name', 'client'));
        });
    }

    public function index(Request $request)
    {
        $query = $this->clientsQuery()
            ->withCount(['bookings'])
            ->with(['bookings' => fn ($q) => $q->latest()->limit(3)]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->status === 'inactive') {
            $query->onlyTrashed();
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'total' => $this->clientsQuery()->withTrashed()->count(),
            'active' => $this->clientsQuery()->count(),
            'new_this_month' => $this->clientsQuery()->whereBetween('created_at', [now()->startOfMonth(), now()])->count(),
            'with_bookings' => $this->clientsQuery()->has('bookings')->count(),
        ];

        return view('backend.pages.sages-home.clients.index', compact('clients', 'stats'));
    }

    public function show(User $client)
    {
        $client->load(['bookings.residence', 'bookings.payments']);

        $valid = $client->bookings->whereIn('status', ['confirmed', 'completed']);
        $analytics = [
            'total_bookings' => $client->bookings->count(),
            'confirmed_bookings' => $valid->count(),
            'cancelled_bookings' => $client->bookings->where('status', 'cancelled')->count(),
            'pending_bookings' => $client->bookings->where('status', 'pending')->count(),
            'total_amount' => $valid->sum('total_amount'),
        ];

        return view('backend.pages.sages-home.clients.show', compact('client', 'analytics'));
    }

    public function create()
    {
        $generatedPassword = PasswordGenerator::generate();

        return view('backend.pages.sages-home.clients.form', compact('generatedPassword'));
    }

    public function store(Request $request)
    {
        $request->merge(['password' => strtoupper(trim((string) $request->password)) ?: null]);

        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'nullable|string|min:8|max:64',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ], [
            'email.unique' => 'Un compte existe déjà avec cette adresse e-mail.',
            'phone.unique' => 'Un compte existe déjà avec ce numéro de téléphone.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ]);

        // Mot de passe généré automatiquement si le champ est vide
        $password = $validated['password'] ?? PasswordGenerator::generate();

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($password),
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
            'role' => 'client',
        ]);

        $user->assignRole('client');

        return redirect()->route('admin.clients.show', $user)
            ->with('success', 'Client créé avec succès.')
            ->with('generated_password', $password);
    }

    public function edit(User $client)
    {
        return view('backend.pages.sages-home.clients.form', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($client->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($client->id)],
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ], [
            'email.unique' => 'Un compte existe déjà avec cette adresse e-mail.',
            'phone.unique' => 'Un compte existe déjà avec ce numéro de téléphone.',
        ]);

        $data = $validated;
        if ($request->has('email_verified_present')) {
            $data['email_verified_at'] = $request->boolean('email_verified')
                ? ($client->email_verified_at ?? now())
                : null;
        }

        $client->update($data);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Client modifié avec succès.');
    }

    /**
     * Réinitialise le mot de passe : 8 caractères (majuscules + chiffres),
     * affiché une seule fois à l'administrateur.
     */
    public function resetPassword(User $client)
    {
        $password = PasswordGenerator::generate();
        $client->update(['password' => Hash::make($password)]);

        return redirect()->route('admin.clients.show', $client)
            ->with('success', 'Nouveau mot de passe généré.')
            ->with('generated_password', $password);
    }

    public function destroy(User $client)
    {
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
        $clients = $this->clientsQuery()->withTrashed()->withCount('bookings')->orderBy('username')->get();
        $filename = 'clients_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($clients) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
            fputcsv($file, ['Nom', 'E-mail', 'Téléphone', 'Adresse', 'Ville', 'Pays', 'Réservations', "Date d'inscription", 'Statut'], ';');

            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->username,
                    $client->email,
                    $client->phone ? '="' . $client->phone . '"' : '',
                    $client->address,
                    $client->city,
                    $client->country,
                    $client->bookings_count,
                    $client->created_at?->format('d/m/Y'),
                    $client->deleted_at ? 'Inactif' : 'Actif',
                ], ';');
            }
            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
