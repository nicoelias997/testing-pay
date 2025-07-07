<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Str;

class MercadoPagoService
{
    protected string $baseUri;
    protected string $publicKey;
    protected string $accessToken;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUri  = config('services.mercado_pago.base_uri');
        $this->publicKey   = config('services.mercado_pago.public_key');
        $this->accessToken = config('services.mercado_pago.access_token');
        $this->clientId = config('services.mercado_pago.client_id');
        $this->clientSecret   = config('services.mercado_pago.client_secret');
    }

    public function getAccessToken(): string|null
    {
        $response = Http::post("{$this->baseUri}/oauth/token", [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials'
            ]);
        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        return null;
    }

    //TODO: Fijarme en el laravel passport
    public function handlePayment(Request $request){
        $order = $this->createPayment($request->amount, $request->currency);
    }

    //Una vez creada, nos solicita un "PAYER_ACTION"
    public function createPayment($amount, $currency): array|null
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) return null;

        $response = Http::withHeaders([
            'X-Idempotency-Key' => Str::uuid7()->toString(),
            'Authorization' => "Bearer $accessToken"
        ])
        
            // withToken($accessToken)
            // ->withHeader('X-Idempotency-Key', Str::uuid7()->toString())

            ->post("{$this->baseUri}/v1/orders", [
                'type' => 'online',
                'external_reference' => 'dcxfd3',
                'transactions' => [
                    'payments' => [
                        'amount' => $this->roundAmount($amount, $currency),
                        'payment_method' => [
                            "id" => "visa",
                            "type" => "credit_card",
                            "token" => "12345",
                            "installments" => 1,
                        ],
                    ],
                    "payer" => [
                        "email" => 'test@test.com',
                        "first_name" => 'Test',
                        'last_name' => 'User',
                        'identification' => [
                            'type' => 'DNI',
                            'number' => '40404404'
                        ]
                    ],
                ],
            ]);
            dd($response);
            return $response->successful() ? $response->json() : null;
    }

    public function handleCaptureOrder($orderId){
        if($orderId){
            $payment = $this->captureOrder($orderId);
            $name = $payment['payer']['name']['given_name'];
            return redirect()
                    ->route('ui.payment.index')
                    ->withSuccess(['payment' => "Thanks, {$name}. We received your payment."]);
        } 
       return redirect()
            ->action([PaymentController::class, 'index']);
    }
    public function captureOrder($orderId)
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) return null;

        $response = Http::withToken($accessToken)
            ->withBody('', 'application/json')
            ->post("{$this->baseUri}/v2/checkout/orders/$orderId/capture");
          
        return $response->successful() ? $response->json() : $response->json();
    }

       public function resolveFactor($currency) {
        $zeroDecimalCurrencies = ['JPY'];

        if(in_array(strtoupper($currency), $zeroDecimalCurrencies)){
            return 1;
        }

        return 100;
    }

    public function roundAmount($amount, $currency){
        $factor = $this->resolveFactor($currency);
        return round($amount * $factor) / $factor;
    }
}
