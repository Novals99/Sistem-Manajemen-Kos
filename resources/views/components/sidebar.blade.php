@php
    $user = auth()->user();
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home'],
        ['label' => 'Rooms', 'route' => 'rooms.index', 'icon' => 'building', 'roles' => ['owner', 'admin']],
        ['label' => 'Tenants', 'route' => 'tenants.index', 'icon' => 'users', 'roles' => ['owner', 'admin']],
        ['label' => $user && $user->isResident() ? 'My Booking' : 'Bookings', 'route' => 'bookings.index', 'icon' => 'calendar'],
        ['label' => 'Payments', 'route' => 'payments.index', 'icon' => 'credit-card'],
        ['label' => 'Maintenance', 'route' => 'maintenances.index', 'icon' => 'wrench'],
        ['label' => 'Reports', 'route' => 'reports.index', 'icon' => 'chart', 'roles' => ['owner', 'admin']],
        ['label' => 'Activity Log', 'route' => 'activity-logs.index', 'icon' => 'clock', 'roles' => ['owner']],
        ['label' => 'User Management', 'route' => 'users.index', 'icon' => 'shield', 'roles' => ['owner']],
    ];
@endphp

{{-- Sidebar --}}
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar border-r border-gray-100 flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0"
>
    {{-- Logo --}}
    <div class="h-16 flex items-center gap-3 px-6 border-b border-gray-100">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </div>
        <span class="text-lg font-bold text-charcoal">KosManager</span>

        {{-- Close button (mobile) --}}
        <button @click="sidebarOpen = false" class="ml-auto lg:hidden text-cool-gray hover:text-charcoal">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @foreach($navItems as $item)
            @php
                $roles = $item['roles'] ?? null;
                $show = !$roles || (auth()->check() && in_array(auth()->user()->role, $roles));
                $active = false;
                try { $active = request()->routeIs($item['route'] . '*') || request()->routeIs($item['route']); } catch (\Exception $e) {}
            @endphp

            @if($show)
                @php
                    $href = '#';
                    try { $href = route($item['route']); } catch (\Exception $e) {}
                @endphp
                <a href="{{ $href }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                          {{ $active
                              ? 'bg-primary-50/80 text-primary-600 shadow-sm'
                              : 'text-cool-gray hover:bg-sidebar-hover hover:text-charcoal' }}"
                >
                    {{-- Icon --}}
                    <span class="w-5 h-5 flex-shrink-0">
                        @switch($item['icon'])
                            @case('home')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                @break
                            @case('building')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                @break
                            @case('users')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                @break
                            @case('calendar')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @break
                            @case('credit-card')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                @break
                            @case('wrench')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                @break
                            @case('chart')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                @break
                            @case('clock')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @break
                            @case('shield')
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                @break
                        @endswitch
                    </span>

                    {{ $item['label'] }}

                    {{-- Active indicator --}}
                    @if($active)
                        <span class="ml-auto w-1.5 h-1.5 rounded-full bg-primary-500"></span>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    {{-- User Profile (Bottom) with Dropdown --}}
    <div class="p-3 border-t border-gray-100" x-data="{ userMenuOpen: false }">
        <div class="relative">
            <button @click="userMenuOpen = !userMenuOpen" @click.outside="userMenuOpen = false" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-sidebar-hover transition-colors duration-200">
                <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0 text-left">
                    <p class="text-sm font-semibold text-charcoal truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-cool-gray truncate">{{ ucfirst(auth()->user()->role ?? 'resident') }}</p>
                </div>
                <svg class="w-4 h-4 text-cool-gray flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
            
            {{-- Dropdown Menu --}}
            <div x-show="userMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1"
                 class="absolute bottom-full left-0 w-full mb-2 bg-dropdown rounded-xl shadow-lg border border-gray-100 py-1 overflow-hidden z-50"
                 style="display: none;">
                
                {{-- Appearance --}}
                <div class="px-4 py-3 border-b border-gray-100">
                    <p class="text-[10px] font-bold text-cool-gray mb-2.5 uppercase tracking-wider">Appearance</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-charcoal">Dark Mode</span>
                        <button 
                            type="button"
                            @click="theme = theme === 'light' ? 'dark' : 'light'" 
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500/20"
                            :class="theme === 'dark' ? 'bg-primary-500' : 'bg-gray-200'"
                            role="switch" 
                            :aria-checked="theme === 'dark' ? 'true' : 'false'"
                        >
                            <!-- Slidable Pill Ball -->
                            <span 
                                class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
                                :class="theme === 'dark' ? 'translate-x-5' : 'translate-x-0'"
                            >
                                <!-- Sun Icon (Visible in Light Mode) -->
                                <span 
                                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                    :class="theme === 'dark' ? 'opacity-0 ease-out duration-100' : 'opacity-100 ease-in duration-200'"
                                    aria-hidden="true"
                                >
                                    <svg class="h-3 w-3 text-warning-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464-4.95a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 1.414l-.707.707zm2.652 2.652a1 1 0 01-1.414 1.414l-.707-.707a1 1 0 011.414-1.414l.707.707zM16 10a1 1 0 01-1-1h-1a1 1 0 110 2h1a1 1 0 011-1zm-3.05 4.95a1 1 0 111.414-1.414l.707.707a1 1 0 00-1.414 1.414l-.707-.707zm-2.652 2.652a1 1 0 101.414-1.414l-.707-.707a1 1 0 10-1.414 1.414l.707.707zM4 10a1 1 0 01-1-1H2a1 1 0 110 2h1a1 1 0 011-1zm1.05-4.95a1 1 0 10-1.414 1.414l.707.707a1 1 0 101.414-1.414l-.707-.707zm7.07 10.606a1 1 0 11-1.414-1.414l.707-.707a1 1 0 111.414 1.414l-.707.707z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <!-- Moon Icon (Visible in Dark Mode) -->
                                <span 
                                    class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                    :class="theme === 'dark' ? 'opacity-100 ease-in duration-200' : 'opacity-0 ease-out duration-100'"
                                    aria-hidden="true"
                                >
                                    <svg class="h-3 w-3 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                                    </svg>
                                </span>
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Logout --}}
                <div class="p-1">
                    <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="w-full text-left px-3 py-2 text-sm text-danger-500 hover:bg-danger-50 hover:text-danger-600 rounded-md transition-colors flex items-center gap-2 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Log out
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
</aside>
