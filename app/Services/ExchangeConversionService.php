<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class ExchangeConversionService
{
    protected string $baseUri;

    public function __construct()
    {
        $this->baseUri  = config('services.currency_conversion.base_uri');
    }

    public function getLatestConversion($currency): array|null
    {
        $response = Http::get("{$this->baseUri}/latest/$currency");

        return $response->successful() ? $response->json() : null;
    }

    public function getConvertCurrency($baseCurrency, $targetCurrency, $amount)
    {
        $response = Http::get("{$this->baseUri}/pair/$baseCurrency/$targetCurrency/$amount");

        return $response->successful() ? $response->json() : null;
    }
}
