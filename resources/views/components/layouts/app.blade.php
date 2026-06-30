<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="KosManager — Modern Boarding House Management System">

    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name', 'KosManager') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface" 
      x-data="{ sidebarOpen: false, theme: localStorage.getItem('theme') || 'light' }"
      x-init="$watch('theme', val => localStorage.setItem('theme', val))"
      :class="{ 'dark': theme === 'dark' }">

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/30 lg:hidden"
         style="display: none;"
    ></div>

    {{-- Sidebar --}}
    <x-sidebar />

    {{-- Main Content --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">
        {{-- Navbar --}}
        <x-navbar :title="$title ?? 'Dashboard'" />

        {{-- Page Content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            @if(session('error'))
                <x-alert type="error" :message="session('error')" />
            @endif

            @if(session('warning'))
                <x-alert type="warning" :message="session('warning')" />
            @endif

            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="px-6 py-4 text-center text-xs text-cool-gray border-t border-gray-100">
            &copy; {{ date('Y') }} {{ config('app.name') }}. Built for PBPSI — Universitas Budi Luhur.
        </footer>
    </div>

</body>
</html>
