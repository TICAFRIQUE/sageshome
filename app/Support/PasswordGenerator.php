<?php

namespace App\Support;

/**
 * Génère des mots de passe lisibles : lettres MAJUSCULES + chiffres.
 * Les caractères ambigus (O/0, I/1) sont exclus pour éviter les erreurs
 * quand le mot de passe est dicté ou recopié par le client.
 */
class PasswordGenerator
{
    public const LETTERS = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    public const DIGITS = '23456789';

    public static function generate(int $length = 8): string
    {
        $length = max(6, $length);

        // Au moins 2 lettres et 2 chiffres
        $chars = [
            self::pick(self::LETTERS),
            self::pick(self::LETTERS),
            self::pick(self::DIGITS),
            self::pick(self::DIGITS),
        ];
        $all = self::LETTERS . self::DIGITS;
        while (count($chars) < $length) {
            $chars[] = self::pick($all);
        }

        // Mélange cryptographiquement sûr (Fisher-Yates)
        for ($i = count($chars) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
        }

        return implode('', $chars);
    }

    private static function pick(string $alphabet): string
    {
        return $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
}
