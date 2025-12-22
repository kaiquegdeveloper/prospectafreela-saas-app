<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val); })" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Primary Meta Tags -->
        <title>ProspectaFreela - Prospecção Automática B2B e B2C com IA</title>
        <meta name="title" content="ProspectaFreela - Prospecção Automática B2B e B2C com IA">
        <meta name="description" content="Plataforma de prospecção automática com inteligência artificial para Designers, Programadores e Gestores de Tráfego. Encontre clientes ideais e aumente suas vendas automaticamente.">
        <meta name="keywords" content="prospecção automática, B2B, B2C, inteligência artificial, leads qualificados, vendas, designers, programadores, gestores de tráfego, automação de vendas">
        <meta name="author" content="ProspectaFreela">
        <meta name="robots" content="index, follow">
        <meta name="language" content="Portuguese">
        
        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="ProspectaFreela - Prospecção Automática B2B e B2C com IA">
        <meta property="og:description" content="Plataforma de prospecção automática com inteligência artificial para Designers, Programadores e Gestores de Tráfego. Encontre clientes ideais e aumente suas vendas.">
        <meta property="og:image" content="{{ asset('favicon.svg') }}">
        
        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="ProspectaFreela - Prospecção Automática B2B e B2C com IA">
        <meta property="twitter:description" content="Plataforma de prospecção automática com inteligência artificial para Designers, Programadores e Gestores de Tráfego. Encontre clientes ideais e aumente suas vendas.">
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
    </head>
    <body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 transition-colors duration-300">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main class="relative">
                {{ $slot }}
            </main>
        </div>
        
        <!-- Plan Modal -->
        <x-plan-modal />
        
        <!-- Botão Flutuante Achar Clientes -->
        @auth
            @if(!request()->is('prospects/create'))
                <a href="{{ route('prospects.create') }}" 
                   class="fixed bottom-6 right-6 z-50 inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-indigo-600 to-blue-600 text-white rounded-full shadow-2xl hover:shadow-3xl hover:scale-110 transition-all duration-300 group"
                   title="Achar clientes">
                    <svg class="w-8 h-8 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="sr-only">Achar clientes</span>
                </a>
            @endif
        @endauth
        
        @stack('scripts')
    </body>
</html>
