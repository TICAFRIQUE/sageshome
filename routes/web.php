<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ResidenceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\backend\DashboardController as BackendDashboardController;
use App\Http\Controllers\backend\AdminController;
use App\Http\Controllers\backend\ModuleController;
use App\Http\Controllers\backend\RoleController;
use App\Http\Controllers\backend\ParametreController;
use App\Http\Controllers\backend\PermissionController;
use App\Http\Controllers\backend\ResidenceController as AdminResidenceController;
use App\Http\Controllers\backend\BookingController as AdminBookingController;
use App\Http\Controllers\backend\ResidenceTypeController;
use App\Http\Controllers\TestEmailController;



Route::fallback(function () {
    return view('backend.utility.auth-404-basic');
});

// Routes de test email (à supprimer en production)
Route::get('/test-email', [TestEmailController::class, 'testEmail'])->name('test.email');
Route::get('/test-admin-email', [TestEmailController::class, 'testAdminEmail'])->name('test.admin.email');

// Routes publiques (front-end)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Routes des résidences
Route::prefix('residences')->group(function () {
    Route::get('/', [ResidenceController::class, 'index'])->name('residences.index');
    Route::get('/{residence:slug}', [ResidenceController::class, 'show'])->name('residences.show');
    Route::post('/{residence}/check-availability', [ResidenceController::class, 'checkAvailability'])->name('residences.check-availability');
    Route::get('/{residence}/unavailable-dates', [ResidenceController::class, 'getUnavailableDates'])->name('residences.unavailable-dates');
});

// API routes pour les filtres
Route::prefix('api')->group(function () {
    Route::get('/communes/{ville}', function ($ville) {
        return response()->json(
            \App\Models\Residence::getAvailableCommunes($ville)
        );
    })->name('api.communes');

    // Route pour vérifier le statut d'un paiement
    Route::get('/payment/{payment}/status', [BookingController::class, 'getPaymentStatus'])->name('api.payment.status');
});

// Routes de réservation (authentification requise)
Route::middleware(['auth'])->prefix('client')->group(function () {
    // Dashboard client
    Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard.index');
        Route::get('/bookings', 'bookings')->name('dashboard.bookings');
        Route::get('/bookings/{booking}', 'bookingShow')->name('dashboard.booking.show');
        Route::patch('/bookings/{booking}/cancel', 'cancelBooking')->name('dashboard.booking.cancel');
        Route::get('/profile', 'profile')->name('dashboard.profile');
        Route::patch('/profile', 'updateProfile')->name('dashboard.profile.update');
        Route::patch('/profile/password', 'updatePassword')->name('dashboard.profile.password');
    });

    Route::prefix('booking')->group(function () {
        Route::get('/create/{residence}', [BookingController::class, 'create'])->name('booking.create');
        Route::post('/store', [BookingController::class, 'store'])->name('booking.store');
        Route::get('/payment/{booking}', [BookingController::class, 'payment'])->name('booking.payment');
        Route::post('/process-payment/{booking}', [BookingController::class, 'processPayment'])->name('booking.process-payment');
        Route::get('/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation');

        // Redirections vers le dashboard
        Route::get('/my-bookings', function () {
            return redirect()->route('dashboard.bookings');
        })->name('booking.my-bookings');

        Route::get('/{booking}', function ($booking) {
            return redirect()->route('dashboard.booking.show', $booking);
        })->name('booking.show');

        Route::post('/{booking}/cancel', function ($booking) {
            return redirect()->route('dashboard.booking.cancel', $booking);
        })->name('booking.cancel');
    });
});

// Routes de paiement (callback) - SANS AUTH pour permettre le retour Wave
Route::get('/payment/{payment}/confirm', [BookingController::class, 'confirmPayment'])->name('payment.confirm');
Route::post('/payment/{payment}/confirm', [BookingController::class, 'confirmPayment']);

// Webhook Wave pour les notifications de paiement - SANS AUTH
Route::post('/webhook/wave/payment', [BookingController::class, 'waveWebhook'])->name('wave.webhook');

// Routes d'authentification Laravel par défaut
// require __DIR__.'/auth.php';

