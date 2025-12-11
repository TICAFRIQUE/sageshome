<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe</title>
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
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            color: #856404;
        }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: #2F4A33;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 0.9em;
        }
        .security-info {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin: 20px 0;
        }
        .expiry-warning {
            background: #fff;
            border: 2px solid #ff9800;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .expiry-warning strong {
            color: #ff9800;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔐 Réinitialisation de mot de passe</h1>
        <p>Sages Home</p>
    </div>

    <div class="content">
        <p>Bonjour <strong>{{ $user->username }}</strong>,</p>

        <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte Sages Home.</p>

        <div class="info-box">
            <strong>⚠️ Si vous n'avez pas demandé cette réinitialisation</strong>, ignorez cet email. Votre mot de passe actuel restera inchangé.
        </div>

        <p>Pour réinitialiser votre mot de passe, cliquez sur le bouton ci-dessous :</p>

        <div style="text-align: center;">
            <a href="{{ $resetLink }}" class="btn">Réinitialiser mon mot de passe</a>
        </div>

        <div class="expiry-warning">
            <strong>⏰ Ce lien expire dans 60 minutes</strong>
            <p style="margin: 5px 0 0 0; font-size: 0.9em;">Pour des raisons de sécurité, ce lien ne sera valide que pendant 1 heure.</p>
        </div>

        <div class="security-info">
            <strong>🛡️ Conseils de sécurité :</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>Choisissez un mot de passe fort d'au moins 8 caractères</li>
                <li>Mélangez des lettres majuscules, minuscules et des chiffres</li>
                <li>N'utilisez pas le même mot de passe pour plusieurs comptes</li>
                <li>Ne partagez jamais votre mot de passe avec qui que ce soit</li>
            </ul>
        </div>

        <p><strong>Si le bouton ne fonctionne pas</strong>, copiez et collez ce lien dans votre navigateur :</p>
        <p style="word-break: break-all; background: #f5f5f5; padding: 10px; border-radius: 5px; font-size: 0.85em;">
            {{ $resetLink }}
        </p>

        <p style="margin-top: 30px;">Si vous avez des questions, n'hésitez pas à nous contacter :</p>
        <ul>
            <li>Email : infos@sageshome.ci</li>
            <li>Téléphone : +225 XX XX XX XX XX</li>
        </ul>

        <p style="margin-top: 30px; color: #666; font-size: 0.9em;">
            <strong>Note :</strong> Ce lien de réinitialisation ne peut être utilisé qu'une seule fois. Si vous avez besoin d'un nouveau lien, vous devrez refaire une demande de réinitialisation.
        </p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Sages Home. Tous droits réservés.</p>
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.</p>
    </div>
</body>
</html>
