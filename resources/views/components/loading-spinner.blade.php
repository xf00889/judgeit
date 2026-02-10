@props(['size' => 'md', 'text' => null])

@php
    $sizeClasses = [
        'sm' => 'w-5 h-5',
        'md' => 'w-8 h-8',
        'lg' => 'w-12 h-12',
    ];
    
    $spinnerSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center']) }}>
    <div class="spinner {{ $spinnerSize }}"></div>
    @if($text)
        <p class="mt-3 text-sm text-gray-600">{{ $text }}</p>
    @endif
</div>
