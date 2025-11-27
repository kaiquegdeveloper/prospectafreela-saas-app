<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header Section -->
            <div class="animate-fade-in">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                            Olá, {{ auth()->user()->name }}! 👋
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Aqui está um resumo das suas prospecções
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <a href="{{ route('prospects.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200 group">
                            <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Nova Prospecção
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-slide-up">
                @php
                    $totalProspects = auth()->user()->prospects()->count();
                    $concluidos = auth()->user()->prospects()->where('status', 'done')->count();
                    $pendentes = auth()->user()->prospects()->where('status', 'pending')->count();
                    $taxaSucesso = $totalProspects > 0 ? round(($concluidos / $totalProspects) * 100, 1) : 0;
                @endphp

                <!-- Total Prospects Card -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-blue-100">Total</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-4xl font-bold text-white">{{ $totalProspects }}</p>
                            <p class="text-sm text-blue-100">Prospects cadastrados</p>
                        </div>
                    </div>
                </div>

                <!-- Concluídos Card -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-neon-lime-200 to-neon-lime-300 dark:from-neon-lime-300 dark:to-neon-lime-400 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-900/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl bg-gray-900/20 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">Concluídos</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-4xl font-bold text-gray-900">{{ $concluidos }}</p>
                            <p class="text-sm text-gray-800">{{ $taxaSucesso }}% de sucesso</p>
                        </div>
                    </div>
                </div>

                <!-- Pendentes Card -->
                <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 dark:from-amber-600 dark:to-orange-600 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl bg-white/20 backdrop-blur-sm">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-amber-100">Pendentes</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-4xl font-bold text-white">{{ $pendentes }}</p>
                            <p class="text-sm text-amber-100">Em processamento</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 animate-slide-up">
                <!-- Progress Chart -->
                <div class="rounded-2xl bg-white dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Progresso Geral</h3>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-700 dark:text-gray-300">Concluídos</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $concluidos }} ({{ $taxaSucesso }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $taxaSucesso }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-700 dark:text-gray-300">Pendentes</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $pendentes }} ({{ $totalProspects > 0 ? round(($pendentes / $totalProspects) * 100, 1) : 0 }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-3 rounded-full transition-all duration-1000 ease-out" style="width: {{ $totalProspects > 0 ? round(($pendentes / $totalProspects) * 100, 1) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="rounded-2xl bg-white dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Ações Rápidas</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('prospects.create') }}" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-4 hover:shadow-lg transition-all duration-200 hover:scale-105">
                            <div class="relative z-10">
                                <svg class="w-8 h-8 text-white mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <p class="text-sm font-semibold text-white">Nova Prospecção</p>
                            </div>
                        </a>
                        <a href="{{ route('prospects.index') }}" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-gray-600 to-gray-700 dark:from-gray-700 dark:to-gray-800 p-4 hover:shadow-lg transition-all duration-200 hover:scale-105">
                            <div class="relative z-10">
                                <svg class="w-8 h-8 text-white mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                <p class="text-sm font-semibold text-white">Ver Todos</p>
                            </div>
                        </a>
                        <a href="{{ route('prospects.export') }}" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-green-500 to-green-600 p-4 hover:shadow-lg transition-all duration-200 hover:scale-105">
                            <div class="relative z-10">
                                <svg class="w-8 h-8 text-white mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-sm font-semibold text-white">Exportar CSV</p>
                            </div>
                        </a>
                        <button @click="$dispatch('open-modal', 'plan-modal')" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-neon-lime-200 to-neon-lime-300 p-4 hover:shadow-lg transition-all duration-200 hover:scale-105">
                            <div class="relative z-10">
                                <svg class="w-8 h-8 text-gray-900 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <p class="text-sm font-semibold text-gray-900">Meu Plano</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Prospects Table -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200/50 dark:border-gray-700/50 shadow-xl overflow-hidden animate-slide-up">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Prospects Recentes</h3>
                        <a href="{{ route('prospects.index') }}" class="text-sm font-medium text-neon-lime-200 hover:text-neon-lime-300 transition-colors flex items-center group">
                            Ver todos
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    @php
                        $recentProspects = auth()->user()->prospects()->orderBy('created_at', 'desc')->limit(5)->get();
                    @endphp
                    @if($recentProspects->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Cidade</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentProspects as $prospect)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('prospects.show', $prospect) }}" class="text-sm font-medium text-gray-900 dark:text-gray-100 hover:text-neon-lime-200 transition-colors">
                                                {{ $prospect->nome }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $prospect->cidade }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($prospect->status === 'done')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-neon-lime-200/20 to-neon-lime-300/20 text-gray-900 dark:text-gray-100 border border-neon-lime-200/30">
                                                    <span class="w-2 h-2 bg-neon-lime-200 rounded-full mr-2"></span>
                                                    Concluído
                                                </span>
                                            @elseif($prospect->status === 'pending')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-amber-500/20 to-orange-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                                                    <span class="w-2 h-2 bg-amber-500 rounded-full mr-2 animate-pulse"></span>
                                                    Pendente
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-red-500/20 to-red-600/20 text-red-700 dark:text-red-300 border border-red-500/30">
                                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                                    Erro
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $prospect->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <a href="{{ route('prospects.show', $prospect) }}" class="text-neon-lime-200 hover:text-neon-lime-300 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">Nenhum prospect ainda.</p>
                            <a href="{{ route('prospects.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg hover:shadow-lg transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Inicie uma prospecção
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
