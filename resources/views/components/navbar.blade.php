@props(['title' => 'Dashboard'])

<header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md border-b border-gray-100">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        {{-- Left: Hamburger + Title --}}
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden text-cool-gray hover:text-charcoal transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-xl font-bold text-charcoal">{{ $title }}</h1>
        </div>

        {{-- Right: Search + Actions --}}
        <div class="flex items-center gap-3">
            {{-- Search --}}
            <div class="hidden sm:block relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-cool-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text"
                       placeholder="Search..."
                       class="w-56 pl-9 pr-4 py-2 rounded-lg border border-gray-100 bg-surface text-sm text-charcoal placeholder:text-cool-gray focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200"
                />
            </div>

            {{-- Date Badge --}}
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-primary-500 text-white text-xs font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ now()->format('D, d M Y') }}
            </div>

            {{-- Notifications --}}
            <button class="relative p-2 rounded-lg text-cool-gray hover:bg-surface-hover hover:text-charcoal transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                {{-- Notification dot --}}
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-danger-500 rounded-full"></span>
            </button>

            {{-- User Avatar (mobile) --}}
            <div class="lg:hidden w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
        </div>
    </div>
</header>
