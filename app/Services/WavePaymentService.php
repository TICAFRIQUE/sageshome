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
    public function createCheckoutSession($amount, $successUrl, $errorUrl, $clientReference = null)
    {
        try {
            $checkoutParams = [
                'amount' => (string) $amount,
                'currency' => 'XOF',
                'success_url' => $successUrl,
                'error_url' => $errorUrl,
            ];

            // Ajouter la référence client si fournie (champ autorisé par Wave)
            if (!empty($clientReference)) {
                $checkoutParams['client_reference'] = (string) $clientReference;
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
                Log::error('Erreur vérification statut Wave: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'Impossible de vérifier le statut du paiement',
                    'details' => $response->json(),
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

    /**
     * Vérifier si un webhook Wave est valide
     */
    public function validateWebhook($payload, $signature = null)
    {
        // Pour l'instant, validation basique
        // Dans un environnement de production, vous devriez vérifier la signature du webhook
        return isset($payload['id']) && isset($payload['checkout_status']);
    }

    /**
     * Obtenir les détails d'un paiement via client_reference
     */
    public function getPaymentByReference($clientReference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(10)
            ->get($this->apiUrl . '/checkout/sessions', [
                'client_reference' => $clientReference,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'data' => $data['result'] ?? $data,
                ];
            } else {
                Log::error('Erreur recherche paiement Wave: ' . $response->body());
                return [
                    'success' => false,
                    'error' => 'Impossible de trouver le paiement',
                    'details' => $response->json(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Erreur recherche paiement Wave: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur de connexion: ' . $e->getMessage(),
            ];
        }
    }
}