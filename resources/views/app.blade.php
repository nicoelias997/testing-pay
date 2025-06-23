<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" href="{{ asset('images/icon.jfif') }}" type="image/x-icon"/>
        <title>Recetas de casa</title>
        <meta name="description" content="">
        <meta name="keywords" content="laravel,inertia,vue.js, pay method">
        <!-- Otros metadatos como og:title, og:description, og:image para compartir en redes sociales -->
        <meta property="og:title" content="Nicolas Elias">
        <meta property="og:description" content="">
        @vite('resources/js/app.js')
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
