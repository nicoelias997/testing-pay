<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\PaymentPlatform;

class PaymentPlatformsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentPlatform::create([
            'name' => 'PayPal',
            'image' => '/img/payment-platforms/paypal.jpg'
        ]);
        PaymentPlatform::create([
            'name' => 'Stripe',
            'image' => '/img/payment-platforms/stripe.jpg'
        ]);
        PaymentPlatform::create([
            'name' => 'Mercado Pago',
            'image' => '/img/payment-platforms/mercadopago.jpg'
        ]);
         PaymentPlatform::create([
            'name' => 'PayU',
            'image' => '/img/payment-platforms/payu.jpg'
        ]);
    }
}
