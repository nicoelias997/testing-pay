<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

use App\Traits\ApiResponses;

class PayPalService
{
    protected string $baseUri;
    protected string $clientId;
    protected string $clientSecret;
    protected string $mode;

    use ApiResponses;
    // C:\Users\nicoe\Desktop\Proyectos\testing-pay
    // cd C:\Users\Dev\Desktop\Nico\Proyectos\testing-pay
    // php artisan tinker
    //$paypal = new App\Services\PayPalService;
    //$paypal->makeRequest('GET', '/v1/invoicing/invoices')
    public function __construct()
    {
        $this->baseUri  = config('services.paypal.base_uri');
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret   = config('services.paypal.client_secret');
        $this->mode     = config('services.paypal.mode');
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers){
        $headers['Authorization'] = "Bearer " . $this->getAccessToken();
    }

    public function getAccessToken(): string|null
    {
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post("{$this->baseUri}/v1/oauth2/token", [
                'grant_type' => 'client_credentials'
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        return null;
    }

    public function createOrder($amount, $currency): array|null
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) return null;

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUri}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => strtoupper($currency),
                        'value' => $amount
                    ]
                ]],
                'payment_source' => [
                    "paypal" => [
                        "experience_context" => [
                            'brand_name' => config('app.name'),
                            'shipping_preference' => 'NO_SHIPPING',
                            'user_action' => 'PAY_NOW',
                            'return_url' => route('ui.payment.create'),
                            'cancel_url' => route('ui.payment.index')
                        ]
                    ]
                ]
            ]);

        return $response->successful() ? $response->json() : null;
    }

    public function captureOrder($orderId): array|null
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) return null;

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUri}/v2/checkout/orders/{$orderId}/capture");

        return $response->successful() ? $response->json() : null;
    }
}
