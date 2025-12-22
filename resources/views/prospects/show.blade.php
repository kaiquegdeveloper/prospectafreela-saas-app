<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detalhes do Prospect') }}
            </h2>
            <a href="{{ route('prospects.index') }}" class="text-sm text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
                ← Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6 sm:p-8">
                    <!-- Header -->
                    <div class="mb-8 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-3">
                                    {{ $prospect->nome }}
                                </h1>
                                <div class="flex flex-wrap items-center gap-3">
                                    @if($prospect->status === 'done')
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-full bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 dark:from-green-900 dark:to-emerald-900 dark:text-green-200 border border-green-200 dark:border-green-800">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                            Concluído
                                        </span>
                                    @elseif($prospect->status === 'pending')
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-full bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 dark:from-yellow-900 dark:to-amber-900 dark:text-yellow-200 border border-yellow-200 dark:border-yellow-800">
                                            <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                                            Pendente
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 text-sm font-semibold rounded-full bg-gradient-to-r from-red-100 to-rose-100 text-red-800 dark:from-red-900 dark:to-rose-900 dark:text-red-200 border border-red-200 dark:border-red-800">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                            Erro
                                        </span>
                                    @endif
                                    <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Criado em {{ $prospect->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @php
                                    $waNumber = $prospect->whatsapp_link_number;
                                @endphp
                                @php
                                    $waNumber = $prospect->whatsapp_link_number;
                                @endphp
                                @if($waNumber)
                                    <a href="https://wa.me/{{ $waNumber }}@if(!empty($whatsappMessage ?? ''))?text={{ urlencode($whatsappMessage) }}@endif" 
                                       target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-colors shadow-md hover:shadow-lg">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                        WhatsApp
                                    </a>
                                @endif
                                @if($prospect->email)
                                    <a href="mailto:{{ $prospect->email }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors shadow-md hover:shadow-lg">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        E-mail
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Informações -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Contato -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Informações de Contato
                            </h3>
                            <dl class="space-y-3">
                                @if($prospect->email)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            <a href="mailto:{{ $prospect->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                {{ $prospect->email }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                                @if($prospect->telefone)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefone</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            <a href="tel:{{ $prospect->telefone }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                {{ $prospect->telefone }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                                @php
                                    $waNumber = $prospect->whatsapp_link_number;
                                @endphp
                                @if($waNumber)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">WhatsApp</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="inline-flex items-center text-green-600 hover:text-green-800 dark:text-green-400">
                                                <svg class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                                </svg>
                                                {{ $prospect->whatsapp ?? $prospect->telefone }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                                @if($prospect->site)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Site</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ $prospect->site }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                {{ $prospect->site }}
                                                <svg class="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Localização e Contexto -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Localização e Contexto
                            </h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Cidade</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $prospect->cidade }}</dd>
                                </div>
                                @if($prospect->endereco)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Endereço</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $prospect->endereco }}</dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nicho</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $prospect->nicho }}</dd>
                                </div>
                                @if($prospect->google_maps_url)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Google Maps</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ $prospect->google_maps_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                                Ver no Google Maps
                                                <svg class="inline h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                            </a>
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Lead / Mini CRM -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Lead / Oportunidade</h3>
                        <form method="POST" action="{{ route('prospects.lead', $prospect) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Valor da oportunidade (R$)
                                </label>
                                <input type="number" step="0.01" min="0"
                                       name="opportunity_value"
                                       value="{{ old('opportunity_value', optional($prospect->lead)->opportunity_value) }}"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Probabilidade (%)
                                </label>
                                <input type="number" min="0" max="100"
                                       name="probability"
                                       value="{{ old('probability', optional($prospect->lead)->probability) }}"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Estágio
                                </label>
                                <select name="stage"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                                    @php
                                        $currentStage = old('stage', optional($prospect->lead)->stage);
                                    @endphp
                                    <option value="">Selecione</option>
                                    <option value="novo" {{ $currentStage === 'novo' ? 'selected' : '' }}>Novo</option>
                                    <option value="contatado" {{ $currentStage === 'contatado' ? 'selected' : '' }}>Contatado</option>
                                    <option value="negociando" {{ $currentStage === 'negociando' ? 'selected' : '' }}>Negociando</option>
                                    <option value="ganho" {{ $currentStage === 'ganho' ? 'selected' : '' }}>Fechado - Ganho</option>
                                    <option value="perdido" {{ $currentStage === 'perdido' ? 'selected' : '' }}>Fechado - Perdido</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Data estimada de fechamento
                                </label>
                                <input type="date"
                                       name="expected_close_date"
                                       value="{{ old('expected_close_date', optional($prospect->lead?->expected_close_date)->format('Y-m-d')) }}"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Notas internas
                                </label>
                                <textarea name="notes" rows="3"
                                          class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100">{{ old('notes', optional($prospect->lead)->notes) }}</textarea>
                                <div class="mt-2 flex items-center gap-2">
                                    <input type="checkbox" id="is_private" name="is_private" value="1"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           {{ old('is_private', optional($prospect->lead)->is_private ?? true) ? 'checked' : '' }}>
                                    <label for="is_private" class="text-xs text-gray-600 dark:text-gray-400">
                                        Visível apenas para você (mantemos registro no sistema para histórico)
                                    </label>
                                </div>
                            </div>
                            <div class="md:col-span-2 flex justify-end gap-3">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Salvar Lead
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Ações -->
                    <div class="mt-6 flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <form action="{{ route('prospects.destroy', $prospect) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este prospect?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Excluir Prospect
                            </button>
                        </form>
                        <a href="{{ route('prospects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

