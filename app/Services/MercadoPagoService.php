<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Services\ExchangeConversionService;

class MercadoPagoService
{
    protected string $baseUri;
    protected string $publicKey;
    protected string $accessToken;
    protected string $clientId;
    protected string $clientSecret;
    protected string $baseCurrency;
    protected $converter;

    public function __construct(ExchangeConversionService $converter)
    {
        $this->baseUri  = config('services.mercado_pago.base_uri');
        $this->publicKey   = config('services.mercado_pago.public_key');
        $this->accessToken = config('services.mercado_pago.access_token');
        $this->clientId = config('services.mercado_pago.client_id');
        $this->clientSecret   = config('services.mercado_pago.client_secret');
        $this->baseCurrency = config('services.mercado_pago.base_currency');

        $this->converter = $converter;
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
        $order = $this->createPayment($request);
        return redirect()
            ->action([PaymentController::class, 'index']);
    }

    public function createPayment($request)
    {   
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) return null;
        $responseTest = Http::withHeaders([
            'X-Idempotency-Key' => Str::uuid()->toString(),
            'Authorization' => "Bearer $this->accessToken"
        ])->get("{$this->baseUri}/v1/payments/search");

         $response = Http::withHeaders([
        'X-Idempotency-Key' => Str::uuid()->toString(),
        'Authorization' => "Bearer $this->accessToken"
    ])->post("{$this->baseUri}/v1/orders", [
        'type' => 'online',
        'processing_mode' => 'automatic',
        'total_amount' => (string) $this->roundAmount($request->amount, $request->currency), // debe ser string
        'external_reference' => Str::uuid()->toString(),
        'transactions' => [
            'payments' => [ // este es un array de objetos
                [
                    'amount' => (string) $this->roundAmount($request->amount, $request->currency), // también string
                    'payment_method' => [
                        'id' => $request->payment_method_id,
                        'type' => 'credit_card',
                        'token' => $request->token,
                        'installments' => 1,
                    ]
                ]
            ]
        ],
        'payer' => [
            'first_name' => 'APRO',
            'email' => $request->payer['email'],
            'identification' => [
                'type' => $request->payer['identification']['type'],
                'number' => $request->payer['identification']['number']
            ]
        ]
    ]);
    return $response->successful() ? $response->json() : $response;
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

       public function resolveFactor($currency, $baseCurrency, $amount) {
        $conversion = $this->converter->getConvertCurrency($baseCurrency, $currency, $amount);
        if($conversion['result'] === 'success'){
           return $conversion['conversion_rate'];
        } else {
            return 1;
        }
    }

    public function roundAmount($amount, $currency){
        $factor = $this->resolveFactor($currency, $this->baseCurrency, $amount);
        return round($amount / $factor);
    }
}
