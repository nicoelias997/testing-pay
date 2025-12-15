<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Favicons -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('icon.svg') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icon.svg') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icon.svg') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icon.svg') }}">
        <link rel="manifest" href="{{ asset('manifest.json') }}">

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#1976d2">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="Payment Platform">

        <title>Payment Platform</title>
        <meta name="description" content="Modern payment processing platform with support for multiple payment methods including Stripe, PayPal, MercadoPago and PayU">
        <meta name="keywords" content="payment platform, laravel, inertia, vue.js, stripe, paypal, mercadopago, payu, payment gateway">

        <!-- Social Media Meta Tags -->
        <meta property="og:title" content="Payment Platform - Nicolas Elias">
        <meta property="og:description" content="Modern payment processing platform with support for multiple payment methods">
        <meta property="og:image" content="{{ asset('icon.svg') }}">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="Payment Platform">
        <meta name="twitter:description" content="Modern payment processing platform">

        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
