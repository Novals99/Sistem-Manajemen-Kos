<x-layouts.app :title="'Bookings'">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-charcoal">Booking Management</h2>
            <p class="text-sm text-cool-gray">Manage room reservations</p>
        </div>
        @if(auth()->user()->isStaff())
        <a href="{{ route('bookings.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Booking
        </a>
        @endif
    </div>

    <div class="card mb-6">
        <form method="GET" action="{{ route('bookings.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code, tenant, or room..." class="input flex-1" />
            <select name="status" class="select w-48" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="btn-primary">Search</button>
            @if(request('search') || request('status'))
                <a href="{{ route('bookings.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card !p-0">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Booking Code</th>
                        <th>Tenant</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="font-mono text-xs">{{ $booking->booking_code }}</td>
                            <td>{{ $booking->tenant->name ?? '-' }}</td>
                            <td>{{ $booking->room->room_number ?? '-' }}</td>
                            <td>{{ $booking->check_in_date->format('d M Y') }}</td>
                            <td>{{ $booking->duration_months }} months</td>
                            <td>
                                @php
                                    $statusType = match($booking->status) {
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'completed' => 'gray',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-badge :type="$statusType" :dot="true">{{ ucfirst($booking->status) }}</x-badge>
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn-ghost btn-sm !px-2" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if(auth()->user()->isStaff())
                                        <a href="{{ route('bookings.edit', $booking) }}" class="btn-ghost btn-sm !px-2" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0 border-b-0">
                                <x-empty-state 
                                    icon="calendar" 
                                    title="No Bookings Found" 
                                    message="There are currently no bookings recorded in the system."
                                    actionLabel="New Booking"
                                    actionRoute="{{ route('bookings.create') }}"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $bookings->links() }}</div>
        @endif
    </div>
</x-layouts.app>
