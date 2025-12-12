<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                <a href="{{ route('sales-scripts.index') }}" class="hover:text-neon-lime-600 dark:hover:text-neon-lime-400 transition-colors">
                    Scripts de Vendas
                </a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('sales-scripts.category', $script->category) }}" class="hover:text-neon-lime-600 dark:hover:text-neon-lime-400 transition-colors">
                    {{ $script->category->name }}
                </a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-900 dark:text-gray-300">{{ $script->stage_name }}</span>
            </nav>

            <!-- Script Card -->
            <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 shadow-2xl">
                <!-- Header -->
                <div class="p-8 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-neon-lime-50 to-transparent dark:from-neon-lime-900/20">
                    <div class="flex items-start justify-between">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3">
                                <span class="text-3xl">{{ $script->category->icon }}</span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-neon-lime-100 dark:bg-neon-lime-900/50 text-neon-lime-800 dark:text-neon-lime-300">
                                    {{ $script->stage_name }}
                                </span>
                            </div>
                            @if($script->title)
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ $script->title }}
                                </h1>
                            @endif
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ $script->category->name }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-8 space-y-6">
                    <div class="prose prose-lg max-w-none dark:prose-invert">
                        <div class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">
                            {{ $script->content }}
                        </div>
                    </div>

                    @if($script->tips)
                        <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-6">
                            <div class="flex items-start space-x-3">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200 mb-2">
                                        💡 Dica Profissional
                                    </h3>
                                    <p class="text-blue-800 dark:text-blue-300 leading-relaxed">
                                        {{ $script->tips }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button
                            onclick="copyToClipboard('{{ addslashes($script->content) }}')"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-neon-lime-200 to-neon-lime-300 text-gray-900 font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Copiar Script
                        </button>
                        <a
                            href="https://wa.me/?text={{ urlencode($script->content) }}"
                            target="_blank"
                            class="inline-flex items-center px-6 py-3 bg-green-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200"
                        >
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                            Abrir WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Related Scripts -->
            @if($relatedScripts->count() > 0)
                <div class="space-y-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Scripts Relacionados
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($relatedScripts as $related)
                            <a
                                href="{{ route('sales-scripts.show', $related) }}"
                                class="block p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-neon-lime-300 dark:hover:border-neon-lime-300 transition-all duration-200 hover:shadow-lg"
                            >
                                @if($related->title)
                                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">
                                        {{ $related->title }}
                                    </h3>
                                @endif
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ $related->content }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Copiado!';
                button.classList.add('bg-green-500');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-500');
                }, 2000);
            });
        }
    </script>
</x-app-layout>

