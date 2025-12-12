<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header Section -->
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                            Super Admin Dashboard 🎯
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Visão geral completa do sistema
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
                        <a href="{{ route('super-admin.users') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Usuários
                        </a>
                        <a href="{{ route('super-admin.plans') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Planos
                        </a>
                        <a href="{{ route('super-admin.saas-dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            SaaS Dashboard
                        </a>
                        <a href="{{ route('super-admin.settings') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Configurações
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Users Ativos Logados -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-4xl font-bold text-white">{{ $activeUsers }}</p>
                            <p class="text-sm text-blue-100">Usuários Ativos (24h)</p>
                        </div>
                    </div>
                </div>

                <!-- Users Totais -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-4xl font-bold text-white">{{ $totalUsers }}</p>
                            <p class="text-sm text-purple-100">Total de Usuários</p>
                        </div>
                    </div>
                </div>

                <!-- Fat. Total -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-4xl font-bold text-white">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</p>
                            <p class="text-sm text-green-100">Faturamento Total</p>
                        </div>
                    </div>
                </div>

                <!-- MRR -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 dark:from-amber-600 dark:to-orange-600 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-4xl font-bold text-white">R$ {{ number_format($mrr, 2, ',', '.') }}</p>
                            <p class="text-sm text-amber-100">MRR (Mensal)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Stats Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Usos da API -->
                <div class="rounded-2xl bg-white dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Uso da API (30 dias)</h3>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($apiUsage30Days, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Chamadas realizadas</p>
                        </div>
                    </div>
                </div>

                <!-- Custo Estimado da API -->
                <div class="rounded-2xl bg-white dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Custo Estimado (30 dias)</h3>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">${{ number_format($apiCost30Days, 2, '.', ',') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">USD estimado</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detecção de Anomalias -->
            @if(count($anomalies) > 0)
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Detecção de Anomalias
                    </h3>
                </div>
                <div class="space-y-3">
                    @foreach($anomalies as $anomaly)
                    <div class="p-4 rounded-xl bg-{{ $anomaly['severity'] === 'high' ? 'red' : 'amber' }}-50 dark:bg-{{ $anomaly['severity'] === 'high' ? 'red' : 'amber' }}-900/20 border border-{{ $anomaly['severity'] === 'high' ? 'red' : 'amber' }}-200 dark:border-{{ $anomaly['severity'] === 'high' ? 'red' : 'amber' }}-800">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-{{ $anomaly['severity'] === 'high' ? 'red' : 'amber' }}-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                            <div>
                                <p class="text-sm font-semibold text-{{ $anomaly['severity'] === 'high' ? 'red' : 'amber' }}-900 dark:text-{{ $anomaly['severity'] === 'high' ? 'red' : 'amber' }}-200">
                                    {{ ucfirst($anomaly['severity']) }} - {{ ucfirst(str_replace('_', ' ', $anomaly['type'])) }}
                                </p>
                                <p class="text-sm text-{{ $anomaly['severity'] === 'high' ? 'red' : 'amber' }}-700 dark:text-{{ $anomaly['severity'] === 'high' ? 'red' : 'amber' }}-300 mt-1">
                                    {{ $anomaly['message'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Top Users Usage -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Top Usuários por Uso (7 dias)</h3>
                        <a href="{{ route('super-admin.searches') }}" class="text-sm font-medium text-neon-lime-200 hover:text-neon-lime-300 transition-colors flex items-center group">
                            Ver todos
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    @if($userUsageStats->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Usuário</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Pesquisas</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($userUsageStats as $stat)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-neon-lime-200 to-neon-lime-300 flex items-center justify-center text-gray-900 font-semibold">
                                                    {{ strtoupper(substr($stat->user->name, 0, 1)) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $stat->user->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $stat->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                                {{ $stat->searches_count }} pesquisas
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-12 text-center">
                            <p class="text-gray-500 dark:text-gray-400">Nenhum dado disponível</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('super-admin.reports.users-not-logged-in') }}" class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-xl bg-red-100 dark:bg-red-900/30">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Usuários Não Logados</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ver usuários inativos</p>
                </a>

                <a href="{{ route('super-admin.reports.users-logged-in-today') }}" class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-xl bg-green-100 dark:bg-green-900/30">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Logados Hoje</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Usuários ativos hoje</p>
                </a>

                <a href="{{ route('super-admin.queues') }}" class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Monitorar Filas</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Controle de filas</p>
                </a>

                <a href="{{ route('super-admin.costs') }}" class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-xl bg-amber-100 dark:bg-amber-900/30">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Controle de Custos</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Análise de custos</p>
                </a>

                <a href="{{ route('super-admin.reports.user-login-counts') }}" class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-xl bg-purple-100 dark:bg-purple-900/30">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Contagem de Logins</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Vezes que user logou</p>
                </a>

                <a href="{{ route('super-admin.logs') }}" class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6 hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-xl bg-gray-100 dark:bg-gray-700">
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Logs do Laravel</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Visualizar logs</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

