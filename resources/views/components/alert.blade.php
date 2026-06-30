@props([
    'type' => 'success',
    'message' => '',
    'dismissible' => true,
])

@php
    $colors = [
        'success' => 'bg-success-50 border-success-500 text-success-600',
        'error' => 'bg-danger-50 border-danger-500 text-danger-600',
        'warning' => 'bg-warning-50 border-warning-500 text-warning-600',
        'info' => 'bg-info-50 border-info-500 text-info-600',
    ];
    $iconPaths = [
        'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'error' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'warning' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    ];
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl border-l-4 {{ $colors[$type] ?? $colors['info'] }}"
     role="alert"
>
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPaths[$type] ?? $iconPaths['info'] }}"/>
    </svg>
    <p class="text-sm font-medium flex-1">{{ $message }}</p>
    @if($dismissible)
        <button @click="show = false" class="ml-auto opacity-60 hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
</div>
