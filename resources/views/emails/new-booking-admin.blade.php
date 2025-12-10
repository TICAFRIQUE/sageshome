<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle réservation</title>
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
            background: linear-gradient(135deg, #2F4A33, #1a7e20);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #856404;
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
            margin: 10px 5px;
        }
        .btn-danger {
            background: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔔 Nouvelle Réservation</h1>
        <p>Sages Home - Administration</p>
    </div>
    
    <div class="content">
        <div class="alert">
            <strong>⚠️ Action requise !</strong> Une nouvelle réservation vient d'être effectuée et nécessite votre attention.
        </div>
        
        <h3 style="color: #2F4A33;">Informations du client</h3>
        <div class="booking-details">
            <div class="detail-row">
                <span class="label">Nom complet :</span>
                <span class="value">{{ $booking->first_name }} {{ $booking->last_name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Email :</span>
                <span class="value">{{ $booking->email }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Téléphone :</span>
                <span class="value">{{ $booking->phone }}</span>
            </div>
            
            @if($booking->country)
            <div class="detail-row">
                <span class="label">Pays :</span>
                <span class="value">{{ $booking->country }}</span>
            </div>
            @endif
        </div>
        
        <h3 style="color: #2F4A33;">Détails de la réservation</h3>
        <div class="booking-details">
            <div class="detail-row">
                <span class="label">Référence :</span>
                <span class="value"><strong>{{ $booking->booking_number ?? $booking->id }}</strong></span>
            </div>
            
            <div class="detail-row">
                <span class="label">Résidence :</span>
                <span class="value">{{ $booking->residence->name }}</span>
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
                <span class="label">Durée :</span>
                <span class="value">{{ $booking->nights }} nuit(s)</span>
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
                <span class="label">Statut du paiement :</span>
                <span class="value">
                    @if($booking->payment)
                        {{ ucfirst($booking->payment->status) }} - {{ ucfirst($booking->payment->method) }}
                    @else
                        En attente
                    @endif
                </span>
            </div>
            
            <div class="detail-row">
                <span class="label">Date de réservation :</span>
                <span class="value">{{ $booking->created_at->format('d/m/Y à H:i') }}</span>
            </div>
            
            @if($booking->special_requests)
            <div class="detail-row">
                <span class="label">Demandes spéciales :</span>
                <span class="value">{{ $booking->special_requests }}</span>
            </div>
            @endif
        </div>
        
        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn">Voir la réservation</a>
            <a href="{{ route('admin.bookings.index') }}" class="btn">Toutes les réservations</a>
        </div>
        
        <p style="margin-top: 30px;"><strong>Actions à effectuer :</strong></p>
        <ol>
            <li>Vérifier la disponibilité de la résidence</li>
            <li>Confirmer la réservation</li>
            <li>Contacter le client si nécessaire</li>
            <li>Préparer la résidence pour l'arrivée du client</li>
        </ol>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Sages Home - Système de gestion des réservations</p>
        <p>Cet email a été envoyé automatiquement depuis le système de réservation.</p>
    </div>
</body>
</html>
