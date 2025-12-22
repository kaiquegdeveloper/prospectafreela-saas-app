@props(['quotaData', 'user'])

@php
    $dailyExceeded = $quotaData['daily']['exceeded'] ?? false;
    $monthlyExceeded = $quotaData['monthly']['exceeded'] ?? false;
    $dailyReset = $quotaData['daily']['reset_at'] ?? null;
    $monthlyReset = $quotaData['monthly']['reset_at'] ?? null;
    $supportWhatsApp = \App\Models\AppSetting::get('support_whatsapp', '');
    $supportEmail = \App\Models\AppSetting::get('support_email', '');
@endphp

<div id="quotaExceededModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeQuotaModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-white/20 backdrop-blur-sm mb-4">
                    <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2" id="modal-title">
                    Ops, seus créditos acabaram!
                </h3>
                <p class="text-red-100 text-sm">
                    Você atingiu o limite da sua cota de prospecções
                </p>
            </div>

            <!-- Body -->
            <div class="bg-white dark:bg-gray-800 px-6 py-6">
                <!-- Tempo até reinício -->
                <div class="mb-6 space-y-4">
                    @if($dailyExceeded && $dailyReset)
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-1">
                                        Créditos diários reiniciam em:
                                    </h4>
                                    <p class="text-lg font-bold text-blue-600 dark:text-blue-400" id="daily-reset-timer">
                                        {{ $dailyReset['hours'] }}h {{ $dailyReset['minutes'] }}min
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($monthlyExceeded && $monthlyReset)
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-4 border border-indigo-200 dark:border-indigo-800">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-indigo-900 dark:text-indigo-200 mb-1">
                                        Créditos mensais reiniciam em:
                                    </h4>
                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400" id="monthly-reset-timer">
                                        {{ $monthlyReset['days'] }}d {{ $monthlyReset['hours'] }}h {{ $monthlyReset['minutes'] }}min
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Opções de Conversão -->
                <div class="space-y-4">
                    @if(!$user->free_searches_used)
                        <!-- Ganhar 30 buscas gratuitas -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-5 border-2 border-green-300 dark:border-green-700">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-green-500 text-white">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                                        Não fique sem buscar clientes!
                                    </h4>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                        Ganhe <strong class="text-green-600 dark:text-green-400">30 buscas gratuitas</strong> agora mesmo! 
                                        <span class="text-xs text-gray-500 dark:text-gray-400">(Limitado a 1 vez)</span>
                                    </p>
                                    <form action="{{ route('quota.activate-free-searches') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Ganhar 30 Buscas Gratuitas
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Entre em contato -->
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl p-5 border-2 border-indigo-300 dark:border-indigo-700">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-12 w-12 rounded-full bg-indigo-500 text-white">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    Entre em contato e garanta um upgrade
                                </h4>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                    Fale com nosso suporte e descubra planos com mais créditos para você continuar prospectando sem limites!
                                </p>
                                @if($supportWhatsApp)
                                    @php
                                        $supportMessage = "Olá! Meu email é {$user->email} e quero mais créditos para buscar clientes.";
                                        $waLink = "https://wa.me/" . preg_replace('/\D/', '', $supportWhatsApp) . "?text=" . urlencode($supportMessage);
                                    @endphp
                                    <a href="{{ $waLink }}" 
                                       target="_blank"
                                       class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 mb-3">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                        </svg>
                                        Falar no WhatsApp
                                    </a>
                                @endif
                                @if($supportEmail)
                                    <a href="mailto:{{ $supportEmail }}?subject=Quero mais créditos&body=Olá! Meu email é {{ $user->email }} e quero mais créditos para buscar clientes." 
                                       class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Enviar E-mail
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" 
                            onclick="closeQuotaModal()"
                            class="w-full text-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                        Não, quero continuar sem buscar clientes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openQuotaModal() {
        const modal = document.getElementById('quotaExceededModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            startTimers();
        }
    }

    function closeQuotaModal() {
        const modal = document.getElementById('quotaExceededModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }

    function startTimers() {
        @if($dailyExceeded && $dailyReset)
            const dailyResetTime = new Date('{{ $dailyReset['timestamp'] }}').getTime();
            updateDailyTimer(dailyResetTime);
            setInterval(() => updateDailyTimer(dailyResetTime), 60000); // Atualiza a cada minuto
        @endif

        @if($monthlyExceeded && $monthlyReset)
            const monthlyResetTime = new Date('{{ $monthlyReset['timestamp'] }}').getTime();
            updateMonthlyTimer(monthlyResetTime);
            setInterval(() => updateMonthlyTimer(monthlyResetTime), 60000); // Atualiza a cada minuto
        @endif
    }

    function updateDailyTimer(resetTime) {
        const now = new Date().getTime();
        const distance = resetTime - now;

        if (distance < 0) {
            document.getElementById('daily-reset-timer').textContent = 'Reiniciado!';
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

        document.getElementById('daily-reset-timer').textContent = `${hours}h ${minutes}min`;
    }

    function updateMonthlyTimer(resetTime) {
        const now = new Date().getTime();
        const distance = resetTime - now;

        if (distance < 0) {
            document.getElementById('monthly-reset-timer').textContent = 'Reiniciado!';
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

        document.getElementById('monthly-reset-timer').textContent = `${days}d ${hours}h ${minutes}min`;
    }

    // Fecha modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('quotaExceededModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeQuotaModal();
            }
        }
    });

    // Verifica se deve abrir o modal automaticamente
    @if($dailyExceeded || $monthlyExceeded)
        document.addEventListener('DOMContentLoaded', function() {
            // Aguarda um pouco para melhor UX
            setTimeout(() => {
                openQuotaModal();
            }, 500);
        });
    @endif
</script>
@endpush

