<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
        
        <!-- Styles -->

        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <style>
            #tidio-chat {
                display: none;
            }
        </style>
        <!-- Scripts -->
        @routes
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script src="https:////code.tidio.co/jp69yo91ama4mvtde3dod6pimgo55hqp.js" defer></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body class="font-proximanova antialiased min-h-90vh">
        
        @inertia
        @if(env('APP_ENV') !== 'production')
            <div class="fixed bottom-0 w-full bg-yellow-500 text-white px-8 py-2 mx-auto rounded-sm text-center font-semibold">
                    Test mode enabled 
            </div>
        @endif
        @env ('local')
            <script src="http://localhost:8080/js/bundle.js"></script>
        @endenv
    </body>
</html>
