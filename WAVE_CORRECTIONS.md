# Corrections API Wave Payment

## Problème identifié
L'API Wave rejetait les requêtes avec l'erreur :
```
"code": "request-validation-error", 
"message": "Request invalid", 
"details": [{"loc": ["metadata"], "msg": "extra fields not permitted", "type": "value_error.extra"}]
```

## Corrections apportées

### 1. Service WavePaymentService
- **Suppression** du paramètre `metadata` non autorisé par l'API Wave
- **Ajout** du paramètre `client_reference` qui est supporté officiellement
- **Amélioration** de la gestion des erreurs avec details des réponses API

### 2. Controller BookingController
- **Modification** de l'utilisation du service pour utiliser `client_reference` au format `payment_{ID}`
- **Correction** de la validation des statuts de paiement (`checkout_status` + `payment_status`)
- **Amélioration** du webhook pour gérer `client_reference` et retrouver les paiements

### 3. Webhook Wave
- **Ajout** de la gestion du `client_reference` pour retrouver les paiements
- **Correction** des champs de statut selon l'API Wave officielle
- **Amélioration** du logging pour debug
- **Exclusion** du webhook de la vérification CSRF

### 4. Configuration
- **Ajout** de l'exclusion CSRF pour `/webhook/wave/payment`

## Format API Wave corrigé

### Requête (avant)
```json
{
  "amount": "1000",
  "currency": "XOF", 
  "success_url": "...",
  "error_url": "...",
  "metadata": {  // ❌ CHAMP NON AUTORISÉ
    "booking_id": 123,
    "payment_id": 456
  }
}
```

### Requête (après)
```json
{
  "amount": "1000",
  "currency": "XOF",
  "success_url": "...", 
  "error_url": "...",
  "client_reference": "payment_456"  // ✅ CHAMP AUTORISÉ
}
```

### Réponse Wave
```json
{
  "id": "cos-18qq25rgr100a",
  "checkout_status": "complete",    // ✅ Champ correct
  "payment_status": "succeeded",    // ✅ Champ correct  
  "client_reference": "payment_456",
  "transaction_id": "TDH5TEWTLFE",
  "wave_launch_url": "https://pay.wave.com/c/cos-18qq25rgr100a"
}
```

## Tests
- ✅ Payload sans metadata validé
- ✅ Gestion des statuts Wave corrigée  
- ✅ Webhook fonctionnel avec client_reference
- ✅ Exclusion CSRF configurée

Date: 1 décembre 2025