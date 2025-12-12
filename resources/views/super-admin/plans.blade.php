<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                        Gestão de Planos
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Gerencie os planos e suas quotas</p>
                </div>
                <a href="{{ route('super-admin.dashboard') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Voltar
                </a>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
                    <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4">
                    <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Create Plan Form -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Criar Novo Plano</h2>
                <form method="POST" action="{{ route('super-admin.plans.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nome do Plano</label>
                        <input type="text" name="name" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="Ex: Plano Básico">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quota Mensal</label>
                        <input type="number" name="monthly_prospect_quota" required min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quota Diária</label>
                        <input type="number" name="daily_prospect_quota" required min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="60">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preço (R$)</label>
                        <input type="number" name="price" step="0.01" min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900" placeholder="0.00">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="is_active" checked class="rounded border-gray-300 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ativo</span>
                        </label>
                        <button type="submit" class="ml-4 px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg hover:shadow-lg transition">
                            Criar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Plans Table -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Nome</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Quota Mensal</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Quota Diária</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Preço</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Usuários</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($plans as $plan)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $plan->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($plan->monthly_prospect_quota) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($plan->daily_prospect_quota) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">R$ {{ number_format($plan->price, 2, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($plan->is_active)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                Ativo
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                                Inativo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $plan->users()->count() }} usuário(s)</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button 
                                            onclick="openEditModal({{ $plan->id }}, '{{ $plan->name }}', {{ $plan->monthly_prospect_quota }}, {{ $plan->daily_prospect_quota }}, {{ $plan->price }}, {{ $plan->is_active ? 'true' : 'false' }})"
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                        >
                                            Editar
                                        </button>
                                        <form method="POST" action="{{ route('super-admin.plans.delete', $plan) }}" class="inline" onsubmit="return confirm('Tem certeza que deseja deletar este plano?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                Deletar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Nenhum plano cadastrado ainda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($plans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $plans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Editar Plano</h3>
                <form method="POST" id="editForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nome do Plano</label>
                        <input type="text" name="name" id="edit_name" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quota Mensal</label>
                        <input type="number" name="monthly_prospect_quota" id="edit_monthly_quota" required min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quota Diária</label>
                        <input type="number" name="daily_prospect_quota" id="edit_daily_quota" required min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preço (R$)</label>
                        <input type="number" name="price" id="edit_price" step="0.01" min="0" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                    </div>
                    <div>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="is_active" id="edit_is_active" class="rounded border-gray-300 dark:border-gray-700">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ativo</span>
                        </label>
                    </div>
                    <div class="flex space-x-3 mt-4">
                        <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg hover:shadow-lg transition">
                            Salvar
                        </button>
                        <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, name, monthlyQuota, dailyQuota, price, isActive) {
            document.getElementById('editForm').action = '{{ route("super-admin.plans.update", ":id") }}'.replace(':id', id);
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_monthly_quota').value = monthlyQuota;
            document.getElementById('edit_daily_quota').value = dailyQuota;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_is_active').checked = isActive === true || isActive === 'true';
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</x-app-layout>

