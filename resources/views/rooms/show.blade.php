<x-layouts.app :title="'Room ' . $room->room_number">

    <div class="mb-6">
        <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Rooms
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Room Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-charcoal">Room {{ $room->room_number }}</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('rooms.edit', $room) }}" class="btn-outline btn-sm">Edit</a>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-cool-gray mb-1">Floor</p>
                        <p class="font-semibold text-charcoal">{{ $room->floor }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray mb-1">Type</p>
                        <p class="font-semibold text-charcoal">{{ ucfirst($room->type) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray mb-1">Price / Month</p>
                        <p class="font-semibold text-charcoal">Rp {{ number_format($room->price, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray mb-1">Status</p>
                        @php
                            $statusType = match($room->status) {
                                'available' => 'success',
                                'occupied' => 'primary',
                                'maintenance' => 'warning',
                                'reserved' => 'info',
                                default => 'gray',
                            };
                        @endphp
                        <x-badge :type="$statusType" :dot="true">{{ ucfirst($room->status) }}</x-badge>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray mb-1">Max Occupants</p>
                        <p class="font-semibold text-charcoal">{{ $room->max_occupants }}</p>
                    </div>
                </div>

                @if($room->description)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-cool-gray mb-1">Description</p>
                        <p class="text-sm text-charcoal">{{ $room->description }}</p>
                    </div>
                @endif

                @if($room->facilities && count($room->facilities))
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-cool-gray mb-2">Facilities</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($room->facilities as $facility)
                                <span class="badge-primary">{{ $facility }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Booking History --}}
            <div class="card !p-0">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-charcoal">Booking History</h3>
                </div>
                @if($room->bookings->count())
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead><tr><th>Code</th><th>Tenant</th><th>Check In</th><th>Duration</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($room->bookings->sortByDesc('created_at') as $booking)
                                    <tr>
                                        <td class="font-mono text-xs">{{ $booking->booking_code }}</td>
                                        <td>{{ $booking->tenant->name ?? '-' }}</td>
                                        <td>{{ $booking->check_in_date->format('d M Y') }}</td>
                                        <td>{{ $booking->duration_months }} months</td>
                                        <td>
                                            @php $bt = match($booking->status) { 'active'=>'success','pending'=>'warning','confirmed'=>'info','completed'=>'gray','cancelled'=>'danger',default=>'gray' }; @endphp
                                            <x-badge :type="$bt" :dot="true">{{ ucfirst($booking->status) }}</x-badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-sm text-cool-gray">No booking history.</div>
                @endif
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            {{-- Current Tenant --}}
            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Current Tenant</h4>
                @php $currentBooking = $room->bookings->where('status', 'active')->first(); @endphp
                @if($currentBooking && $currentBooking->tenant)
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold">
                            {{ strtoupper(substr($currentBooking->tenant->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-charcoal text-sm">{{ $currentBooking->tenant->name }}</p>
                            <p class="text-xs text-cool-gray">{{ $currentBooking->tenant->phone }}</p>
                        </div>
                    </div>
                    <div class="text-xs space-y-1 text-cool-gray">
                        <p>Since: {{ $currentBooking->check_in_date->format('d M Y') }}</p>
                        <p>Duration: {{ $currentBooking->duration_months }} months</p>
                        <p>Rate: Rp {{ number_format($currentBooking->monthly_rate, 0, ',', '.') }}/mo</p>
                    </div>
                @else
                    <p class="text-sm text-cool-gray">No current tenant</p>
                @endif
            </div>

            {{-- Maintenance --}}
            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Maintenance Issues</h4>
                @php $pendingMaint = $room->maintenances->whereIn('status', ['reported','in_progress']); @endphp
                @if($pendingMaint->count())
                    <div class="space-y-3">
                        @foreach($pendingMaint as $m)
                            <div class="p-3 rounded-lg bg-surface-hover">
                                <p class="text-sm font-semibold text-charcoal">{{ $m->title }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    @php $pt = match($m->priority) { 'urgent'=>'danger','high'=>'warning','medium'=>'info',default=>'gray' }; @endphp
                                    <x-badge :type="$pt">{{ ucfirst($m->priority) }}</x-badge>
                                    <x-badge :type="$m->status === 'in_progress' ? 'info' : 'warning'" :dot="true">{{ str_replace('_',' ',ucfirst($m->status)) }}</x-badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-cool-gray">No pending issues</p>
                @endif
            </div>
        </div>
    </div>

</x-layouts.app>
