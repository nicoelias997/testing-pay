<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Services\ExchangeConversionService;

class PayUService
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
        $this->baseUri  = config('services.payu.base_uri');
        $this->publicKey   = config('services.payu.public_key');
        $this->secretKey = config('services.payu.secret_key');
        $this->clientId = config('services.payu.client_id');
        $this->clientSecret   = config('services.payu.client_secret');
        $this->merchantTest = config('services.payu.merchant_test');
        $this->loginTest = config('services.payu.login_test');
        $this->baseCurrency = config('services.payu.account_test_ars');

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
            'Authorization' => "Bearer $this->accessToken"
        ])->get("{$this->baseUri}/SUBMIT_TRANSACTION");

        $response = Http::post("{$this->baseUri}/submit_transaction", [
        'language' => 'es',
        'command' => 'SUBMIT_TRANSACTION',
        'test' => true,
        'merchant' => [
            'apiKey' => $this->publicKey,
            'apiLogin' => $this->loginTest,
        ],
        'transaction' => [
            'order' => [
                'accountId' => $this->accountId,
                'referenceCode' => 'pedido-001',
                'description' => 'Descripción del pedido',
                'language' => 'es',
                'signature' => $this->generateSignature(
                    $this->publicKey,
                    'pedido-001',
                    $this->roundAmount($request->amount, $request->currency),
                    $this->baseCurrency
                ),
                'additionalValues' => [
                    'TX_VALUE' => [
                        'value' => $this->roundAmount($request->amount, $request->currency),
                        'currency' => $this->baseCurrency,
                    ]
                ],
                'buyer' => [
                    'fullName' => $request->buyer['fullName'],
                    'emailAddress' => $request->buyer['email'],
                    'contactPhone' => $request->buyer['phone'],
                    'dniNumber' => $request->buyer['dni'],
                    'shippingAddress' => [
                        'street1' => $request->buyer['address']['street1'],
                        'city' => $request->buyer['address']['city'],
                        'state' => $request->buyer['address']['state'],
                        'country' => $request->buyer['address']['country'], // ISO 3166-1 alpha-2
                        'postalCode' => $request->buyer['address']['postalCode'],
                        'phone' => $request->buyer['phone'],
                    ]
                ]
            ],
            'payer' => [
                'fullName' => $request->payer['fullName'],
                'emailAddress' => $request->payer['email'],
                'contactPhone' => $request->payer['phone'],
                'dniNumber' => $request->payer['dni'],
                'dniType' => $request->payer['dniType'],
                'billingAddress' => [
                    'street1' => $request->payer['address']['street1'],
                    'state' => $request->payer['address']['state'],
                ],
            ],
            'type' => 'AUTHORIZATION_AND_CAPTURE',
            'paymentMethod' => $request->paymentMethod,
            'paymentCountry' => 'AR',
            'deviceSessionId' => $request->deviceSessionId,
            'ipAddress' => $request->ipAddress,
            'cookie' => $request->cookie,
            'userAgent' => $request->userAgent,
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
