<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe modifié</title>
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
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .success-box {
            background: #d4edda;
            border: 2px solid #28a745;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
        }
        .success-box .icon {
            font-size: 48px;
            color: #28a745;
            margin-bottom: 10px;
        }
        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #2F4A33;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.9em;
        }
        .security-tips {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Mot de passe modifié avec succès</h1>
        <p>Sages Home</p>
    </div>

    <div class="content">
        <div class="success-box">
            <div class="icon">🎉</div>
            <h2 style="margin: 10px 0; color: #28a745;">Confirmation de modification</h2>
            <p style="margin: 5px 0;">Votre mot de passe a été réinitialisé avec succès.</p>
        </div>

        <p>Bonjour <strong>{{ $user->username }}</strong>,</p>

        <p>Nous vous confirmons que votre mot de passe Sages Home a bien été modifié le <strong>{{ now()->format('d/m/Y à H:i') }}</strong>.</p>

        <div class="info-box">
            <strong>📧 Informations de votre compte :</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li><strong>Email :</strong> {{ $user->email }}</li>
                <li><strong>Date de modification :</strong> {{ now()->format('d/m/Y à H:i') }}</li>
            </ul>
        </div>

        <div class="alert-box">
            <strong>⚠️ Vous n'avez pas effectué cette modification ?</strong>
            <p style="margin: 10px 0 0 0;">Si vous n'êtes pas à l'origine de cette modification, contactez-nous immédiatement pour sécuriser votre compte.</p>
        </div>

        <p>Vous pouvez maintenant vous connecter à votre compte avec votre nouveau mot de passe :</p>

        <div style="text-align: center;">
            <a href="{{ route('login') }}" class="btn">Me connecter</a>
        </div>

        <div class="security-tips">
            <h3 style="color: #2F4A33; margin-top: 0;">🛡️ Conseils de sécurité</h3>
            <ul style="padding-left: 20px; color: #666;">
                <li>Ne partagez jamais votre mot de passe avec qui que ce soit</li>
                <li>Utilisez un mot de passe unique pour chaque site web</li>
                <li>Changez régulièrement votre mot de passe</li>
                <li>Activez l'authentification à deux facteurs si disponible</li>
                <li>Déconnectez-vous toujours après avoir utilisé un appareil partagé</li>
            </ul>
        </div>

        <p>Si vous avez des questions ou besoin d'aide, notre équipe est à votre disposition :</p>
        <ul>
            <li><strong>Email :</strong> infos@sageshome.ci</li>
            <li><strong>Téléphone :</strong> +225 XX XX XX XX XX</li>
        </ul>

        <p style="margin-top: 30px;">Merci de votre confiance !</p>
        <p><strong>L'équipe Sages Home</strong></p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Sages Home. Tous droits réservés.</p>
        <p>Cet email de confirmation a été envoyé automatiquement.</p>
        <p style="margin-top: 10px; font-size: 0.85em;">
            Si vous avez reçu cet email par erreur, veuillez l'ignorer ou nous contacter.
        </p>
    </div>
</body>
</html>
