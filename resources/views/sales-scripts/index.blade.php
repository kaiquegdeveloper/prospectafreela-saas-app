<x-app-layout>
    <div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Header Section -->
            <div class="text-center space-y-4">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-neon-lime-200 to-neon-lime-300 shadow-lg mb-4">
                    <svg class="w-10 h-10 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl sm:text-5xl font-bold bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 dark:from-white dark:via-gray-200 dark:to-white bg-clip-text text-transparent">
                    Scripts de Vendas 📝
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Descubra scripts profissionais para cada etapa do seu processo de vendas. Escolha sua modalidade e comece a vender mais!
                </p>
            </div>

            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('sales-scripts.search') }}" method="GET" class="relative">
                    <div class="relative">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Buscar scripts..."
                            class="w-full pl-12 pr-4 py-4 text-lg rounded-2xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-neon-lime-300 focus:ring-2 focus:ring-neon-lime-200 focus:outline-none transition-all duration-200 shadow-lg"
                        />
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </form>
            </div>

            <!-- Categories Grid -->
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-12">
                    @foreach($categories as $category)
                        <a 
                            href="{{ route('sales-scripts.category', $category) }}"
                            class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 hover:border-neon-lime-300 dark:hover:border-neon-lime-300 transition-all duration-300 shadow-lg hover:shadow-2xl transform hover:-translate-y-2"
                        >
                            <!-- Gradient Overlay on Hover -->
                            <div class="absolute inset-0 bg-gradient-to-br from-neon-lime-50/0 to-neon-lime-100/0 group-hover:from-neon-lime-50/50 group-hover:to-neon-lime-100/30 dark:group-hover:from-neon-lime-900/10 dark:group-hover:to-neon-lime-800/10 transition-all duration-300"></div>
                            
                            <div class="relative p-6 space-y-4">
                                <!-- Icon -->
                                <div class="flex items-center justify-between">
                                    <div class="text-5xl transform group-hover:scale-110 transition-transform duration-300">
                                        {{ $category->icon }}
                                    </div>
                                    <div class="flex items-center space-x-2 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700">
                                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                            {{ $category->scripts_count ?? 0 }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Title -->
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-neon-lime-600 dark:group-hover:text-neon-lime-400 transition-colors">
                                        {{ $category->name }}
                                    </h3>
                                </div>

                                <!-- Description -->
                                @if($category->description)
                                    <p class="text-gray-600 dark:text-gray-400 line-clamp-2">
                                        {{ $category->description }}
                                    </p>
                                @endif

                                <!-- Arrow Indicator -->
                                <div class="flex items-center text-neon-lime-600 dark:text-neon-lime-400 font-semibold group-hover:translate-x-2 transition-transform duration-300">
                                    <span>Explorar scripts</span>
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Nenhuma categoria de scripts disponível no momento.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

