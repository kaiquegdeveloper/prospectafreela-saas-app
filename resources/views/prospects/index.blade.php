<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Prospects') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('prospects.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exportar CSV
                </a>
                <a href="{{ route('prospects.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Buscar clientes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagens de Feedback -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Uso de Cota -->
            @isset($usage)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Você usou {{ $usage['daily']['percent'] }}% da sua cota diária.
                            </p>
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
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Você usou {{ $usage['monthly']['percent'] }}% da sua cota mensal.
                            </p>
                        </div>
                    </div>
                </div>
            @endisset

            <!-- Filtros e Busca -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('prospects.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Buscar
                            </label>
                            <input type="text" 
                                   name="search" 
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Nome, email, telefone, cidade..."
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="min-w-[150px]">
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select name="status" 
                                    id="status"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Todos</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                                <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Concluído</option>
                                <option value="error" {{ request('status') === 'error' ? 'selected' : '' }}>Erro</option>
                            </select>
                        </div>
                        <div class="min-w-[180px]">
                            <label for="nicho" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nicho
                            </label>
                            <select name="nicho"
                                    id="nicho"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Todos</option>
                                @isset($niches)
                                    @foreach($niches as $niche)
                                        <option value="{{ $niche }}" {{ request('nicho') === $niche ? 'selected' : '' }}>
                                            {{ $niche }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Filtrar
                            </button>
                            @if(request()->hasAny(['search', 'status', 'nicho']))
                                <a href="{{ route('prospects.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Limpar
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabela de Prospects -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    @if($prospects->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contato</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cidade / Nicho</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($prospects as $prospect)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $prospect->nome }}
                                            </div>
                                            @if($prospect->site)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    <a href="{{ $prospect->site }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                        {{ Str::limit($prospect->site, 40) }}
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                @if($prospect->email)
                                                    <div class="flex items-center mb-1">
                                                        <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span class="text-xs">{{ $prospect->email }}</span>
                                                    </div>
                                                @endif
                                                @if($prospect->telefone)
                                                    <div class="flex items-center mb-1">
                                                        <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                        </svg>
                                                        <span class="text-xs">{{ $prospect->telefone }}</span>
                                                    </div>
                                                @endif
                                                @if($prospect->whatsapp)
                                                    <div class="flex items-center">
                                                        <svg class="h-4 w-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                                        </svg>
                                                        <span class="text-xs text-green-600 dark:text-green-400">{{ $prospect->whatsapp }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @php
                                                    $waNumber = $prospect->whatsapp_link_number;
                                                @endphp
                                                @if($waNumber)
                                                    <a href="https://wa.me/{{ $waNumber }}@if(!empty($whatsappMessage))?text={{ urlencode($whatsappMessage) }}@endif"
                                                       target="_blank"
                                                       class="inline-flex items-center px-2 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 text-[11px] font-medium hover:bg-emerald-500/20">
                                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                                        </svg>
                                                        Chamar no WhatsApp
                                                    </a>
                                                @endif
                                                @if($prospect->email)
                                                    <a href="mailto:{{ $prospect->email }}"
                                                       class="inline-flex items-center px-2 py-1 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-300 text-[11px] font-medium hover:bg-blue-500/20">
                                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Enviar e-mail
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $prospect->cidade }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $prospect->nicho }}</div>
                                            @if($prospect->lead)
                                                <div class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-700 dark:text-amber-300 text-[11px] font-medium">
                                                    Lead • R$ {{ number_format($prospect->lead->opportunity_value ?? 0, 2, ',', '.') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($prospect->status === 'done')
                                                <span data-status="done" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Concluído
                                                </span>
                                            @elseif($prospect->status === 'pending')
                                                <span data-status="pending" class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Pendente
                                                </span>
                                            @else
                                                <span data-status="error" class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Erro
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('prospects.show', $prospect) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                                Ver
                                            </a>
                                            <form action="{{ route('prospects.destroy', $prospect) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este prospect?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $prospects->links() }}
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Nenhum prospect encontrado</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                @if(request()->hasAny(['search', 'status']))
                                    Tente ajustar os filtros ou
                                @endif
                                <a href="{{ route('prospects.create') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    inicie uma nova prospecção
                                </a>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Quota Excedida -->
    @if(isset($quotaData) && $quotaData['exceeded'])
        <x-quota-exceeded-modal :quotaData="$quotaData" :user="$user" />
    @endif

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Marca o tempo de carregamento da página
            window.pageLoadTime = Date.now();
            
            const firstProspect = @json($prospects->first());
            let lastProspectId = firstProspect ? firstProspect.id : 0;
            let currentCount = {{ $prospects->total() }};
            let pollingInterval;
            let isPolling = false;

            // Inicia polling apenas se houver prospects pendentes ou se acabou de criar uma prospecção
            const hasPendingProspects = {{ $prospects->where('status', 'pending')->count() > 0 ? 'true' : 'false' }};
            const justCreated = {{ (session('success') || session('info')) ? 'true' : 'false' }};

            // Sempre inicia polling se houver mensagem de sucesso/info (nova prospecção criada)
            if (justCreated || hasPendingProspects) {
                startPolling();
            }

            // Se acabou de criar, força atualização mais rápida
            if (justCreated) {
                // Primeira verificação imediata
                setTimeout(() => checkForNewProspects(), 1000);
            }

            function startPolling() {
                if (isPolling) return;
                isPolling = true;

                pollingInterval = setInterval(() => {
                    checkForNewProspects();
                }, 3000); // Verifica a cada 3 segundos
            }

            function stopPolling() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    isPolling = false;
                }
            }

            function checkForNewProspects() {
                fetch(`/api/prospects/check-new?last_id=${lastProspectId}&count=${currentCount}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.has_new) {
                        // Atualiza contadores
                        if (data.new_prospects && data.new_prospects.length > 0) {
                            lastProspectId = Math.max(...data.new_prospects.map(p => p.id));
                        }
                        currentCount = data.total_count;
                        
                        // Recarrega a página para mostrar novos prospects
                        if (data.new_count > 0 || data.total_count !== currentCount) {
                            location.reload();
                        }
                    }

                    // Para polling se não há mais prospects pendentes (mas continua por um tempo se acabou de criar)
                    const stillPending = document.querySelector('span[data-status="pending"]') !== null;
                    if (!stillPending) {
                        // Se acabou de criar, continua por mais 30 segundos antes de parar
                        if (justCreated && Date.now() - window.pageLoadTime < 30000) {
                            // Continua polling
                        } else {
                            stopPolling();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking for new prospects:', error);
                });
            }

            // Para polling quando a página perde foco (economiza recursos)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopPolling();
                } else if (hasPendingProspects || justCreated) {
                    startPolling();
                }
            });

            // Busca reativa: submete o formulário ao digitar / alterar filtros (com debounce)
            const filterForm = document.querySelector('form[action="{{ route('prospects.index') }}"]');
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status');
            const nicheSelect = document.getElementById('nicho');
            let searchTimeout;

            function debounceSubmit() {
                if (!filterForm) return;
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterForm.submit();
                }, 500);
            }

            if (searchInput) {
                searchInput.addEventListener('input', debounceSubmit);
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', debounceSubmit);
            }

            if (nicheSelect) {
                nicheSelect.addEventListener('change', debounceSubmit);
            }
        });
    </script>
    @endpush
</x-app-layout>

