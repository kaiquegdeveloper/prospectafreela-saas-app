<x-modal name="plan-modal" maxWidth="3xl">
    <div class="p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Meu Plano
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Gerencie sua assinatura e recursos
                </p>
            </div>
            <button @click="$dispatch('close-modal', 'plan-modal')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Current Plan Card -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-neon-lime-200/20 via-neon-lime-300/10 to-transparent dark:from-neon-lime-200/10 dark:via-neon-lime-300/5 dark:to-transparent border-2 border-neon-lime-200/30 dark:border-neon-lime-200/20 p-8 mb-6">
            <div class="absolute top-0 right-0 w-32 h-32 bg-neon-lime-200/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-neon-lime-300/10 rounded-full -ml-12 -mb-12 blur-xl"></div>
            
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-neon-lime-200 to-neon-lime-300 shadow-lg">
                            <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                Plano Atual
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Ativo desde {{ now()->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                    <span class="px-4 py-2 rounded-full bg-neon-lime-200/30 dark:bg-neon-lime-200/20 text-gray-900 dark:text-gray-100 font-semibold text-sm">
                        Gratuito
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 border border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center space-x-2 mb-2">
                            <svg class="w-5 h-5 text-neon-lime-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Prospecções</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Ilimitadas</p>
                    </div>
                    <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 border border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center space-x-2 mb-2">
                            <svg class="w-5 h-5 text-neon-lime-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Exportação</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">CSV</p>
                    </div>
                    <div class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm rounded-xl p-4 border border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center space-x-2 mb-2">
                            <svg class="w-5 h-5 text-neon-lime-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Suporte</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">Básico</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features List -->
        <div class="space-y-3 mb-6">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Recursos Incluídos</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                    <svg class="w-5 h-5 text-neon-lime-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Prospecções ilimitadas</span>
                </div>
                <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                    <svg class="w-5 h-5 text-neon-lime-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Exportação em CSV</span>
                </div>
                <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                    <svg class="w-5 h-5 text-neon-lime-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Suporte básico</span>
                </div>
                <div class="flex items-center space-x-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50">
                    <svg class="w-5 h-5 text-neon-lime-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Dashboard completo</span>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border border-yellow-200 dark:border-yellow-800/50 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200 mb-1">
                        Em Desenvolvimento
                    </h3>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        Funcionalidades de billing e gerenciamento de assinatura serão implementadas em breve. Você poderá gerenciar sua assinatura, escolher entre diferentes planos e visualizar histórico de pagamentos.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-modal>

