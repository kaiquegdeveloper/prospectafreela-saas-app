<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                        Dashboard SaaS Avançado
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Métricas comparativas dos últimos 30 dias vs período anterior</p>
                </div>
                <a href="{{ route('super-admin.dashboard') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Voltar
                </a>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div class="rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 p-6 shadow-xl">
                    <p class="text-sm text-blue-100 mb-2">Ganho de Usuários</p>
                    <p class="text-3xl font-bold text-white">{{ $currentUsers }}</p>
                    <p class="text-sm text-blue-100 mt-2">
                        <span class="{{ $userGrowth >= 0 ? 'text-green-200' : 'text-red-200' }}">
                            {{ $userGrowth >= 0 ? '+' : '' }}{{ number_format($userGrowth, 1) }}%
                        </span>
                        vs período anterior
                    </p>
                </div>

                <div class="rounded-2xl bg-gradient-to-br from-red-500 to-red-600 dark:from-red-600 dark:to-red-700 p-6 shadow-xl">
                    <p class="text-sm text-red-100 mb-2">Perda de Usuários</p>
                    <p class="text-3xl font-bold text-white">{{ $currentLostUsers }}</p>
                    <p class="text-sm text-red-100 mt-2">
                        <span class="{{ $userLoss <= 0 ? 'text-green-200' : 'text-red-200' }}">
                            {{ $userLoss >= 0 ? '+' : '' }}{{ number_format($userLoss, 1) }}%
                        </span>
                        vs período anterior
                    </p>
                </div>

                <div class="rounded-2xl bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 p-6 shadow-xl">
                    <p class="text-sm text-green-100 mb-2">MRR</p>
                    <p class="text-3xl font-bold text-white">R$ {{ number_format($currentMRR, 2, ',', '.') }}</p>
                    <p class="text-sm text-green-100 mt-2">
                        <span class="{{ $mrrGrowth >= 0 ? 'text-green-200' : 'text-red-200' }}">
                            {{ $mrrGrowth >= 0 ? '+' : '' }}{{ number_format($mrrGrowth, 1) }}%
                        </span>
                        vs período anterior
                    </p>
                </div>

                <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 dark:from-amber-600 dark:to-orange-600 p-6 shadow-xl">
                    <p class="text-sm text-amber-100 mb-2">Churn Rate</p>
                    <p class="text-3xl font-bold text-white">{{ number_format($churnRate, 2) }}%</p>
                    <p class="text-sm text-amber-100 mt-2">Taxa de cancelamento</p>
                </div>

                <div class="rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 p-6 shadow-xl">
                    <p class="text-sm text-purple-100 mb-2">LTV</p>
                    <p class="text-3xl font-bold text-white">R$ {{ number_format($ltv, 2, ',', '.') }}</p>
                    <p class="text-sm text-purple-100 mt-2">Lifetime Value</p>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Novos Usuários (30 dias)</h3>
                    <div class="h-64 flex items-end justify-between gap-1">
                        @foreach($dailyMetrics as $metric)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-blue-500 rounded-t" style="height: {{ $metric['new_users'] > 0 ? max(10, ($metric['new_users'] / max(array_column($dailyMetrics, 'new_users'))) * 100) : 0 }}%"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 mt-2">{{ $metric['new_users'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Receita Diária (30 dias)</h3>
                    <div class="h-64 flex items-end justify-between gap-1">
                        @foreach($dailyMetrics as $metric)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-green-500 rounded-t" style="height: {{ $metric['revenue'] > 0 ? max(10, ($metric['revenue'] / max(array_column($dailyMetrics, 'revenue'))) * 100) : 0 }}%"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 mt-2">R$ {{ number_format($metric['revenue'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

