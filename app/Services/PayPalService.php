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
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response) {
        return json_decode($response);
    }

    public function resolveAccessToken(){
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        return "Basic {$credentials}"; 
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

    public function createOrder($amount = 10, $currency = 'USD'): array|null
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) return null;

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => $amount
                    ]
                ]]
            ]);

        return $response->successful() ? $response->json() : null;
    }

    public function captureOrder($orderId): array|null
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) return null;

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");

        return $response->successful() ? $response->json() : null;
    }
}
