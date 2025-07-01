<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
// use Stripe;
class StripeService
{
    protected string $baseUri;
    protected string $clientId;
    protected string $clientSecret;
    protected object $stripe;

    public function __construct()
    {
        $this->baseUri  = config('services.stripe.base_uri');
        $this->clientId = config('services.stripe.client_id');
        $this->clientSecret   = config('services.stripe.client_secret');
        $this->stripe = new \Stripe\StripeClient($this->clientSecret);
    }

    public function getAccessToken()
    {
        $ch = $this->stripe;
        // $response = Http::asForm()
        //     ->withBasicAuth($this->clientId, $this->clientSecret)
        //     ->post("{$this->baseUri}/v1/charges", [
        //         // 'grant_type' => 'client_credentials'
        //     ]);
        $ch = $stripe->charges->retrieve(
            'ch_3Ln3fO2eZvKYlo2C1kqP3AMr',
            [],
            ['api_key' => "$this->clientSecret"]
            );
        $ch->capture(); // Uses the same API Key.

        return $ch;
        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        return null;
    }

    //TODO: Fijarme en el laravel passport
    public function handlePayment(Request $request){
        $order = $this->createOrder($request->amount, $request->currency);
    }

    //Una vez creada, nos solicita un "PAYER_ACTION"
    public function createOrder($amount, $currency)
    {
        //  $accessToken = $this->getAccessToken();

        // if (!$accessToken) return null;

        //TODO: type can be: card, card_present. customer_balance, klarna, link, paypal, 
        $paymentMethod = $this->stripe->paymentIntents->create([
            'amount' => 100,
            'currency' => 'usd',
            'payment_method' => 'pm_card_visa',
            'payment_method_types' => ['card'],
            // 'type' => 'card',
            // 'card' => [
            //     'number' => '424242424242',
            //     'exp_month' => '10',
            //     'exp_year' => '32',
            //     'cvc' => 341
            // ]
        ]);
        return $paymentMethod;
    }

    public function handleCaptureOrder($orderId){

    }
    public function captureOrder($orderId)
    {

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
