<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de réservation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #F2D18A, #C29B32);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .booking-details {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .total {
            font-size: 1.2em;
            font-weight: bold;
            color: #2F4A33;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.9em;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #2F4A33;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Confirmation de Réservation</h1>
        <p>Merci d'avoir choisi Sages Home</p>
    </div>
    
    <div class="content">
        <p>Bonjour <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong>,</p>
        
        <p>Nous avons bien reçu votre réservation. Voici les détails :</p>
        
        <div class="booking-details">
            <h3 style="color: #2F4A33; margin-top: 0;">Informations de la réservation</h3>
            
            <div class="detail-row">
                <span class="label">Numéro de réservation :</span>
                <span class="value">{{ $booking->booking_number ?? $booking->id }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Résidence :</span>
                <span class="value">{{ $booking->residence->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Adresse :</span>
                <span class="value">{{ $booking->residence->location }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Date d'arrivée :</span>
                <span class="value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Date de départ :</span>
                <span class="value">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Nombre de nuits :</span>
                <span class="value">{{ $booking->nights }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Nombre de personnes :</span>
                <span class="value">{{ $booking->guests }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Montant total :</span>
                <span class="value total">{{ number_format($booking->total_amount, 0, ',', ' ') }} FCFA</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Statut :</span>
                <span class="value">
                    @if($booking->status === 'pending')
                        <span style="color: #ff9800;">En attente de confirmation</span>
                    @elseif($booking->status === 'confirmed')
                        <span style="color: #4caf50;">Confirmée</span>
                    @else
                        {{ ucfirst($booking->status) }}
                    @endif
                </span>
            </div>
        </div>
        
        <p>Notre équipe va traiter votre demande et vous contactera prochainement pour confirmer votre réservation.</p>
        
        <div style="text-align: center;">
            <a href="{{ route('dashboard.booking.show', $booking->id) }}" class="btn">Voir ma réservation</a>
        </div>
        
        <p><strong>Informations importantes :</strong></p>
        <ul>
            <li>Veuillez arriver après 14h00 le jour de votre arrivée</li>
            <li>Le départ doit se faire avant 11h00 le jour de votre départ</li>
            <li>Une pièce d'identité valide sera demandée à l'arrivée</li>
        </ul>
        
        <p>Si vous avez des questions, n'hésitez pas à nous contacter :</p>
        <ul>
            <li>Email : contact@sageshome.com</li>
            <li>Téléphone : +225 XX XX XX XX XX</li>
        </ul>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Sages Home. Tous droits réservés.</p>
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.</p>
    </div>
</body>
</html>
