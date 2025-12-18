<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Meu Plano') }}
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $plan = $user->plan;
        $monthlyQuota = $user->getEffectiveMonthlyQuota();
        $dailyQuota = $user->getEffectiveDailyQuota();
        $planPrice = $plan?->price ?? 0;
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                Meu Plano
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400">Resumo do seu plano atual</p>
                        </div>
                        <a href="https://wa.me/5511978310358?text=Quero%20upgrade%20no%20plano" target="_blank" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg shadow hover:shadow-lg transition">
                            Falar sobre upgrade
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 border border-blue-200 dark:border-blue-800 p-6">
                            <p class="text-sm text-blue-700 dark:text-blue-200 font-semibold">Plano</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $plan?->name ?? 'Sem plano' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Valor: R$ {{ number_format($planPrice, 2, ',', '.') }}</p>
                        </div>
                        <div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Quotas</p>
                            <div class="mt-2 space-y-1">
                                <p class="text-lg text-gray-900 dark:text-gray-100"><strong>{{ $monthlyQuota }}</strong> prospecções mensais</p>
                                <p class="text-lg text-gray-900 dark:text-gray-100"><strong>{{ $dailyQuota }}</strong> prospecções diárias</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Observação</p>
                        <p class="text-gray-800 dark:text-gray-200 mt-2">
                            O valor do plano é cobrado uma única vez conforme acordado. Para upgrades ou ajustes, fale diretamente com nosso time pelo WhatsApp.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

