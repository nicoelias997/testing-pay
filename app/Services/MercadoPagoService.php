<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
        $response = Http::asForm()
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->post("{$this->baseUri}/oauth/token", [
                'grant_type' => 'client_credentials'
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        return null;
    }

    //TODO: Fijarme en el laravel passport
    public function handlePayment(Request $request){
        $order = $this->createOrder($request->amount, $request->currency);
        
        $orderLinks = collect($order['links']);

        $payerAction = $orderLinks->where('rel', 'payer-action')->first();
        
        return Inertia::location($payerAction['href']);
    }

    //Una vez creada, nos solicita un "PAYER_ACTION"
    public function createOrder($amount, $currency): array|null
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) return null;

        dd(HasUuids::newUniqueId());
        $response = Http::withToken($accessToken)
            ->withHeader('X-Idempotency-Key', HasUuids::newUniqueId())
            ->post("{$this->baseUri}/v1/orders", [
                'type' => 'online',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => strtoupper($currency),
                        'value' => $this->roundAmount($amount, $currency)
                    ]
                ]],
                'payment_source' => [
                    "mercado_pago" => [
                        "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                        "experience_context" => [
                            'brand_name' => config('app.name'),
                            'shipping_preference' => 'NO_SHIPPING',
                            'user_action' => 'PAY_NOW',
                            'return_url' => route('ui.payment.approved'),
                            'cancel_url' => route('ui.payment.cancelled')
                        ]
                    ]
                ]
            ]);

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
