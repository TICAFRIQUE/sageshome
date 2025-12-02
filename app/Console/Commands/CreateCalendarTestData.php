<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Residence;
use App\Models\User;
use App\Models\Booking;
use App\Models\AvailabilityCalendar;
use Carbon\Carbon;

class CreateCalendarTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendar:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer des données de test pour le calendrier de disponibilité';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Création de données de test pour le calendrier ===');

        // Récupérer la première résidence
        $residence = Residence::first();
        if (!$residence) {
            $this->error('Aucune résidence trouvée');
            return 1;
        }

        $this->info("Résidence sélectionnée: {$residence->name} (ID: {$residence->id})");

        // Récupérer ou créer un utilisateur test
        $user = User::first();
        if (!$user) {
            $this->error('Aucun utilisateur trouvé');
            return 1;
        }

        $this->info("Utilisateur sélectionné: {$user->email}");

        // Créer quelques réservations de test
        $today = Carbon::today();
        
        // Réservation 1: Dans 5 jours pour 3 nuits
        $checkIn1 = $today->copy()->addDays(5);
        $checkOut1 = $checkIn1->copy()->addDays(3);
        
        $booking1 = Booking::create([
            'user_id' => $user->id,
            'residence_id' => $residence->id,
            'check_in' => $checkIn1,
            'check_out' => $checkOut1,
            'check_in_date' => $checkIn1,
            'check_out_date' => $checkOut1,
            'guests' => 2,
            'guests_count' => 2,
            'nights' => 3,
            'price_per_night' => $residence->price_per_night,
            'total_price' => $residence->price_per_night * 3,
            'subtotal_amount' => $residence->price_per_night * 3,
            'tax_amount' => ($residence->price_per_night * 3) * 0.1,
            'final_amount' => ($residence->price_per_night * 3) * 1.1,
            'total_amount' => ($residence->price_per_night * 3) * 1.1,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'first_name' => $user->username,
            'last_name' => '',
            'email' => $user->email,
            'phone' => $user->phone ?? '123456789',
            'country' => 'Sénégal',
        ]);
        
        $this->info("Réservation 1 créée: {$checkIn1->format('d/m/Y')} - {$checkOut1->format('d/m/Y')}");

        // Réservation 2: Dans 15 jours pour 2 nuits
        $checkIn2 = $today->copy()->addDays(15);
        $checkOut2 = $checkIn2->copy()->addDays(2);
        
        $booking2 = Booking::create([
            'user_id' => $user->id,
            'residence_id' => $residence->id,
            'check_in' => $checkIn2,
            'check_out' => $checkOut2,
            'check_in_date' => $checkIn2,
            'check_out_date' => $checkOut2,
            'guests' => 4,
            'guests_count' => 4,
            'nights' => 2,
            'price_per_night' => $residence->price_per_night,
            'total_price' => $residence->price_per_night * 2,
            'subtotal_amount' => $residence->price_per_night * 2,
            'tax_amount' => ($residence->price_per_night * 2) * 0.1,
            'final_amount' => ($residence->price_per_night * 2) * 1.1,
            'total_amount' => ($residence->price_per_night * 2) * 1.1,
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'first_name' => $user->username,
            'last_name' => '',
            'email' => $user->email,
            'phone' => $user->phone ?? '123456789',
            'country' => 'Sénégal',
        ]);
        
        $this->info("Réservation 2 créée: {$checkIn2->format('d/m/Y')} - {$checkOut2->format('d/m/Y')}");

        // Créer quelques dates indisponibles dans le calendrier (maintenance, etc.)
        
        // Période de maintenance: dans 10 jours pour 2 jours
        $maintenanceStart = $today->copy()->addDays(10);
        for ($i = 0; $i < 2; $i++) {
            AvailabilityCalendar::create([
                'residence_id' => $residence->id,
                'date' => $maintenanceStart->copy()->addDays($i),
                'is_available' => false,
                'min_stay' => null,
                'price_override' => null,
            ]);
        }
        
        $this->info("Période de maintenance créée: {$maintenanceStart->format('d/m/Y')} - {$maintenanceStart->copy()->addDay()->format('d/m/Y')}");

        // Quelques dates avec prix spéciaux (weekend, etc.)
        $specialPriceDate = $today->copy()->addDays(20);
        AvailabilityCalendar::create([
            'residence_id' => $residence->id,
            'date' => $specialPriceDate,
            'is_available' => true,
            'min_stay' => null,
            'price_override' => $residence->price_per_night * 1.5, // Prix majoré de 50%
        ]);
        
        $this->info("Date à prix spécial créée: {$specialPriceDate->format('d/m/Y')} (Prix: " . number_format($residence->price_per_night * 1.5, 0) . " FCFA)");

        $this->info("\n🎉 Données de test créées avec succès !");
        $this->info("📊 Résumé:");
        $this->info("- 2 réservations confirmées");
        $this->info("- 2 jours de maintenance");  
        $this->info("- 1 jour à prix spécial");
        $this->info("\n💡 Vous pouvez maintenant tester le calendrier sur la page de la résidence");

        return 0;
    }
}
