<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nova Prospecção') }}
        </h2>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @isset($usage)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-200 dark:border-gray-700">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Uso diário de prospects</h3>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-full">
                                    {{ $usage['daily']['used'] }} / {{ $usage['daily']['quota'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-emerald-400 via-emerald-500 to-emerald-600 transition-all duration-300"
                                     style="width: {{ $usage['daily']['percent'] }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-200 dark:border-gray-700">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Uso mensal de prospects</h3>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-full">
                                    {{ $usage['monthly']['used'] }} / {{ $usage['monthly']['quota'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-indigo-400 via-indigo-500 to-indigo-600 transition-all duration-300"
                                     style="width: {{ $usage['monthly']['percent'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6 sm:p-8">
                    @if($errors->has('quota'))
                        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                        Limite de Cota Atingido
                                    </h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                        <p>{{ $errors->first('quota') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($usage))
                        @php
                            $dailyExceeded = $usage['daily']['quota'] > 0 && $usage['daily']['used'] >= $usage['daily']['quota'];
                            $monthlyExceeded = $usage['monthly']['quota'] > 0 && $usage['monthly']['used'] >= $usage['monthly']['quota'];
                        @endphp
                        @if($dailyExceeded || $monthlyExceeded)
                            <div class="mb-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                            Atenção: Cota Atingida
                                        </h3>
                                        <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                                            @if($dailyExceeded && $monthlyExceeded)
                                                <p>Você atingiu o limite diário (<strong>{{ $usage['daily']['used'] }}/{{ $usage['daily']['quota'] }}</strong>) e mensal (<strong>{{ $usage['monthly']['used'] }}/{{ $usage['monthly']['quota'] }}</strong>) de prospects.</p>
                                            @elseif($dailyExceeded)
                                                <p>Você atingiu o limite diário de prospects (<strong>{{ $usage['daily']['used'] }}/{{ $usage['daily']['quota'] }}</strong>). Tente novamente amanhã.</p>
                                            @elseif($monthlyExceeded)
                                                <p>Você atingiu o limite mensal de prospects (<strong>{{ $usage['monthly']['used'] }}/{{ $usage['monthly']['quota'] }}</strong>). Entre em contato para aumentar sua cota.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('prospects.store') }}">
                        @csrf

                        <!-- Cidade -->
                        <div class="mb-6">
                            <label for="cidade" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Cidade
                                    <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       name="cidade" 
                                       id="cidade"
                                       value="{{ old('cidade') }}"
                                       required
                                       autofocus
                                       autocomplete="off"
                                       placeholder="Digite o nome da cidade..."
                                       class="block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white px-4 py-3 transition-all duration-200 @error('cidade') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                                <div id="cidade-autocomplete" class="absolute z-50 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl hidden max-h-72 overflow-auto"></div>
                                <div id="cidade-nearby" class="mt-2 hidden">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Cidades próximas:</p>
                                    <div id="nearby-cities-list" class="flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                            @error('cidade')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Nicho -->
                        <div class="mb-6">
                            <label for="nicho" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Nicho
                                    <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <input type="text" 
                                   name="nicho" 
                                   id="nicho"
                                   value="{{ old('nicho') }}"
                                   required
                                   placeholder="Ex: Restaurante, Academia, Clínica, Loja de roupas"
                                   class="block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white px-4 py-3 transition-all duration-200 @error('nicho') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                            @error('nicho')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-2.5 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Descreva o tipo de negócio que você deseja prospectar.
                            </p>
                        </div>

                        <!-- Quantidade de Resultados -->
                        <div class="mb-6">
                            <label for="max_results" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2.5">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    Quantos resultados trazer?
                                </span>
                            </label>
                            
                            <div class="space-y-4">
                                <!-- Input numérico e slider -->
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <input type="range" 
                                               id="max_results_slider"
                                               min="1" 
                                               max="{{ $maxApiFetches }}" 
                                               value="{{ old('max_results', $maxApiFetches) }}"
                                               class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                    </div>
                                    <div class="w-24">
                                        <input type="number" 
                                               name="max_results" 
                                               id="max_results"
                                               min="1" 
                                               max="{{ $maxApiFetches }}"
                                               value="{{ old('max_results', $maxApiFetches) }}"
                                               required
                                               class="block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white px-4 py-2.5 text-center font-semibold text-lg transition-all duration-200 @error('max_results') border-red-500 focus:border-red-500 focus:ring-red-500/20 @enderror">
                                    </div>
                                </div>
                                
                                <!-- Preview e aviso de múltiplos jobs -->
                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                    <div id="results-preview" class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Você buscará <span id="results-count" class="font-bold text-indigo-600 dark:text-indigo-400">{{ old('max_results', $maxApiFetches) }}</span> resultado(s)
                                        </span>
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 px-2.5 py-1 rounded-full border border-gray-200 dark:border-gray-700">
                                            Máx: {{ $maxApiFetches }}
                                        </span>
                                    </div>
                                    <div id="quota-warning" class="hidden mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                                    Múltiplos jobs serão criados
                                                </p>
                                                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1" id="quota-warning-text">
                                                    A busca será dividida em <span id="jobs-count" class="font-semibold">2</span> job(s) para respeitar sua cota disponível.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @error('max_results')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Info Box -->
                        <div class="mb-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50">
                                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">
                                        Como funciona?
                                    </h3>
                                    <div class="text-sm text-blue-800 dark:text-blue-300 space-y-2">
                                        <p>O sistema irá buscar empresas no Google Maps com base na cidade e nicho informados. Para cada empresa encontrada, serão coletados:</p>
                                        <ul class="list-none space-y-1.5 mt-2">
                                            <li class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Nome da empresa</span>
                                            </li>
                                            <li class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Telefone e WhatsApp (quando disponível)</span>
                                            </li>
                                            <li class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>E-mail (quando disponível no site)</span>
                                            </li>
                                            <li class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>Site e endereço</span>
                                            </li>
                                        </ul>
                                        <p class="mt-3 font-medium flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            O processo pode levar alguns minutos. Você receberá uma notificação quando estiver concluído.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('prospects.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-all duration-200">
                                Cancelar
                            </a>
                            @php
                                $canProspect = true;
                                if (isset($usage)) {
                                    $dailyExceeded = $usage['daily']['quota'] > 0 && $usage['daily']['used'] >= $usage['daily']['quota'];
                                    $monthlyExceeded = $usage['monthly']['quota'] > 0 && $usage['monthly']['used'] >= $usage['monthly']['quota'];
                                    $canProspect = !$dailyExceeded && !$monthlyExceeded;
                                }
                            @endphp
                            <button type="submit" 
                                    @if(!$canProspect) disabled @endif
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 border border-transparent rounded-xl font-semibold text-sm text-white shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 @if(!$canProspect) opacity-50 cursor-not-allowed @endif">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                @if($canProspect)
                                    Iniciar Prospecção
                                @else
                                    Cota Atingida
                                @endif
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxApiFetches = {{ $maxApiFetches }};
            const dailyQuota = {{ $usage['daily']['quota'] ?? 0 }};
            const dailyUsed = {{ $usage['daily']['used'] ?? 0 }};
            const monthlyQuota = {{ $usage['monthly']['quota'] ?? 0 }};
            const monthlyUsed = {{ $usage['monthly']['used'] ?? 0 }};
            
            // Calcula cota disponível (menor entre diária e mensal)
            const availableDaily = Math.max(0, dailyQuota - dailyUsed);
            const availableMonthly = Math.max(0, monthlyQuota - monthlyUsed);
            const availableQuota = Math.min(availableDaily, availableMonthly);
            
            // Elementos do formulário
            const cidadeInput = document.getElementById('cidade');
            const autocompleteDiv = document.getElementById('cidade-autocomplete');
            const nearbyDiv = document.getElementById('cidade-nearby');
            const nearbyList = document.getElementById('nearby-cities-list');
            const maxResultsInput = document.getElementById('max_results');
            const maxResultsSlider = document.getElementById('max_results_slider');
            const resultsCount = document.getElementById('results-count');
            const quotaWarning = document.getElementById('quota-warning');
            const jobsCount = document.getElementById('jobs-count');
            
            let searchTimeout;
            let selectedIndex = -1;
            let selectedCityData = null;

            // Sincroniza slider e input numérico
            function syncInputs() {
                const value = parseInt(maxResultsInput.value) || 1;
                const clampedValue = Math.max(1, Math.min(maxApiFetches, value));
                
                if (maxResultsSlider.value != clampedValue) {
                    maxResultsSlider.value = clampedValue;
                }
                if (maxResultsInput.value != clampedValue) {
                    maxResultsInput.value = clampedValue;
                }
                
                updateResultsPreview(clampedValue);
            }

            maxResultsSlider.addEventListener('input', function() {
                maxResultsInput.value = this.value;
                updateResultsPreview(parseInt(this.value));
            });

            maxResultsInput.addEventListener('input', function() {
                syncInputs();
            });

            maxResultsInput.addEventListener('blur', function() {
                syncInputs();
            });

            // Atualiza preview e aviso de múltiplos jobs
            function updateResultsPreview(count) {
                resultsCount.textContent = count;
                
                if (count > availableQuota && availableQuota > 0) {
                    const jobsNeeded = Math.ceil(count / availableQuota);
                    jobsCount.textContent = jobsNeeded;
                    quotaWarning.classList.remove('hidden');
                } else {
                    quotaWarning.classList.add('hidden');
                }
            }

            // Inicializa preview
            updateResultsPreview(parseInt(maxResultsInput.value) || maxApiFetches);

            // Autocomplete de cidades
            cidadeInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    autocompleteDiv.classList.add('hidden');
                    nearbyDiv.classList.add('hidden');
                    selectedIndex = -1;
                    selectedCityData = null;
                    return;
                }

                // Mostra loading
                autocompleteDiv.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Buscando cidades...</div>';
                autocompleteDiv.classList.remove('hidden');

                searchTimeout = setTimeout(() => {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    fetch(`/api/cities/search?q=${encodeURIComponent(query)}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (Array.isArray(data) && data.length > 0) {
                            autocompleteDiv.innerHTML = data.map((city, index) => `
                                <div class="px-4 py-3 hover:bg-indigo-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-150 ${index === 0 ? 'bg-indigo-50 dark:bg-gray-700' : ''}" 
                                     data-index="${index}" 
                                     data-value="${city.text}"
                                     data-city="${city.city}"
                                     data-state="${city.state || ''}"
                                     data-lat="${city.lat || ''}"
                                     data-lng="${city.lng || ''}">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">${city.city}</div>
                                            ${city.state ? `<div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">${city.state}</div>` : ''}
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                            autocompleteDiv.classList.remove('hidden');
                            selectedIndex = 0;
                        } else {
                            autocompleteDiv.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">Nenhuma cidade encontrada</div>';
                            autocompleteDiv.classList.remove('hidden');
                            selectedIndex = -1;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching cities:', error);
                        autocompleteDiv.innerHTML = '<div class="px-4 py-3 text-sm text-red-500 dark:text-red-400 flex items-center gap-2"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg> Erro ao buscar cidades</div>';
                        autocompleteDiv.classList.remove('hidden');
                        selectedIndex = -1;
                    });
                }, 300);
            });

            // Busca cidades próximas quando uma cidade é selecionada
            function loadNearbyCities(cityData) {
                if (!cityData || !cityData.lat || !cityData.lng) {
                    nearbyDiv.classList.add('hidden');
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                fetch(`/api/cities/nearby?lat=${cityData.lat}&lng=${cityData.lng}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data) && data.length > 0) {
                        nearbyList.innerHTML = data.map(city => `
                            <button type="button" 
                                    class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-200"
                                    onclick="document.getElementById('cidade').value='${city.text}'; document.getElementById('cidade-nearby').classList.add('hidden');">
                                ${city.city}${city.state ? ', ' + city.state : ''}
                            </button>
                        `).join('');
                        nearbyDiv.classList.remove('hidden');
                    } else {
                        nearbyDiv.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error fetching nearby cities:', error);
                    nearbyDiv.classList.add('hidden');
                });
            }

            // Navegação com teclado
            cidadeInput.addEventListener('keydown', function(e) {
                const items = autocompleteDiv.querySelectorAll('[data-index]');
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    updateSelection(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    updateSelection(items);
                } else if (e.key === 'Enter' && selectedIndex >= 0) {
                    e.preventDefault();
                    const selected = items[selectedIndex];
                    if (selected) {
                        cidadeInput.value = selected.dataset.value;
                        selectedCityData = {
                            city: selected.dataset.city,
                            state: selected.dataset.state,
                            lat: selected.dataset.lat,
                            lng: selected.dataset.lng
                        };
                        autocompleteDiv.classList.add('hidden');
                        loadNearbyCities(selectedCityData);
                    }
                } else if (e.key === 'Escape') {
                    autocompleteDiv.classList.add('hidden');
                }
            });

            function updateSelection(items) {
                items.forEach((item, index) => {
                    if (index === selectedIndex) {
                        item.classList.add('bg-indigo-50', 'dark:bg-gray-700');
                    } else {
                        item.classList.remove('bg-indigo-50', 'dark:bg-gray-700');
                    }
                });
            }

            // Clique em item
            autocompleteDiv.addEventListener('click', function(e) {
                const item = e.target.closest('[data-value]');
                if (item) {
                    cidadeInput.value = item.dataset.value;
                    selectedCityData = {
                        city: item.dataset.city,
                        state: item.dataset.state,
                        lat: item.dataset.lat,
                        lng: item.dataset.lng
                    };
                    autocompleteDiv.classList.add('hidden');
                    loadNearbyCities(selectedCityData);
                }
            });

            // Fechar ao clicar fora
            document.addEventListener('click', function(e) {
                if (!cidadeInput.contains(e.target) && !autocompleteDiv.contains(e.target)) {
                    autocompleteDiv.classList.add('hidden');
                    selectedIndex = -1;
                }
            });

            // Atualiza selectedIndex quando o mouse passa sobre um item
            autocompleteDiv.addEventListener('mouseover', function(e) {
                const item = e.target.closest('[data-index]');
                if (item) {
                    selectedIndex = parseInt(item.dataset.index);
                    const items = autocompleteDiv.querySelectorAll('[data-index]');
                    updateSelection(items);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

