<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                        Módulos - {{ $user->name }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Controle de acesso aos módulos do sistema</p>
                </div>
                <a href="{{ route('super-admin.users') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Voltar
                </a>
            </div>

            <!-- Modules Form -->
            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <form method="POST" action="{{ route('super-admin.users.update-modules', $user) }}">
                    @csrf
                    <div class="space-y-4">
                        @foreach($availableModules as $moduleKey => $moduleName)
                            @php
                                $userModule = $userModules->get($moduleKey);
                                $isEnabled = $userModule ? $userModule->is_enabled : true;
                            @endphp
                            <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-neon-lime-200 to-neon-lime-300 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $moduleName }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Módulo: {{ $moduleKey }}</p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="modules[{{ $moduleKey }}]" 
                                        value="1"
                                        {{ $isEnabled ? 'checked' : '' }}
                                        class="sr-only peer"
                                    >
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-neon-lime-300 dark:peer-focus:ring-neon-lime-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-neon-lime-200"></div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-lg hover:shadow-lg transition">
                            Salvar Alterações
                        </button>
                        <a href="{{ route('super-admin.users') }}" class="px-6 py-3 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-gray-100 font-semibold rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

