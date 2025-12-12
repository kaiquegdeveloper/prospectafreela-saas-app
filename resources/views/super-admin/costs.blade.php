<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                        Controle de Custos
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
                </div>
                <div class="mt-4 sm:mt-0 flex gap-2">
                    <form method="GET" class="flex gap-2">
                        <select name="period" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                            <option value="7" {{ $period == 7 ? 'selected' : '' }}>7 dias</option>
                            <option value="30" {{ $period == 30 ? 'selected' : '' }}>30 dias</option>
                            <option value="90" {{ $period == 90 ? 'selected' : '' }}>90 dias</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg">Filtrar</button>
                    </form>
                    <a href="{{ route('super-admin.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        ← Voltar
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 p-6 shadow-xl">
                    <p class="text-sm text-blue-100 mb-2">Custo Total API</p>
                    <p class="text-3xl font-bold text-white">${{ number_format($totalApiCost, 2, '.', ',') }}</p>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 p-6 shadow-xl">
                    <p class="text-sm text-green-100 mb-2">Total de Chamadas</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($totalApiCalls, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 p-6 shadow-xl">
                    <p class="text-sm text-purple-100 mb-2">Custo Médio por Chamada</p>
                    <p class="text-3xl font-bold text-white">${{ $totalApiCalls > 0 ? number_format($totalApiCost / $totalApiCalls, 4, '.', ',') : '0.0000' }}</p>
                </div>
            </div>

            <!-- Costs by API -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Custos por API</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">API</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Chamadas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Custo Total</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Custo Médio</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($apiCosts as $cost)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cost->api_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ number_format($cost->total_calls, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">${{ number_format($cost->total_cost, 2, '.', ',') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">${{ $cost->total_calls > 0 ? number_format($cost->total_cost / $cost->total_calls, 4, '.', ',') : '0.0000' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Nenhum custo encontrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Users by Cost -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Top 20 Usuários por Custo</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Usuário</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Chamadas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Custo Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($costsByUser as $cost)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $cost->user->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ number_format($cost->total_calls, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">${{ number_format($cost->total_cost, 2, '.', ',') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Nenhum dado encontrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

