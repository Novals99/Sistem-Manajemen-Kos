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
            @php
                $unreadCount = auth()->user()->unreadNotifications()->count();
                $recentNotifications = auth()->user()->notifications()->take(5)->get();
            @endphp
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false" class="relative p-2 rounded-lg text-cool-gray hover:bg-surface-hover hover:text-charcoal transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-4 h-4 px-1 rounded-full bg-danger-500 text-[10px] font-bold text-white flex items-center justify-center border border-white dark:border-slate-900 shadow-sm animate-pulse">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </button>

                {{-- Notification Dropdown Panel --}}
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     class="absolute right-0 mt-2 w-80 bg-dropdown rounded-xl shadow-lg border border-border-theme py-1 z-50 overflow-hidden"
                     style="display: none;"
                >
                    <div class="px-4 py-2.5 border-b border-border-theme flex items-center justify-between">
                        <span class="font-bold text-charcoal text-sm">Notifications</span>
                        @if($unreadCount > 0)
                            <button onclick="event.preventDefault(); markAllNotificationsRead()" class="text-xs text-primary-500 hover:text-primary-600 font-semibold transition-colors">
                                Mark all read
                            </button>
                        @endif
                    </div>

                    <div class="max-h-80 overflow-y-auto divide-y divide-border-theme/40">
                        @forelse($recentNotifications as $notification)
                            <a href="#"
                               onclick="event.preventDefault(); markNotificationRead('{{ $notification->id }}', '{{ $notification->data['link'] ?? '#' }}')"
                               class="flex items-start gap-3 px-4 py-3 hover:bg-surface-hover transition-colors {{ $notification->read_at ? 'opacity-70' : 'bg-primary-50/20 font-medium' }}"
                            >
                                <div class="w-8 h-8 rounded-lg bg-surface flex items-center justify-center shadow-sm border border-border-theme flex-shrink-0">
                                    @switch($notification->data['icon'] ?? 'bell')
                                        @case('calendar')
                                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            @break
                                        @case('credit-card')
                                            <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            @break
                                        @case('wrench')
                                            <svg class="w-4 h-4 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            @break
                                        @case('building')
                                            <svg class="w-4 h-4 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            @break
                                        @case('users')
                                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            @break
                                        @case('shield')
                                            <svg class="w-4 h-4 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            @break
                                        @default
                                            <svg class="w-4 h-4 text-cool-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                    @endswitch
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-charcoal truncate">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                    <p class="text-[11px] text-cool-gray mt-0.5 line-clamp-2 leading-relaxed">{{ $notification->data['description'] ?? '' }}</p>
                                    <span class="text-[10px] text-cool-gray/80 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-xs text-cool-gray">
                                No notifications yet.
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('notifications.index') }}" class="block text-center py-2 bg-gray-50 dark:bg-slate-800/40 text-xs font-semibold text-charcoal border-t border-border-theme hover:bg-surface-hover transition-colors">
                        View All Notifications
                    </a>
                </div>
            </div>

            <script>
                function markNotificationRead(id, redirectUrl) {
                    fetch('/notifications/' + id + '/read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = redirectUrl;
                        }
                    })
                    .catch(error => {
                        window.location.href = redirectUrl;
                    });
                }

                function markAllNotificationsRead() {
                    fetch('/notifications/read-all', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                }
            </script>

            {{-- User Avatar (mobile) --}}
            <div class="lg:hidden w-8 h-8 rounded-full overflow-hidden bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-sm border border-border-theme/40">
                @if(auth()->user() && auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover" />
                @else
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                @endif
            </div>
        </div>
    </div>
</header>
