<x-auth-layout title="Login">
    <!-- Left Column - Login Form (White) -->
    <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 md:px-8 lg:px-12 xl:px-16 2xl:px-20 bg-gradient-to-br from-white via-gray-50 to-white min-h-screen lg:min-h-0 lg:h-screen relative overflow-y-auto overflow-x-hidden" style="scrollbar-width: none; -ms-overflow-style: none;">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 opacity-[0.02]">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgb(0,0,0) 1px, transparent 0); background-size: 24px 24px;"></div>
        </div>
        
        <div class="relative z-10 w-full max-w-md mx-auto">
            <!-- Logo with enhanced animation -->
            <div class="mb-6 sm:mb-8 md:mb-10">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group group-hover:scale-105 transition-transform duration-300">
                    <div class="relative">
                        <div class="absolute inset-0 bg-neon-lime-200 rounded-xl blur-md opacity-60 group-hover:opacity-80 group-hover:blur-lg transition-all duration-300"></div>
                        <div class="relative bg-gradient-to-br from-neon-lime-200 via-neon-lime-200 to-neon-lime-300 rounded-xl p-2.5 shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="w-8 h-8 text-gray-900 transform group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <span class="text-xl sm:text-2xl font-extrabold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">
                        ProspectaFreela
                    </span>
                </a>
            </div>

            <!-- Title with better typography -->
            <div class="mb-6 sm:mb-8">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-3 sm:mb-4 leading-tight tracking-tight">
                    Área de Login
                </h1>
                <p class="text-gray-600 text-base sm:text-lg leading-relaxed">
                    Insira os dados abaixo para entrar na plataforma
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <!-- Login Form with enhanced UX -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-bold text-gray-900 tracking-wide">
                        E-mail
                    </label>
                    <div class="relative group">
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            placeholder="seu@email.com"
                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-neon-lime-300 focus:ring-4 focus:ring-neon-lime-200/30 transition-all duration-300 outline-none text-gray-900 placeholder-gray-400 bg-white shadow-sm hover:shadow-md focus:shadow-lg"
                        />
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-neon-lime-200/0 via-neon-lime-200/0 to-neon-lime-200/0 group-focus-within:from-neon-lime-200/5 group-focus-within:via-neon-lime-200/10 group-focus-within:to-neon-lime-200/5 transition-all duration-300 pointer-events-none"></div>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-bold text-gray-900 tracking-wide">
                        Senha
                    </label>
                    <div class="relative group">
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:border-neon-lime-300 focus:ring-4 focus:ring-neon-lime-200/30 transition-all duration-300 outline-none text-gray-900 placeholder-gray-400 bg-white shadow-sm hover:shadow-md focus:shadow-lg"
                        />
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-neon-lime-200/0 via-neon-lime-200/0 to-neon-lime-200/0 group-focus-within:from-neon-lime-200/5 group-focus-within:via-neon-lime-200/10 group-focus-within:to-neon-lime-200/5 transition-all duration-300 pointer-events-none"></div>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center group">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember"
                        class="w-5 h-5 text-neon-lime-300 border-gray-300 rounded focus:ring-2 focus:ring-neon-lime-200 focus:ring-offset-2 cursor-pointer transition-all duration-200"
                    />
                    <label for="remember_me" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer group-hover:text-gray-900 transition-colors">
                        Lembrar-me
                    </label>
                </div>

                <!-- Submit Button with premium feel -->
                <button 
                    type="submit"
                    class="w-full py-4 bg-gradient-to-r from-neon-lime-200 via-neon-lime-200 to-neon-lime-300 text-gray-900 font-bold text-lg rounded-xl shadow-lg hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-neon-lime-200/50 relative overflow-hidden group"
                >
                    <span class="relative z-10 flex items-center justify-center">
                        <span>Entrar</span>
                        <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 transform -skew-x-12 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                </button>
            </form>

            <!-- Forgot Password Link -->
            @if (Route::has('password.request'))
                <div class="mt-4 text-center">
                    <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:text-neon-lime-300 font-medium transition-colors duration-200 inline-flex items-center group">
                        <span>Esqueceu sua senha?</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column - Features (Green) - SUPER MODERN -->
    <div class="hidden lg:flex flex-1 bg-gradient-to-br from-neon-lime-200 via-neon-lime-200 to-neon-lime-300 relative overflow-hidden h-screen">
        <!-- Animated background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgb(0,0,0) 1px, transparent 0); background-size: 40px 40px; animation: backgroundMove 20s linear infinite;"></div>
        </div>
        
        <!-- Floating orbs for depth -->
        <div class="absolute top-20 right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-20 left-20 w-96 h-96 bg-white/5 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
        
        <div class="relative z-10 flex flex-col justify-center px-8 sm:px-10 md:px-12 xl:px-16 2xl:px-20 py-12 sm:py-14 md:py-16 overflow-y-auto overflow-x-hidden h-full" style="scrollbar-width: none; -ms-overflow-style: none;">
            <!-- Main Title with impact -->
            <div class="mb-12 xl:mb-16">
                <h2 class="text-4xl xl:text-5xl 2xl:text-6xl font-extrabold text-gray-900 mb-6 leading-tight tracking-tight">
                    A plataforma que te ajuda a encontrar 
                    <span class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 bg-clip-text text-transparent">clientes ideais</span>
                </h2>
                <div class="w-24 h-1.5 bg-gradient-to-r from-gray-900 to-gray-700 rounded-full"></div>
            </div>

            <!-- Features List with premium animations -->
            <ul class="space-y-8 xl:space-y-10">
                <!-- Feature 1 -->
                <li class="flex items-start group">
                    <div class="relative flex-shrink-0">
                        <!-- Connecting Line -->
                        <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-gradient-to-b from-gray-900/20 via-gray-900/30 to-transparent"></div>
                        <!-- Bullet Point with glassmorphism -->
                        <div class="relative w-10 h-10 rounded-full bg-white/80 backdrop-blur-md border-2 border-gray-900/20 shadow-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-white group-hover:shadow-2xl transition-all duration-300">
                            <div class="w-4 h-4 rounded-full bg-gradient-to-br from-neon-lime-300 to-neon-lime-400 shadow-lg"></div>
                            <div class="absolute inset-0 rounded-full bg-neon-lime-200/20 blur-md group-hover:blur-xl transition-all duration-300"></div>
                        </div>
                    </div>
                    <p class="ml-6 text-lg xl:text-xl 2xl:text-2xl text-gray-900 font-bold leading-relaxed group-hover:translate-x-1 transition-transform duration-300">
                        Prospecção automática B2B e B2C com inteligência artificial
                    </p>
                </li>

                <!-- Feature 2 -->
                <li class="flex items-start group">
                    <div class="relative flex-shrink-0">
                        <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-gradient-to-b from-gray-900/20 via-gray-900/30 to-transparent"></div>
                        <div class="relative w-10 h-10 rounded-full bg-white/80 backdrop-blur-md border-2 border-gray-900/20 shadow-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-white group-hover:shadow-2xl transition-all duration-300">
                            <div class="w-4 h-4 rounded-full bg-gradient-to-br from-neon-lime-300 to-neon-lime-400 shadow-lg"></div>
                            <div class="absolute inset-0 rounded-full bg-neon-lime-200/20 blur-md group-hover:blur-xl transition-all duration-300"></div>
                        </div>
                    </div>
                    <p class="ml-6 text-lg xl:text-xl 2xl:text-2xl text-gray-900 font-bold leading-relaxed group-hover:translate-x-1 transition-transform duration-300">
                        Ideal para Designers, Programadores e Gestores de Tráfego
                    </p>
                </li>

                <!-- Feature 3 -->
                <li class="flex items-start group">
                    <div class="relative flex-shrink-0">
                        <div class="absolute left-5 top-10 bottom-0 w-0.5 bg-gradient-to-b from-gray-900/20 via-gray-900/30 to-transparent"></div>
                        <div class="relative w-10 h-10 rounded-full bg-white/80 backdrop-blur-md border-2 border-gray-900/20 shadow-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-white group-hover:shadow-2xl transition-all duration-300">
                            <div class="w-4 h-4 rounded-full bg-gradient-to-br from-neon-lime-300 to-neon-lime-400 shadow-lg"></div>
                            <div class="absolute inset-0 rounded-full bg-neon-lime-200/20 blur-md group-hover:blur-xl transition-all duration-300"></div>
                        </div>
                    </div>
                    <p class="ml-6 text-lg xl:text-xl 2xl:text-2xl text-gray-900 font-bold leading-relaxed group-hover:translate-x-1 transition-transform duration-300">
                        Encontre leads qualificados e aumente suas vendas automaticamente
                    </p>
                </li>

                <!-- Feature 4 -->
                <li class="flex items-start group">
                    <div class="relative flex-shrink-0">
                        <div class="relative w-10 h-10 rounded-full bg-white/80 backdrop-blur-md border-2 border-gray-900/20 shadow-xl flex items-center justify-center group-hover:scale-110 group-hover:bg-white group-hover:shadow-2xl transition-all duration-300">
                            <div class="w-4 h-4 rounded-full bg-gradient-to-br from-neon-lime-300 to-neon-lime-400 shadow-lg"></div>
                            <div class="absolute inset-0 rounded-full bg-neon-lime-200/20 blur-md group-hover:blur-xl transition-all duration-300"></div>
                        </div>
                    </div>
                    <p class="ml-6 text-lg xl:text-xl 2xl:text-2xl text-gray-900 font-bold leading-relaxed group-hover:translate-x-1 transition-transform duration-300">
                        Uma solução completa pensada para te ajudar a vender mais
                    </p>
                </li>
            </ul>
        </div>
    </div>

    <!-- Mobile Features Section - Enhanced -->
    <div class="lg:hidden w-full bg-gradient-to-br from-neon-lime-200 via-neon-lime-200 to-neon-lime-300 px-4 sm:px-6 py-10 sm:py-12 md:py-16 relative overflow-hidden" style="scrollbar-width: none; -ms-overflow-style: none;">
        <!-- Animated background -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, rgb(0,0,0) 1px, transparent 0); background-size: 30px 30px;"></div>
        </div>
        
        <!-- Floating orbs -->
        <div class="absolute top-10 right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 max-w-md mx-auto">
            <div class="mb-8 sm:mb-10">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 mb-3 sm:mb-4 leading-tight tracking-tight">
                    A plataforma que te ajuda a encontrar clientes ideais
                </h2>
                <div class="w-16 sm:w-20 h-1 bg-gradient-to-r from-gray-900 to-gray-700 rounded-full"></div>
            </div>
            <ul class="space-y-6 sm:space-y-8">
                <li class="flex items-start">
                    <div class="relative flex-shrink-0">
                        <div class="absolute left-4 top-7 bottom-0 w-0.5 bg-gradient-to-b from-gray-900/20 via-gray-900/30 to-transparent"></div>
                        <div class="relative w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border-2 border-gray-900/20 shadow-lg flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-gradient-to-br from-neon-lime-300 to-neon-lime-400"></div>
                        </div>
                    </div>
                    <p class="ml-4 text-base sm:text-lg text-gray-900 font-bold leading-relaxed">
                        Prospecção automática B2B e B2C com inteligência artificial
                    </p>
                </li>
                <li class="flex items-start">
                    <div class="relative flex-shrink-0">
                        <div class="absolute left-4 top-7 bottom-0 w-0.5 bg-gradient-to-b from-gray-900/20 via-gray-900/30 to-transparent"></div>
                        <div class="relative w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border-2 border-gray-900/20 shadow-lg flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-gradient-to-br from-neon-lime-300 to-neon-lime-400"></div>
                        </div>
                    </div>
                    <p class="ml-4 text-base sm:text-lg text-gray-900 font-bold leading-relaxed">
                        Ideal para Designers, Programadores e Gestores de Tráfego
                    </p>
                </li>
                <li class="flex items-start">
                    <div class="relative flex-shrink-0">
                        <div class="absolute left-4 top-7 bottom-0 w-0.5 bg-gradient-to-b from-gray-900/20 via-gray-900/30 to-transparent"></div>
                        <div class="relative w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border-2 border-gray-900/20 shadow-lg flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-gradient-to-br from-neon-lime-300 to-neon-lime-400"></div>
                        </div>
                    </div>
                    <p class="ml-4 text-base sm:text-lg text-gray-900 font-bold leading-relaxed">
                        Encontre leads qualificados e aumente suas vendas automaticamente
                    </p>
                </li>
                <li class="flex items-start">
                    <div class="relative flex-shrink-0">
                        <div class="relative w-8 h-8 rounded-full bg-white/80 backdrop-blur-md border-2 border-gray-900/20 shadow-lg flex items-center justify-center">
                            <div class="w-3 h-3 rounded-full bg-gradient-to-br from-neon-lime-300 to-neon-lime-400"></div>
                        </div>
                    </div>
                    <p class="ml-4 text-base sm:text-lg text-gray-900 font-bold leading-relaxed">
                        Uma solução completa pensada para te ajudar a vender mais
                    </p>
                </li>
            </ul>
        </div>
    </div>

    <!-- Custom CSS for animations -->
    <style>
        @keyframes backgroundMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(40px, 40px); }
        }
    </style>
</x-auth-layout>
