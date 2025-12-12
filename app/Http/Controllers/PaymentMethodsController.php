<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Auth;

class PaymentMethodsController extends Controller
{
    protected $paypalService;
    protected $currencyService;

    public function __construct(PayPalService $paypalService, CurrencyService $currencyService)
    {
        $this->paypalService = $paypalService;
        $this->currencyService = $currencyService;
    }

    /**
     * Capturer un paiement PayPal après approbation
     */
    public function capturePayPal(Request $request, Payment $payment)
    {
        // Reconnecter l'utilisateur si nécessaire
        if (!Auth::check() && $payment->booking->user_id) {
            Auth::loginUsingId($payment->booking->user_id);
        }

        // Vérifier que le paiement appartient à l'utilisateur
        if ($payment->booking->user_id !== Auth::id()) {
            abort(403);
        }

        // Vérifier si le paiement n'est pas déjà complété
        if ($payment->status === 'completed') {
            return redirect()->route('booking.confirmation', $payment->booking)
                ->with('success', 'Votre paiement a déjà été confirmé.');
        }

        try {
            // Capturer le paiement
            $result = $this->paypalService->captureOrder($payment->transaction_id);

            if ($result['success'] && $result['status'] === 'COMPLETED') {
                // Mettre à jour le paiement
                $paymentDataStr = $payment->payment_data;
                $paymentData = is_string($paymentDataStr) ? json_decode($paymentDataStr, true) : [];
                $paymentData = is_array($paymentData) ? $paymentData : [];
                $paymentData['captured_at'] = now()->toISOString();
                $paymentData['capture_data'] = $result['data'];

                $payment->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'payment_data' => json_encode($paymentData)
                ]);

                // Confirmer la réservation
                $payment->booking->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'confirmed_at' => now()
                ]);

                \Log::info('Paiement PayPal capturé avec succès', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->transaction_id
                ]);

                return redirect()->route('booking.confirmation', $payment->booking)
                    ->with('success', 'Votre paiement PayPal a été traité avec succès.');
            } else {
                \Log::warning('Échec capture paiement PayPal', [
                    'payment_id' => $payment->id,
                    'result' => $result
                ]);

                $paymentDataStr = $payment->payment_data;
                $paymentData = is_string($paymentDataStr) ? json_decode($paymentDataStr, true) : [];
                $paymentData = is_array($paymentData) ? $paymentData : [];
                $paymentData['error'] = $result['error'] ?? 'Erreur inconnue';

                $payment->update([
                    'status' => 'failed',
                    'payment_data' => json_encode($paymentData)
                ]);

                return redirect()->route('booking.payment', $payment->booking)
                    ->with('error', 'Le paiement PayPal a échoué. Veuillez réessayer.');
            }

        } catch (\Exception $e) {
            \Log::error('Exception capture PayPal: ' . $e->getMessage());

            return redirect()->route('booking.payment', $payment->booking)
                ->with('error', 'Une erreur est survenue lors du traitement du paiement.');
        }
    }

    /**
     * Changer la devise de l'utilisateur
     */
    public function changeCurrency(Request $request)
    {
        $request->validate([
            'currency' => 'required|in:FCFA,EUR,USD'
        ]);

        $this->currencyService->setUserCurrency($request->currency);

        return back()->with('success', 'Devise changée avec succès');
    }
}
