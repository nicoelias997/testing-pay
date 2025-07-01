<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class PayPalService
{
    protected string $baseUri;
    protected string $clientId;
    protected string $clientSecret;
    protected string $mode;

    public function __construct()
    {
        $this->baseUri  = config('services.paypal.base_uri');
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret   = config('services.paypal.client_secret');
        $this->mode     = config('services.paypal.mode');
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

        $response = Http::withToken($accessToken)
            ->post("{$this->baseUri}/v2/checkout/orders", [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => strtoupper($currency),
                        'value' => $this->roundAmount($amount, $currency)
                    ]
                ]],
                'payment_source' => [
                    "paypal" => [
                        "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                        "experience_context" => [
                            'brand_name' => config('app.name'),
                            'shipping_preference' => 'NO_SHIPPING',
                            'user_action' => 'PAY_NOW',
                            'return_url' => route('ui.payment.create'),
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
