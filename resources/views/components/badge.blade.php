@props([
    'type' => 'gray',
    'dot' => false,
])

@php
    $classes = [
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
        'info' => 'badge-info',
        'primary' => 'badge-primary',
        'gray' => 'badge-gray',
    ];
    $dotColors = [
        'success' => 'bg-success-500',
        'warning' => 'bg-warning-500',
        'danger' => 'bg-danger-500',
        'info' => 'bg-info-500',
        'primary' => 'bg-primary-500',
        'gray' => 'bg-cool-gray',
    ];
@endphp

<span {{ $attributes->merge(['class' => $classes[$type] ?? $classes['gray']]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$type] ?? $dotColors['gray'] }}"></span>
    @endif
    {{ $slot }}
</span>
