<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Meu Plano') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            Sistema de Planos
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400">
                            Em breve você poderá gerenciar seu plano e assinatura aqui.
                        </p>
                    </div>

                    <!-- Placeholder Card -->
                    <div class="max-w-md mx-auto bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-8 border border-blue-200 dark:border-blue-800">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                Plano Atual
                            </h3>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-4">
                                Plano Gratuito
                            </p>
                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-6">
                                <p>✓ Prospecções ilimitadas</p>
                                <p>✓ Exportação em CSV</p>
                                <p>✓ Suporte básico</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                Funcionalidade de billing será implementada em breve
                            </p>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                    Em Desenvolvimento
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    <p>Esta funcionalidade está em desenvolvimento. Em breve você poderá:</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1">
                                        <li>Gerenciar sua assinatura</li>
                                        <li>Escolher entre diferentes planos</li>
                                        <li>Visualizar histórico de pagamentos</li>
                                        <li>Atualizar método de pagamento</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

