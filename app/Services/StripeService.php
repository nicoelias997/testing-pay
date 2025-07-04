<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
class StripeService
{
    protected string $baseUri;
    protected string $publicKey;
    protected string $secret_key;
    protected object $stripe;

    public function __construct()
    {
        $this->baseUri  = config('services.stripe.base_uri');
        $this->publicKey = config('services.stripe.public_key');
        $this->secret_key   = config('services.stripe.secret_key');
        $this->stripe = new \Stripe\StripeClient($this->secret_key);
    }

    public function handlePayment(Request $request){
        $handle = $this->createPaymentIntent($request->amount, $request->currency);
        return $handle;
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

    public function createPaymentIntent($amount, $currency)
    {
        //TODO: type can be: card, card_present. customer_balance, klarna, link, paypal, 
        $paymentIntent = $this->stripe->paymentIntents->create([
            'amount' => round($amount * $this->resolveFactor($currency)),
            'currency' => strtoupper($currency),
            'payment_method' => 'pm_card_authenticationRequiredOnSetup',
            'payment_method_types' => ['card'],
        ]);
        return Inertia::render('Ui/Payment/Stripe/3d-secure', [
                    'clientSecret' =>  $paymentIntent['client_secret']
                ]);
        // if($paymentIntent['status'] == 'requires_confirmation'){
        //     $confirmation = $this->confirmPaymentIntent($paymentIntent['id'], $paymentIntent['payment_method']);
        //     if($confirmation['status'] === 'requires_action'){
        //          $clientSecret = $confirmation['client_secret'];

        //         return Inertia::render('Ui/Payment/Stripe/3d-secure', [
        //             'clientSecret' => $clientSecret
        //         ]);
        //     }
        // }
    }

    // public function confirmPaymentIntent($paymentIntentId, $paymentIntentMethod){
    //         $confirmPayment = $this->stripe->paymentIntents->confirm($paymentIntentId, [
    //             'payment_method' => $paymentIntentMethod
    //         ]);
    //         return $confirmPayment ? $confirmPayment : null;
    // }

    public function resolveFactor($currency) {
        $zeroDecimalCurrencies = ['JPY'];

        if(in_array(strtoupper($currency), $zeroDecimalCurrencies)){
            return 1;
        }

        return 100;
    }
}
