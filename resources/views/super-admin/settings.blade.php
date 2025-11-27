<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                        Configurações Globais
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Configure limites e parâmetros do sistema</p>
                </div>
                <a href="{{ route('super-admin.dashboard') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Voltar
                </a>
            </div>

            <!-- Settings Form -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <form method="POST" action="{{ route('super-admin.settings.update') }}">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                Limite de Chamadas da API por Minuto
                            </label>
                            <input 
                                type="number" 
                                name="api_rate_limit_per_minute" 
                                value="{{ isset($settings['api_rate_limit_per_minute']) ? $settings['api_rate_limit_per_minute']->value : 60 }}" 
                                min="1" 
                                max="1000" 
                                required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                            >
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Define quantas chamadas à API do Google Maps Places são permitidas por minuto.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                Limite Padrão de Resultados por Busca
                            </label>
                            <input 
                                type="number" 
                                name="default_results_limit" 
                                value="{{ isset($settings['default_results_limit']) ? $settings['default_results_limit']->value : 50 }}" 
                                min="1" 
                                max="500" 
                                required
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-gray-900 dark:text-gray-100"
                            >
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Define quantos resultados serão retornados por padrão em cada busca. O super admin pode personalizar este limite por usuário.
                            </p>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg hover:shadow-lg transition">
                                Salvar Configurações
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            @if(session('success'))
                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                    <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

