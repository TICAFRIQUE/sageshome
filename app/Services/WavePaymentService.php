<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WavePaymentService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.wave.api_key');
        $this->apiUrl = config('services.wave.api_url', 'https://api.wave.com/v1');
    }

    /**
     * Créer une session de checkout Wave
     */
    public function createCheckoutSession($amount, $successUrl, $errorUrl, $metadata = [])
    {
        try {
            $checkoutParams = [
                'amount' => (string) $amount,
                'currency' => 'XOF',
                'success_url' => $successUrl,
                'error_url' => $errorUrl,
            ];

            // Ajouter les métadonnées si fournies
            if (!empty($metadata)) {
                $checkoutParams['metadata'] = $metadata;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(10)
            ->post($this->apiUrl . '/checkout/sessions', $checkoutParams);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            } else {
                Log::error('Erreur API Wave: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'Erreur lors de la création de la session de paiement Wave',
                    'details' => $response->json(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception Wave API: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur de connexion à Wave: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifier le statut d'un paiement
     */
    public function getPaymentStatus($sessionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(10)
            ->get($this->apiUrl . '/checkout/sessions/' . $sessionId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Impossible de vérifier le statut du paiement',
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erreur vérification statut Wave: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur de connexion: ' . $e->getMessage(),
            ];
        }
    }
}