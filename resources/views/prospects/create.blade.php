<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nova Prospecção') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @isset($usage)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Uso diário de prospects</h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $usage['daily']['used'] }} / {{ $usage['daily']['quota'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-500"
                                     style="width: {{ $usage['daily']['percent'] }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Uso mensal de prospects</h3>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $usage['monthly']['used'] }} / {{ $usage['monthly']['quota'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full bg-gradient-to-r from-indigo-400 to-indigo-500"
                                     style="width: {{ $usage['monthly']['percent'] }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($errors->has('quota'))
                        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
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
                            <div class="mb-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
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
                            <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Cidade <span class="text-red-500">*</span>
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
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('cidade') border-red-500 @enderror">
                                <div id="cidade-autocomplete" class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg hidden max-h-60 overflow-auto"></div>
                            </div>
                            @error('cidade')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nicho -->
                        <div class="mb-6">
                            <label for="nicho" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nicho <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="nicho" 
                                   id="nicho"
                                   value="{{ old('nicho') }}"
                                   required
                                   placeholder="Ex: Restaurante, Academia, Clínica, Loja de roupas"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('nicho') border-red-500 @enderror">
                            @error('nicho')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Descreva o tipo de negócio que você deseja prospectar.
                            </p>
                        </div>

                        <!-- Info Box -->
                        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                        Como funciona?
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                        <p>O sistema irá buscar empresas no Google Maps com base na cidade e nicho informados. Para cada empresa encontrada, serão coletados:</p>
                                        <ul class="list-disc list-inside mt-2 space-y-1">
                                            <li>Nome da empresa</li>
                                            <li>Telefone e WhatsApp (quando disponível)</li>
                                            <li>E-mail (quando disponível no site)</li>
                                            <li>Site e endereço</li>
                                        </ul>
                                        <p class="mt-2 font-medium">O processo pode levar alguns minutos. Você receberá uma notificação quando estiver concluído.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('prospects.index') }}" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
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
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 @if(!$canProspect) opacity-50 cursor-not-allowed @endif">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            const cidadeInput = document.getElementById('cidade');
            const autocompleteDiv = document.getElementById('cidade-autocomplete');
            let searchTimeout;
            let selectedIndex = -1;

            cidadeInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    autocompleteDiv.classList.add('hidden');
                    selectedIndex = -1;
                    return;
                }

                // Mostra loading
                autocompleteDiv.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">Buscando cidades...</div>';
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
                                <div class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer ${index === 0 ? 'bg-gray-50 dark:bg-gray-700' : ''}" 
                                     data-index="${index}" 
                                     data-value="${city.text}">
                                    <div class="font-medium text-gray-900 dark:text-gray-100">${city.city}</div>
                                    ${city.state ? `<div class="text-xs text-gray-500 dark:text-gray-400">${city.state}</div>` : ''}
                                </div>
                            `).join('');
                            autocompleteDiv.classList.remove('hidden');
                            selectedIndex = 0;
                        } else {
                            autocompleteDiv.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">Nenhuma cidade encontrada</div>';
                            autocompleteDiv.classList.remove('hidden');
                            selectedIndex = -1;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching cities:', error);
                        autocompleteDiv.innerHTML = '<div class="px-4 py-2 text-sm text-red-500 dark:text-red-400">Erro ao buscar cidades</div>';
                        autocompleteDiv.classList.remove('hidden');
                        selectedIndex = -1;
                    });
                }, 300);
            });

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
                        autocompleteDiv.classList.add('hidden');
                    }
                } else if (e.key === 'Escape') {
                    autocompleteDiv.classList.add('hidden');
                }
            });

            function updateSelection(items) {
                items.forEach((item, index) => {
                    if (index === selectedIndex) {
                        item.classList.add('bg-gray-100', 'dark:bg-gray-700');
                    } else {
                        item.classList.remove('bg-gray-100', 'dark:bg-gray-700');
                    }
                });
            }

            // Clique em item
            autocompleteDiv.addEventListener('click', function(e) {
                const item = e.target.closest('[data-value]');
                if (item) {
                    cidadeInput.value = item.dataset.value;
                    autocompleteDiv.classList.add('hidden');
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

