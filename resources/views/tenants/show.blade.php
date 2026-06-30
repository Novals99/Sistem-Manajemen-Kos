<x-layouts.app :title="$tenant->name">
    <div class="mb-6">
        <a href="{{ route('tenants.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Tenants
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-xl">
                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-charcoal">{{ $tenant->name }}</h3>
                            <p class="text-sm text-cool-gray">{{ $tenant->occupation ?? 'No occupation listed' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('tenants.edit', $tenant) }}" class="btn-outline btn-sm">Edit</a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                    <div><p class="text-xs text-cool-gray mb-1">Email</p><p class="text-sm font-medium text-charcoal">{{ $tenant->email ?? '-' }}</p></div>
                    <div><p class="text-xs text-cool-gray mb-1">Phone</p><p class="text-sm font-medium text-charcoal">{{ $tenant->phone }}</p></div>
                    <div><p class="text-xs text-cool-gray mb-1">ID Number</p><p class="text-sm font-mono text-charcoal">{{ $tenant->id_number }}</p></div>
                    <div><p class="text-xs text-cool-gray mb-1">Emergency Contact</p><p class="text-sm font-medium text-charcoal">{{ $tenant->emergency_contact ?? '-' }}</p></div>
                    <div><p class="text-xs text-cool-gray mb-1">Emergency Phone</p><p class="text-sm font-medium text-charcoal">{{ $tenant->emergency_phone ?? '-' }}</p></div>
                    <div><p class="text-xs text-cool-gray mb-1">Registered</p><p class="text-sm font-medium text-charcoal">{{ $tenant->created_at->format('d M Y') }}</p></div>
                </div>

                @if($tenant->address)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-cool-gray mb-1">Origin Address</p>
                        <p class="text-sm text-charcoal">{{ $tenant->address }}</p>
                    </div>
                @endif
            </div>

            {{-- Booking History --}}
            <div class="card !p-0">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-charcoal">Booking History</h3>
                </div>
                @if($tenant->bookings->count())
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead><tr><th>Code</th><th>Room</th><th>Check In</th><th>Duration</th><th>Rate</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($tenant->bookings->sortByDesc('created_at') as $booking)
                                    <tr>
                                        <td class="font-mono text-xs">{{ $booking->booking_code }}</td>
                                        <td>{{ $booking->room->room_number ?? '-' }}</td>
                                        <td>{{ $booking->check_in_date->format('d M Y') }}</td>
                                        <td>{{ $booking->duration_months }} mo</td>
                                        <td>Rp {{ number_format($booking->monthly_rate, 0, ',', '.') }}</td>
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

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Current Room</h4>
                @php $currentRoom = $tenant->currentRoom(); @endphp
                @if($currentRoom)
                    <div class="p-4 rounded-xl bg-success-50 text-center">
                        <p class="text-2xl font-bold text-success-600">{{ $currentRoom->room_number }}</p>
                        <p class="text-xs text-success-600 mt-1">Floor {{ $currentRoom->floor }} · {{ ucfirst($currentRoom->type) }}</p>
                    </div>
                @else
                    <p class="text-sm text-cool-gray">No active room</p>
                @endif
            </div>

            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Payment Summary</h4>
                @php
                    $totalPaid = $tenant->payments->where('status', 'paid')->sum('amount');
                    $totalPending = $tenant->payments->where('status', 'pending')->sum('amount');
                @endphp
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-cool-gray">Total Paid</span>
                        <span class="text-sm font-semibold text-success-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-cool-gray">Pending</span>
                        <span class="text-sm font-semibold text-warning-600">Rp {{ number_format($totalPending, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
