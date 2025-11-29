@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Primary Meta Tags -->
        <title>{{ $title ?? 'Login' }} | ProspectaFreela - Prospecção Automática B2B e B2C</title>
        <meta name="title" content="{{ $title ?? 'Login' }} | ProspectaFreela - Prospecção Automática B2B e B2C">
        <meta name="description" content="Plataforma de prospecção automática com IA para Designers, Programadores e Gestores de Tráfego. Encontre clientes ideais e aumente suas vendas com inteligência artificial.">
        <meta name="keywords" content="prospecção automática, B2B, B2C, inteligência artificial, leads qualificados, vendas, designers, programadores, gestores de tráfego">
        <meta name="author" content="ProspectaFreela">
        <meta name="robots" content="index, follow">
        <meta name="language" content="Portuguese">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $title ?? 'Login' }} | ProspectaFreela - Prospecção Automática B2B e B2C">
        <meta property="og:description" content="Plataforma de prospecção automática com IA para Designers, Programadores e Gestores de Tráfego. Encontre clientes ideais e aumente suas vendas.">
        <meta property="og:image" content="{{ asset('favicon.svg') }}">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="{{ $title ?? 'Login' }} | ProspectaFreela - Prospecção Automática B2B e B2C">
        <meta property="twitter:description" content="Plataforma de prospecção automática com IA para Designers, Programadores e Gestores de Tráfego. Encontre clientes ideais e aumente suas vendas.">
        <meta property="twitter:image" content="{{ asset('favicon.svg') }}">
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            html, body {
                overflow: hidden;
                height: 100%;
                width: 100%;
            }
            /* Hide scrollbar for Chrome, Safari and Opera */
            ::-webkit-scrollbar {
                display: none;
            }
            /* Hide scrollbar for IE, Edge and Firefox */
            * {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-white overflow-hidden">
        <div class="min-h-screen h-screen flex flex-col lg:flex-row overflow-hidden">
            {{ $slot }}
        </div>
    </body>
</html>

