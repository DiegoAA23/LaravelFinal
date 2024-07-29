
@props(['route'])

@php
    $classes = 'inline-flex items-center px-4 py-2 bg-gray-800 border-0 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring ring-gray-300 transition ease-in-out duration-150 ' . ($attributes->get('class') ?? '');

    $attributes = $attributes->merge([
        'class' => $classes,
    ]);
@endphp

<a href="{{ route($route) }}" {{ $attributes }}>
    {{ $slot }}
</a>
