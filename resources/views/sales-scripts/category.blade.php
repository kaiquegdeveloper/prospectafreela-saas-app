<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header Section -->
            <div class="space-y-4">
                <a href="{{ route('sales-scripts.index') }}" class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-neon-lime-600 dark:hover:text-neon-lime-400 transition-colors mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Voltar para categorias
                </a>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="text-6xl">
                            {{ $category->icon }}
                        </div>
                        <div>
                            <h1 class="text-4xl sm:text-5xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                                {{ $category->name }}
                            </h1>
                            @if($category->description)
                                <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                                    {{ $category->description }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stages Tabs -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 overflow-x-auto" aria-label="Stages">
                    @foreach($stageNames as $stageKey => $stageName)
                        <button
                            onclick="scrollToStage('{{ $stageKey }}')"
                            class="stage-tab whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300"
                            id="tab-{{ $stageKey }}"
                        >
                            {{ $stageName }}
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-800">
                                {{ $scriptsByStage[$stageKey]->count() }}
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <!-- Scripts by Stage -->
            <div class="space-y-12">
                @foreach($scriptsByStage as $stageKey => $scripts)
                    @if($scripts->count() > 0)
                        <div id="stage-{{ $stageKey }}" class="stage-section scroll-mt-8">
                            <div class="mb-6">
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                                    <span class="mr-3">{{ $stageNames[$stageKey] }}</span>
                                    <span class="text-lg font-normal text-gray-500 dark:text-gray-400">
                                        ({{ $scripts->count() }} scripts)
                                    </span>
                                </h2>
                                <p class="mt-2 text-gray-600 dark:text-gray-400">
                                    @if($stageKey === 'introducao')
                                        Use estes scripts como primeira mensagem para abrir conversas e criar conexão inicial.
                                    @elseif($stageKey === 'qualificacao')
                                        Perguntas estratégicas para entender melhor o cliente e suas necessidades.
                                    @elseif($stageKey === 'levar_call')
                                        Scripts para agendar uma conversa mais profunda e apresentar sua proposta.
                                    @elseif($stageKey === 'quebra_objecao')
                                        Respostas profissionais para as objeções mais comuns dos clientes.
                                    @else
                                        Técnicas para fechar o negócio e definir os próximos passos.
                                    @endif
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($scripts as $script)
                                    <div class="group relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 hover:border-neon-lime-300 dark:hover:border-neon-lime-300 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-1">
                                        <div class="p-6 space-y-4">
                                            @if($script->title)
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                    {{ $script->title }}
                                                </h3>
                                            @endif
                                            
                                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed line-clamp-4">
                                                {{ $script->content }}
                                            </p>

                                            @if($script->tips)
                                                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                                                    <div class="flex items-start space-x-2">
                                                        <svg class="w-5 h-5 text-neon-lime-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                                                            {{ $script->tips }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif

                                            <a 
                                                href="{{ route('sales-scripts.show', $script) }}"
                                                class="inline-flex items-center text-neon-lime-600 dark:text-neon-lime-400 font-semibold text-sm group-hover:translate-x-2 transition-transform duration-300"
                                            >
                                                Ver script completo
                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <script>
        function scrollToStage(stageKey) {
            const element = document.getElementById('stage-' + stageKey);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                // Update active tab
                document.querySelectorAll('.stage-tab').forEach(tab => {
                    tab.classList.remove('border-neon-lime-500', 'text-neon-lime-600', 'dark:text-neon-lime-400');
                    tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                });
                
                const activeTab = document.getElementById('tab-' + stageKey);
                if (activeTab) {
                    activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                    activeTab.classList.add('border-neon-lime-500', 'text-neon-lime-600', 'dark:text-neon-lime-400');
                }
            }
        }

        // Highlight active tab on scroll
        window.addEventListener('scroll', () => {
            const stages = ['introducao', 'qualificacao', 'levar_call', 'quebra_objecao', 'fechamento'];
            const scrollPosition = window.scrollY + 100;

            stages.forEach(stageKey => {
                const element = document.getElementById('stage-' + stageKey);
                if (element) {
                    const offsetTop = element.offsetTop;
                    const offsetBottom = offsetTop + element.offsetHeight;

                    if (scrollPosition >= offsetTop && scrollPosition < offsetBottom) {
                        document.querySelectorAll('.stage-tab').forEach(tab => {
                            tab.classList.remove('border-neon-lime-500', 'text-neon-lime-600', 'dark:text-neon-lime-400');
                            tab.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                        });
                        
                        const activeTab = document.getElementById('tab-' + stageKey);
                        if (activeTab) {
                            activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                            activeTab.classList.add('border-neon-lime-500', 'text-neon-lime-600', 'dark:text-neon-lime-400');
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>

