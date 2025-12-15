<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Services\ExchangeConversionService;

class MercadoPagoService extends BasePaymentService
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

    public function handlePayment(Request $request){
        $order = $this->createPayment($request);
        return redirect()
            ->action([PaymentController::class, 'index']);
    }

    public function createPayment($request)
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            throw new \Exception('Failed to get MercadoPago access token');
        }

        $response = Http::withHeaders([
            'X-Idempotency-Key' => Str::uuid()->toString(),
            'Authorization' => "Bearer $this->accessToken"
        ])->post("{$this->baseUri}/v1/orders", [
            'type' => 'online',
            'processing_mode' => 'automatic',
            'total_amount' => (string) $this->roundAmount($request->amount, $request->currency),
            'external_reference' => Str::uuid()->toString(),
            'transactions' => [
                'payments' => [
                    [
                        'amount' => (string) $this->roundAmount($request->amount, $request->currency),
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

        if (!$response->successful()) {
            $error = $response->json()['message'] ?? 'Payment creation failed';
            throw new \Exception("MercadoPago error: {$error}");
        }

        return $response->json();
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

        if (!$accessToken) {
            throw new \Exception('Failed to get MercadoPago access token');
        }

        $response = Http::withToken($accessToken)
            ->withBody('', 'application/json')
            ->post("{$this->baseUri}/v2/checkout/orders/$orderId/capture");

        if (!$response->successful()) {
            $error = $response->json()['message'] ?? 'Payment capture failed';
            throw new \Exception("MercadoPago capture error: {$error}");
        }

        return $response->json();
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
