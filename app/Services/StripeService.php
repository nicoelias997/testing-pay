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
        $handle = $this->createPaymentIntent($request->amount, $request->currency);
        return redirect()
                    ->action([PaymentController::class, 'index']);
    }

    // public function createPaymentMethod()
    // {
    //     //TODO: type can be: card, card_present. customer_balance, klarna, link, paypal, 
    //     $paymentMethod = $this->stripe->paymentMethods->create([
    //         'type' => 'card',
    //         'card' => [
    //             'number' => 424242424242,
    //             'exp_month' => 10,
    //             'exp_year' => 34,
    //             'cvc' => 567
    //         ]
    //     ]);
    //     return $paymentMethod ? $paymentMethod : null;
    // }

    //Una vez creada, nos solicita un "PAYER_ACTION"
    public function createPaymentIntent($amount, $currency)
    {
        //TODO: type can be: card, card_present. customer_balance, klarna, link, paypal, 
        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => $this->roundAmount($amount, $currency),
            'currency' => strtoupper($currency),
            'payment_method' => 'pm_card_visa',
            'payment_method_types' => ['card'],
        ]);
        if($paymentIntent['status'] == 'requires_confirmation'){
            $confirmation = $this->confirmPaymentIntent($paymentIntent['id'], $paymentIntent['payment_method']);
            if($confirmation['status'] === 'succeeded'){
                 return true;
            }
        }
    }

    public function confirmPaymentIntent($paymentIntentId, $paymentIntentMethod){
            $confirmPayment = $this->stripe->paymentIntents->confirm($paymentIntentId, [
                'payment_method' => $paymentIntentMethod
            ]);
            return $confirmPayment ? $confirmPayment : null;
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
