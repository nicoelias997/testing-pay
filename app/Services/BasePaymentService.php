<?php

namespace App\Services;

use Illuminate\Http\Request;

abstract class BasePaymentService
{
    /**
     * Handle the payment process
     *
     * @param Request $request
     * @return mixed
     */
    abstract public function handlePayment(Request $request);

    /**
     * Resolve the factor for currency conversion
     * Zero-decimal currencies (like JPY, KRW) don't need multiplication
     *
     * @param string $currency
     * @return int
     */
    protected function resolveFactor(string $currency): int
    {
        $zeroDecimalCurrencies = ['JPY', 'KRW', 'VND', 'CLP', 'ISK'];

        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return 1;
        }

        return 100;
    }

    /**
     * Round amount based on currency factor
     *
     * @param float $amount
     * @param string $currency
     * @return float
     */
    protected function roundAmount(float $amount, string $currency): float
    {
        $factor = $this->resolveFactor($currency);
        return round($amount * $factor) / $factor;
    }
}
