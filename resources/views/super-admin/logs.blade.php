<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                        Logs do Laravel
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Visualize os logs da aplicação</p>
                </div>
                <a href="{{ route('super-admin.dashboard') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Voltar
                </a>
            </div>

            <div class="rounded-2xl bg-white dark:bg-gray-800/50 border border-gray-200/50 dark:border-gray-700/50 shadow-xl p-6">
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-4">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong>Nota:</strong> O Laravel Log Viewer será integrado aqui. Para instalar, execute: 
                        <code class="bg-yellow-100 dark:bg-yellow-900/50 px-2 py-1 rounded">./vendor/bin/sail composer require rap2hpoutre/laravel-log-viewer</code>
                    </p>
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    Após a instalação, os logs estarão disponíveis nesta página.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>

