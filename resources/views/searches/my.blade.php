<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Minhas Pesquisas Salvas') }}
            </h2>
            <a href="{{ route('prospects.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Buscar clientes
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagens de Feedback -->
            @if(session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-semibold text-green-800 dark:text-green-200 mb-1">
                                {{ session('success') }}
                            </p>
                            <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                ⏳ O job foi despachado na fila. Os novos prospects aparecerão automaticamente quando o processamento for concluído.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                {{ session('info') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->has('quota'))
                <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                {{ $errors->first('quota') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if(count($searches) > 0)
                <!-- Barra de Busca -->
                <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        <form method="GET" action="{{ route('searches.my') }}" class="flex flex-col sm:flex-row gap-4 items-end">
                            <div class="flex-1 min-w-[200px] w-full">
                                <label for="search" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Buscar pesquisas
                                    </span>
                                </label>
                                <input type="text" 
                                       name="search" 
                                       id="search"
                                       value="{{ request('search') }}"
                                       placeholder="Digite cidade, nicho ou serviço..."
                                       class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:border-gray-600 dark:text-white px-4 py-3 transition-all duration-200">
                            </div>
                            <div class="flex items-end gap-2 w-full sm:w-auto">
                                <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-md hover:shadow-lg transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Buscar
                                </button>
                                @if(request()->has('search'))
                                    <a href="{{ route('searches.my') }}" class="inline-flex items-center justify-center px-4 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Grid de Blocos de Pesquisas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($searches as $search)
                        <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-200 dark:border-gray-700 hover:border-neon-lime-200 dark:hover:border-neon-lime-300">
                            <!-- Header do Card -->
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-white mb-1">
                                            {{ $search['cidade'] }}
                                        </h3>
                                        <p class="text-sm text-blue-100 font-medium">
                                            {{ $search['nicho'] }}
                                        </p>
                                    </div>
                                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-sm text-blue-100">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        <span>{{ $search['results_count'] }} resultados</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span>{{ $search['prospect_count'] }} prospects</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Body do Card -->
                            <div class="p-6">
                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-6">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span>Criada em {{ \Carbon\Carbon::parse($search['created_at'])->format('d/m/Y') }}</span>
                                    </div>
                                </div>

                                <!-- Botões de Ação -->
                                <div class="space-y-3">
                                    <!-- Ver Pesquisa -->
                                    <a href="{{ route('prospects.index', ['cidade' => $search['cidade'], 'nicho' => $search['nicho']]) }}" class="block w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Ver Pesquisa
                                    </a>

                                    <!-- Buscar Mais Resultados -->
                                    <button type="button" 
                                            onclick="openSearchMoreModal({{ $search['search_id'] }}, '{{ $search['cidade'] }}', '{{ $search['nicho'] }}')"
                                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg hover:from-neon-lime-300 hover:to-neon-lime-400 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Buscar Mais Resultados
                                    </button>

                                    <!-- Botões de Exportação -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('searches.export.csv', $search['search_id']) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors font-medium text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            CSV
                                        </a>
                                        <a href="{{ route('searches.export.xlsx', $search['search_id']) }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium text-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            XLSX
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Estado Vazio -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-700 mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        Nenhuma pesquisa salva
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">
                        Quando você criar e completar prospecções, elas aparecerão aqui como pesquisas salvas.
                    </p>
                    <a href="{{ route('prospects.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg hover:shadow-lg transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Criar Primeira Prospecção
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Quota Excedida -->
    @if(isset($quotaData) && $quotaData['exceeded'])
        <x-quota-exceeded-modal :quotaData="$quotaData" :user="$user" />
    @endif

    <!-- Modal Buscar Mais Resultados -->
    <div id="searchMoreModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeSearchMoreModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="searchMoreForm" method="POST" action="">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">
                                    🔍 Buscar Mais Resultados
                                </h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        <span id="modal-city-nicho"></span>
                                    </p>
                                    
                                    <!-- Aviso sobre novos resultados -->
                                    <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-3">
                                        <div class="flex items-start">
                                            <svg class="h-5 w-5 text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                            <p class="text-xs text-blue-800 dark:text-blue-200">
                                                <strong>Importante:</strong> Esta busca irá consultar a API do Google Maps para trazer <strong>NOVOS resultados</strong> que ainda não estão no banco de dados. O job será processado na fila e os novos prospects aparecerão em alguns instantes.
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="max_results_modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Quantos NOVOS resultados buscar?
                                        </label>
                                        <div class="space-y-4">
                                            <div class="flex items-center gap-4">
                                                <div class="flex-1">
                                                    <input type="range" 
                                                           id="max_results_modal_slider"
                                                           min="1" 
                                                           max="{{ auth()->user()->getEffectiveMaxApiFetches() }}" 
                                                           value="{{ auth()->user()->getEffectiveMaxApiFetches() }}"
                                                           class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                                </div>
                                                <div class="w-24">
                                                    <input type="number" 
                                                           name="max_results" 
                                                           id="max_results_modal"
                                                           min="1" 
                                                           max="{{ auth()->user()->getEffectiveMaxApiFetches() }}"
                                                           value="{{ auth()->user()->getEffectiveMaxApiFetches() }}"
                                                           required
                                                           class="block w-full rounded-xl border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:bg-gray-700 dark:text-white px-4 py-2.5 text-center font-semibold text-lg">
                                                </div>
                                            </div>
                                            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-4 border border-indigo-200 dark:border-indigo-800">
                                                <p class="text-sm text-indigo-800 dark:text-indigo-200">
                                                    Você buscará <span id="modal-results-count" class="font-bold text-indigo-600 dark:text-indigo-400">{{ auth()->user()->getEffectiveMaxApiFetches() }}</span> <strong>NOVO(S)</strong> resultado(s) na API
                                                </p>
                                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-2">
                                                    Máximo permitido: <span class="font-semibold">{{ auth()->user()->getEffectiveMaxApiFetches() }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-base font-medium text-white hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Buscar Novos Resultados
                        </button>
                        <button type="button" onclick="closeSearchMoreModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let sliderListener = null;
        let inputListener = null;
        
        function openSearchMoreModal(searchId, cidade, nicho) {
            const modal = document.getElementById('searchMoreModal');
            const form = document.getElementById('searchMoreForm');
            const cityNicho = document.getElementById('modal-city-nicho');
            const maxResults = {{ auth()->user()->getEffectiveMaxApiFetches() }};
            
            form.action = `/pesquisas/${searchId}/buscar-mais`;
            cityNicho.textContent = `${cidade} - ${nicho}`;
            
            // Remove listeners anteriores se existirem
            const slider = document.getElementById('max_results_modal_slider');
            const input = document.getElementById('max_results_modal');
            const count = document.getElementById('modal-results-count');
            
            if (sliderListener) {
                slider.removeEventListener('input', sliderListener);
            }
            if (inputListener) {
                input.removeEventListener('input', inputListener);
            }
            
            // Sincroniza slider e input
            sliderListener = function() {
                input.value = this.value;
                count.textContent = this.value;
            };
            
            inputListener = function() {
                const value = Math.max(1, Math.min(maxResults, parseInt(this.value) || 1));
                slider.value = value;
                this.value = value;
                count.textContent = value;
            };
            
            slider.addEventListener('input', sliderListener);
            input.addEventListener('input', inputListener);
            
            // Atualiza valores iniciais
            const initialValue = maxResults;
            slider.value = initialValue;
            input.value = initialValue;
            count.textContent = initialValue;
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeSearchMoreModal() {
            const modal = document.getElementById('searchMoreModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Fecha modal com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('searchMoreModal');
                if (!modal.classList.contains('hidden')) {
                    closeSearchMoreModal();
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
