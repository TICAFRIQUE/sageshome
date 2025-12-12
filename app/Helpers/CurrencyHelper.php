<?php

namespace App\Helpers;

class CurrencyHelper
{
    // Taux de conversion fixe CFA (1 EUR = 655.957 FCFA)
    const FCFA_TO_EUR_RATE = 655.957;

    /**
     * Convertir FCFA en EUR
     */
    public static function fcfaToEur($amountFcfa)
    {
        return round($amountFcfa / self::FCFA_TO_EUR_RATE, 2);
    }

    /**
     * Convertir EUR en FCFA
     */
    public static function eurToFcfa($amountEur)
    {
        return round($amountEur * self::FCFA_TO_EUR_RATE, 0);
    }

    /**
     * Formater un montant FCFA
     */
    public static function formatFcfa($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Formater un montant EUR
     */
    public static function formatEur($amount)
    {
        return number_format($amount, 2, ',', ' ') . ' €';
    }

    /**
     * Obtenir le prix formaté en FCFA et EUR
     */
    public static function formatBothCurrencies($amountFcfa)
    {
        $fcfa = self::formatFcfa($amountFcfa);
        $eur = self::formatEur(self::fcfaToEur($amountFcfa));
        
        return [
            'fcfa' => $fcfa,
            'eur' => $eur,
            'fcfa_amount' => $amountFcfa,
            'eur_amount' => self::fcfaToEur($amountFcfa)
        ];
    }
}
