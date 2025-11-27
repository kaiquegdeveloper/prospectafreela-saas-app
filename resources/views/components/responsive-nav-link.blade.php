@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center w-full ps-3 pe-4 py-2 border-l-4 border-neon-lime-200 text-start text-base font-medium text-gray-900 dark:text-gray-100 bg-neon-lime-200/10 dark:bg-neon-lime-200/5 focus:outline-none transition duration-150 ease-in-out rounded-r-lg'
            : 'flex items-center w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-800 transition duration-150 ease-in-out rounded-r-lg';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
