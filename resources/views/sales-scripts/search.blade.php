<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header -->
            <div class="space-y-4">
                <a href="{{ route('sales-scripts.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-neon-lime-600 dark:hover:text-neon-lime-400 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Voltar para categorias
                </a>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">
                    Resultados da busca: "{{ $query }}"
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $scripts->total() }} script(s) encontrado(s)
                </p>
            </div>

            <!-- Results -->
            @if($scripts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($scripts as $script)
                        <a
                            href="{{ route('sales-scripts.show', $script) }}"
                            class="block group relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 hover:border-neon-lime-300 dark:hover:border-neon-lime-300 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-1"
                        >
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl">{{ $script->category->icon }}</span>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                        {{ $script->stage_name }}
                                    </span>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                        {{ $script->category->name }}
                                    </h3>
                                    @if($script->title)
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ $script->title }}
                                        </p>
                                    @endif
                                    <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3">
                                        {{ $script->content }}
                                    </p>
                                </div>

                                <div class="flex items-center text-neon-lime-600 dark:text-neon-lime-400 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300">
                                    <span>Ver script completo</span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $scripts->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">
                        Nenhum script encontrado para "{{ $query }}"
                    </p>
                    <a href="{{ route('sales-scripts.index') }}" class="mt-4 inline-flex items-center text-neon-lime-600 dark:text-neon-lime-400 font-semibold">
                        Ver todas as categorias
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

