<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('fcfa_to_eur')) {
    function fcfa_to_eur($amountFcfa)
    {
        return CurrencyHelper::fcfaToEur($amountFcfa);
    }
}

if (!function_exists('eur_to_fcfa')) {
    function eur_to_fcfa($amountEur)
    {
        return CurrencyHelper::eurToFcfa($amountEur);
    }
}

if (!function_exists('format_price')) {
    function format_price($amountFcfa, $showBoth = true)
    {
        if ($showBoth) {
            $prices = CurrencyHelper::formatBothCurrencies($amountFcfa);
            return $prices['fcfa'] . ' <span class="text-muted">(' . $prices['eur'] . ')</span>';
        }
        return CurrencyHelper::formatFcfa($amountFcfa);
    }
}

if (!function_exists('format_fcfa')) {
    function format_fcfa($amount)
    {
        return CurrencyHelper::formatFcfa($amount);
    }
}

if (!function_exists('format_eur')) {
    function format_eur($amount)
    {
        return CurrencyHelper::formatEur($amount);
    }
}
