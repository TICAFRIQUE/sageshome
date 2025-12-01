# Intégration Wave Payment - Documentation

## Configuration

### 1. Variables d'environnement
Ajoutez ces variables à votre fichier `.env` :

```bash
# Wave Payment Configuration
WAVE_API_KEY=wave_sn_prod_YhUNb9d...i4bA6
WAVE_API_URL=https://api.wave.com/v1
```

### 2. Obtenir votre clé API Wave
1. Connectez-vous à votre compte Wave Business
2. Allez dans les paramètres API
3. Générez une nouvelle clé API de production
4. Remplacez la valeur dans `WAVE_API_KEY`

## Fonctionnalités implémentées

### 1. Service WavePaymentService
- **Localisation** : `app/Services/WavePaymentService.php`
- **Fonctions** :
  - `createCheckoutSession()` : Créer une session de paiement Wave
  - `getPaymentStatus()` : Vérifier le statut d'un paiement

### 2. Intégration dans BookingController
- **Méthode `redirectToWave()`** : Redirige vers Wave avec les paramètres corrects
- **Méthode `confirmPayment()`** : Vérifie et confirme le paiement
- **Méthode `waveWebhook()`** : Reçoit les notifications de Wave

### 3. Améliorations du modèle Payment
- Nouvelle colonne `payment_data` : Stocke les données JSON de Wave
- Nouvelle colonne `completed_at` : Date de completion du paiement

## Flux de paiement Wave

### 1. Processus utilisateur
1. L'utilisateur choisit "Paiement Wave" lors de la réservation
2. Une session de paiement est créée via l'API Wave
3. L'utilisateur est redirigé vers l'interface Wave
4. Après paiement, Wave renvoie l'utilisateur vers votre site
5. Le statut est vérifié et la réservation confirmée

### 2. URLs de retour
- **Success URL** : `/payment/{payment}/confirm`
- **Error URL** : `/booking/{booking}/payment?error=wave_payment_failed`
- **Webhook URL** : `/webhook/wave/payment`

## Sécurité et debugging

### 1. Logs
Tous les événements Wave sont loggés dans `storage/logs/laravel.log` :
- Créations de sessions
- Webhooks reçus
- Erreurs API

### 2. Gestion d'erreurs
- Timeouts API (10 secondes)
- Vérification des réponses Wave
- Fallback en cas d'échec

## Test de l'intégration

### 1. En mode développement
1. Utilisez les clés API de sandbox Wave
2. Modifiez `WAVE_API_URL` si nécessaire
3. Testez avec de petits montants

### 2. Webhook local
Pour tester les webhooks en local, utilisez ngrok :
```bash
ngrok http 8000
```
Puis configurez l'URL webhook dans Wave : `https://votre-url.ngrok.io/webhook/wave/payment`

## Dépannage

### Problèmes courants
1. **Erreur "Session ID manquant"** : Vérifiez la configuration API
2. **Timeout** : Vérifiez votre connexion internet
3. **Paiement non confirmé** : Vérifiez les webhooks Wave

### Vérification manuelle
Si un paiement semble bloqué, vous pouvez vérifier manuellement son statut en utilisant l'ID de session dans les logs.

## Support

Pour toute assistance technique :
- Documentation officielle Wave : [https://docs.wave.com](https://docs.wave.com)
- Support Wave : support@wave.com