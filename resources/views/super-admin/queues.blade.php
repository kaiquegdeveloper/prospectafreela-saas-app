<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                        Monitoramento de Filas
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Controle e monitore as filas de processamento</p>
                </div>
                <a href="{{ route('super-admin.dashboard') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Voltar
                </a>
            </div>

            <!-- Queue Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Fila: Prospecting</h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $isProspectingPaused ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' }}">
                            {{ $isProspectingPaused ? 'Pausada' : 'Ativa' }}
                        </span>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pendentes:</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $queueStats['prospecting']['pending'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Processando:</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $queueStats['prospecting']['processing'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Falhas:</span>
                            <span class="text-sm font-semibold text-red-600 dark:text-red-400">{{ $queueStats['prospecting']['failed'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Status Global</h3>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $isGlobalPaused ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' }}">
                            {{ $isGlobalPaused ? 'Pausado' : 'Ativo' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $isGlobalPaused ? 'Todas as filas estão pausadas' : 'Todas as filas estão ativas' }}
                    </p>
                </div>

                <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Ações Rápidas</h3>
                    <div class="space-y-2">
                        <form method="POST" action="{{ route('super-admin.queues.pause') }}" class="inline-block w-full">
                            @csrf
                            <input type="hidden" name="queue_name" value="prospecting">
                            <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 rounded-lg text-sm font-semibold">
                                Pausar Prospecting
                            </button>
                        </form>
                        <form method="POST" action="{{ route('super-admin.queues.resume') }}" class="inline-block w-full">
                            @csrf
                            <input type="hidden" name="queue_name" value="prospecting">
                            <button type="submit" class="w-full px-4 py-2 bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300 rounded-lg text-sm font-semibold">
                                Retomar Prospecting
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Pause History -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Histórico de Pausas</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Fila</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Motivo</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Pausado Por</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Data</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($queuePauses as $pause)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $pause->queue_name ?? 'Global' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $pause->is_paused ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' }}">
                                            {{ $pause->is_paused ? 'Pausada' : 'Ativa' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $pause->reason ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $pause->pausedBy?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $pause->paused_at?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum histórico de pausa encontrado
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

