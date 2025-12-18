<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                        Gestão de Usuários
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Gerencie todos os usuários do sistema</p>
                </div>
                <a href="{{ route('super-admin.dashboard') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Voltar
                </a>
            </div>

            <!-- Filters -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou email..." class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    <select name="role" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Todas as roles</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Usuário</option>
                        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    <select name="status" class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Todos os status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativo</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg">Filtrar</button>
                </form>
            </div>

            <!-- Users Table -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Usuário</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Estatísticas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Plano / Quotas</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-neon-lime-200 to-neon-lime-300 flex items-center justify-center text-gray-900 font-semibold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'super_admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' }}">
                                            {{ $user->role === 'super_admin' ? 'Super Admin' : 'Usuário' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200' }}">
                                            {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        <div>Prospects: {{ $user->prospects_count }}</div>
                                        <div>Pesquisas: {{ $user->searches_count }}</div>
                                        <div>Pagamentos: {{ $user->payments_count }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            <div class="text-xs">
                                                <strong>Plano:</strong> {{ $user->plan?->name ?? 'Sem plano' }}
                                            </div>
                                            <div class="text-xs">
                                                <strong>Quota Mensal:</strong> 
                                                {{ $user->monthly_quota_custom ?? ($user->plan?->monthly_prospect_quota ?? 'N/A') }}
                                            </div>
                                            <div class="text-xs">
                                                <strong>Quota Diária:</strong> 
                                                {{ $user->daily_quota_custom ?? ($user->plan?->daily_prospect_quota ?? 'N/A') }}
                                            </div>
                                            <div class="text-xs">
                                                <strong>Max API Fetches:</strong> 
                                                {{ $user->max_api_fetches_custom ?? '20 (padrão)' }}
                                            </div>
                                            <button 
                                                onclick="openUserModal({{ $user->id }}, '{{ $user->name }}', {{ $user->plan_id ?? 'null' }}, {{ $user->monthly_quota_custom ?? 'null' }}, {{ $user->daily_quota_custom ?? 'null' }}, {{ $user->max_api_fetches_custom ?? 'null' }})"
                                                class="mt-1 px-2 py-1 text-xs rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300"
                                            >
                                                Editar
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            <form method="POST" action="{{ route('super-admin.users.toggle-status', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="w-full px-3 py-1 text-xs rounded-lg {{ $user->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300' }}">
                                                    {{ $user->is_active ? 'Desabilitar' : 'Habilitar' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('super-admin.users.refund', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="w-full px-3 py-1 text-xs rounded-lg {{ $user->refunded_at ? 'bg-orange-100 text-orange-700 hover:bg-orange-200 dark:bg-orange-900/30 dark:text-orange-300' : 'bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300' }}">
                                                    {{ $user->refunded_at ? 'Remover Reembolso' : 'Marcar Reembolso' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('super-admin.users.impersonate', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="w-full px-3 py-1 text-xs rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300">
                                                    Impersonar
                                                </button>
                                            </form>
                                            <a href="{{ route('super-admin.users.login-history', $user) }}" class="px-3 py-1 text-xs rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200 dark:bg-purple-900/30 dark:text-purple-300 text-center">
                                                Histórico Login
                                            </a>
                                            <a href="{{ route('super-admin.users.modules', $user) }}" class="px-3 py-1 text-xs rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-300 text-center">
                                                Módulos
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum usuário encontrado
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar plano e quotas -->
    <div id="userModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Editar Plano e Quotas</h3>
                <form id="userPlanForm" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Plano</label>
                            <select name="plan_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Sem plano</option>
                                @foreach(\App\Models\Plan::all() as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quota Mensal Customizada</label>
                            <input type="number" name="monthly_quota_custom" min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Deixe vazio para usar do plano">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quota Diária Customizada</label>
                            <input type="number" name="daily_quota_custom" min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Deixe vazio para usar do plano">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Max API Fetches Customizado</label>
                            <input type="number" name="max_api_fetches_custom" min="1" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Deixe vazio para usar padrão (20)">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Máximo de resultados que o usuário pode buscar por pesquisa</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-6">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg">Salvar</button>
                        <button type="button" onclick="closeUserModal()" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold rounded-lg">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openUserModal(userId, userName, planId, monthlyQuota, dailyQuota, maxApiFetches) {
            document.getElementById('userPlanForm').action = `/super-admin/users/${userId}/plan`;
            document.querySelector('select[name="plan_id"]').value = planId || '';
            document.querySelector('input[name="monthly_quota_custom"]').value = monthlyQuota || '';
            document.querySelector('input[name="daily_quota_custom"]').value = dailyQuota || '';
            document.querySelector('input[name="max_api_fetches_custom"]').value = maxApiFetches || '';
            document.getElementById('userModal').classList.remove('hidden');
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
        }
    </script>
</x-app-layout>

