@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium leading-5 bg-gradient-to-r from-neon-lime-200/20 to-neon-lime-300/20 dark:from-neon-lime-200/10 dark:to-neon-lime-300/10 text-gray-900 dark:text-gray-100 border border-neon-lime-200/30 dark:border-neon-lime-200/20 shadow-sm transition-all duration-200 hover:scale-105'
            : 'inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition-all duration-200 hover:scale-105';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
