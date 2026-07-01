<x-layouts.app :title="'Notifications'">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-charcoal">Notifications</h2>
            <p class="text-sm text-cool-gray">Manage and view your system notifications</p>
        </div>
        @if(auth()->user()->unreadNotifications()->count() > 0)
            <button onclick="event.preventDefault(); markAllNotificationsRead()" class="btn-secondary">
                Mark all as read
            </button>
        @endif
    </div>

    {{-- Notifications List --}}
    <div class="card !p-0">
        <div class="divide-y divide-border-theme/40">
            @forelse($notifications as $notification)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 hover:bg-surface-hover transition-colors {{ $notification->read_at ? 'opacity-70' : 'bg-primary-50/10' }}">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-surface flex items-center justify-center shadow-sm border border-border-theme flex-shrink-0">
                            @switch($notification->data['icon'] ?? 'bell')
                                @case('calendar')
                                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @break
                                @case('credit-card')
                                    <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    @break
                                @case('wrench')
                                    <svg class="w-5 h-5 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    @break
                                @case('building')
                                    <svg class="w-5 h-5 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    @break
                                @case('users')
                                    <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    @break
                                @case('shield')
                                    <svg class="w-5 h-5 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    @break
                                @default
                                    <svg class="w-5 h-5 text-cool-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @endswitch
                        </div>
                        <div>
                            <p class="font-bold text-charcoal">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <p class="text-sm text-cool-gray mt-1">{{ $notification->data['description'] ?? '' }}</p>
                            <span class="text-xs text-cool-gray/80 mt-2 block">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-center">
                        @if(!$notification->read_at)
                            <button onclick="event.preventDefault(); markNotificationRead('{{ $notification->id }}', '{{ $notification->data['link'] ?? '#' }}')" class="btn-primary btn-sm">
                                Read
                            </button>
                        @endif
                        @if($notification->data['link'] ?? null)
                            <a href="{{ $notification->data['link'] }}" class="btn-secondary btn-sm">
                                View Details
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-0">
                    <x-empty-state 
                        icon="bell" 
                        title="All Caught Up!" 
                        message="You have no notifications at the moment."
                    />
                </div>
            @endforelse
        </div>
        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-border-theme">{{ $notifications->links() }}</div>
        @endif
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
                    if (redirectUrl && redirectUrl !== '#') {
                        window.location.href = redirectUrl;
                    } else {
                        window.location.reload();
                    }
                }
            })
            .catch(error => {
                if (redirectUrl && redirectUrl !== '#') {
                    window.location.href = redirectUrl;
                } else {
                    window.location.reload();
                }
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

</x-layouts.app>