Route::middleware(['admin'])->prefix('admin')->group(function () {

    // login and logout
    Route::controller(AdminController::class)->group(function () {
        route::get('/login', 'login')->name('admin.login')->withoutMiddleware('admin'); // page formulaire de connexion
        route::post('/login', 'login')->name('admin.login')->withoutMiddleware('admin'); // envoi du formulaire
        route::post('/logout', 'logout')->name('admin.logout');
    });



    // dashboard admin
    Route::get('/', [BackendDashboardController::class, 'index'])->name('dashboard.index');

    // parametre application
    Route::prefix('parametre')->controller(ParametreController::class)->group(function () {
        route::get('', 'index')->name('parametre.index');
        route::post('store', 'store')->name('parametre.store');
        route::get('maintenance-up', 'maintenanceUp')->name('parametre.maintenance-up');
        route::get('maintenance-down', 'maintenanceDown')->name('parametre.maintenance-down');
        route::get('optimize-clear', 'optimizeClear')->name('parametre.optimize-clear');
        Route::get('download-backup/{file}', 'downloadBackup')->name('setting.download-backup');  // download backup db
    });


    //register admin
    Route::prefix('register')->controller(AdminController::class)->group(function () {
        route::get('', 'index')->name('admin-register.index');
        route::post('store', 'store')->name('admin-register.store');
        route::post('update/{id}', 'update')->name('admin-register.update');
        route::delete('delete/{id}', 'delete')->name('admin-register.delete');
        route::get('profil/{id}', 'profil')->name('admin-register.profil');
        route::post('change-password', 'changePassword')->name('admin-register.new-password');
    });

    //role
    Route::prefix('role')->controller(RoleController::class)->group(function () {
        route::get('', 'index')->name('role.index');
        route::post('store', 'store')->name('role.store');
        route::post('update/{id}', 'update')->name('role.update');
        route::delete('delete/{id}', 'delete')->name('role.delete');
    });

    //permission
    Route::prefix('permission')->controller(PermissionController::class)->group(function () {
        route::get('', 'index')->name('permission.index');
        route::get('create', 'create')->name('permission.create');
        route::post('store', 'store')->name('permission.store');
        route::get('edit{id}', 'edit')->name('permission.edit');
        route::put('update/{id}', 'update')->name('permission.update');
        route::delete('delete/{id}', 'delete')->name('permission.delete');
    });

    //module
    Route::prefix('module')->controller(ModuleController::class)->group(function () {
        route::get('', 'index')->name('module.index');
        route::post('store', 'store')->name('module.store');
        route::post('update/{id}', 'update')->name('module.update');
        route::delete('delete/{id}', 'delete')->name('module.delete');
    });

    // Nouvelles routes pour la gestion des résidences et réservations
    Route::prefix('sages-home')->group(function () {
        // Dashboard Sages Home
        Route::get('/', [BackendDashboardController::class, 'sagesHomeDashboard'])->name('admin.sages-home.dashboard');

        // Route de compatibilité (redirection)
        Route::get('/dashboard', function () {
            return redirect()->route('admin.sages-home.dashboard');
        })->name('admin.dashboard');

        // Gestion des résidences
        Route::prefix('residences')->controller(AdminResidenceController::class)->group(function () {
            Route::get('/', 'index')->name('admin.residences.index');
            Route::get('/create', 'create')->name('admin.residences.create');
            Route::post('/store', 'store')->name('admin.residences.store');
            Route::get('/{residence}', 'show')->name('admin.residences.show');
            Route::get('/{residence}/edit', 'edit')->name('admin.residences.edit');
            Route::put('/{residence}', 'update')->name('admin.residences.update');
            Route::delete('/{residence}', 'destroy')->name('admin.residences.destroy');
            Route::delete('/{image}/image', 'deleteImage')->name('admin.residences.delete-image');
            Route::post('/{image}/make-primary', 'setPrimaryImage')->name('admin.residences.make-primary');
        });

        // Gestion des types de résidences
        Route::prefix('residence-types')->controller(ResidenceTypeController::class)->group(function () {
            Route::get('/', 'index')->name('admin.residence-types.index');
            Route::get('/create', 'create')->name('admin.residence-types.create');
            Route::post('/', 'store')->name('admin.residence-types.store');
            Route::get('/{residenceType:slug}', 'show')->name('admin.residence-types.show');
            Route::get('/{residenceType:slug}/edit', 'edit')->name('admin.residence-types.edit');
            Route::put('/{residenceType:slug}', 'update')->name('admin.residence-types.update');
            Route::delete('/{residenceType:slug}', 'destroy')->name('admin.residence-types.destroy');
        });

        // Gestion des réservations
        Route::prefix('bookings')->controller(AdminBookingController::class)->group(function () {
            Route::get('/', 'index')->name('admin.bookings.index');
            Route::get('/calendar', 'calendar')->name('admin.bookings.calendar');
            Route::get('/calendar-data', 'calendarData')->name('admin.bookings.calendar-data');
            Route::get('/new-bookings', 'getNewBookings')->name('admin.bookings.new-bookings');
            Route::post('/mark-as-seen', 'markAsSeen')->name('admin.bookings.mark-as-seen');
            Route::get('/{booking}', 'show')->name('admin.bookings.show');
            Route::get('/{booking}/quick-view', 'quickView')->name('admin.bookings.quick-view');
            Route::patch('/{booking}/confirm', 'confirm')->name('admin.bookings.confirm');
            Route::patch('/{booking}/cancel', 'cancel')->name('admin.bookings.cancel');
            Route::post('/{booking}/status', 'updateStatus')->name('admin.bookings.update-status');
            Route::post('/{booking}/payment', 'confirmPayment')->name('admin.bookings.confirm-payment');
        });

        // Gestion des clients
        Route::prefix('clients')->controller(\App\Http\Controllers\backend\ClientController::class)->group(function () {
            Route::get('/', 'index')->name('admin.clients.index');
            Route::get('/create', 'create')->name('admin.clients.create');
            Route::post('/', 'store')->name('admin.clients.store');
            Route::get('/export', 'export')->name('admin.clients.export');
            Route::get('/{user}', 'show')->name('admin.clients.show');
            Route::get('/{user}/edit', 'edit')->name('admin.clients.edit');
            Route::put('/{user}', 'update')->name('admin.clients.update');
            Route::delete('/{user}', 'destroy')->name('admin.clients.destroy');
            Route::post('/{user}/restore', 'restore')->name('admin.clients.restore');
        });
    });
});
