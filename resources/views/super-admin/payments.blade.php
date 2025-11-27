<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                        Gestão de Pagamentos
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Registre e gerencie pagamentos</p>
                </div>
                <a href="{{ route('super-admin.dashboard') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Voltar
                </a>
            </div>

            <!-- Add Payment Form -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Adicionar Novo Pagamento</h3>
                <form method="POST" action="{{ route('super-admin.payments.store') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @csrf
                    <select name="user_id" required class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Selecione o usuário</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="amount" step="0.01" min="0" required placeholder="Valor (R$)" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <select name="type" required class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="monthly">Mensal</option>
                        <option value="one_time">Pontual</option>
                    </select>
                    <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg">Adicionar</button>
                </form>
            </div>

            <!-- Filters -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <select name="user_id" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Todos os usuários</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <select name="type" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Todos os tipos</option>
                        <option value="monthly" {{ request('type') === 'monthly' ? 'selected' : '' }}>Mensal</option>
                        <option value="one_time" {{ request('type') === 'one_time' ? 'selected' : '' }}>Pontual</option>
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Data inicial" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg">Filtrar</button>
                </form>
            </div>

            <!-- Payments Table -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Usuário</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Valor</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Tipo</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Data</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Notas</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($payments as $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $payment->user->name }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $payment->type === 'monthly' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-200' }}">
                                            {{ $payment->type === 'monthly' ? 'Mensal' : 'Pontual' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $payment->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum pagamento encontrado
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

