<x-layouts.app :title="'Dashboard'">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-charcoal">Overview</h2>
            <p class="text-sm text-cool-gray">Welcome back, {{ auth()->user()->name }}! Here's what's happening.</p>
        </div>
        @if(auth()->user()->isStaff())
            <div class="flex gap-2">
                <a href="#" class="btn-outline btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export Report
                </a>
            </div>
        @endif
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            label="Monthly Revenue"
            :value="$currentMonthRevenue"
            icon="money"
            prefix="Rp "
            :accent="true"
            subtitle="This month"
        />
        <x-stat-card
            label="Total Rooms"
            :value="$totalRooms"
            icon="room"
            :subtitle="$availableRooms . ' available'"
        />
        <x-stat-card
            label="Total Tenants"
            :value="$totalTenants"
            icon="users"
            :subtitle="$activeBookings . ' active bookings'"
        />
        <x-stat-card
            label="Occupancy Rate"
            :value="$occupancyRate . '%'"
            icon="chart"
            :subtitle="$occupiedRooms . ' of ' . $totalRooms . ' rooms'"
        />
    </div>

    {{-- Quick Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-warning-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-charcoal">{{ $pendingBookings }}</p>
                <p class="text-xs text-cool-gray">Pending Bookings</p>
            </div>
        </div>

        <div class="card flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-danger-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-charcoal">{{ $pendingPayments }}</p>
                <p class="text-xs text-cool-gray">Pending Payments</p>
            </div>
        </div>

        <div class="card flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-info-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-info-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-charcoal">{{ $pendingMaintenance }}</p>
                <p class="text-xs text-cool-gray">Maintenance Requests</p>
            </div>
        </div>
    </div>

    {{-- Tables Section --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Recent Bookings --}}
        <div class="card !p-0">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-charcoal">Recent Bookings</h3>
                @if(auth()->user()->isStaff())
                    <a href="#" class="btn-outline btn-sm">View All</a>
                @endif
            </div>

            @if($recentBookings->count())
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Tenant</th>
                                <th>Room</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td class="font-mono text-xs">{{ $booking->booking_code }}</td>
                                    <td>{{ $booking->tenant->name ?? '-' }}</td>
                                    <td>{{ $booking->room->room_number ?? '-' }}</td>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-10 text-center text-sm text-cool-gray">
                    No bookings yet.
                </div>
            @endif
        </div>

        {{-- Recent Payments --}}
        <div class="card !p-0">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-charcoal">Recent Payments</h3>
                @if(auth()->user()->isStaff())
                    <a href="#" class="btn-outline btn-sm">View All</a>
                @endif
            </div>

            @if($recentPayments->count())
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Tenant</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPayments as $payment)
                                <tr>
                                    <td class="font-mono text-xs">{{ $payment->payment_code }}</td>
                                    <td>{{ $payment->tenant->name ?? '-' }}</td>
                                    <td class="font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $payStatusType = match($payment->status) {
                                                'paid' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger',
                                                'refunded' => 'info',
                                                default => 'gray',
                                            };
                                        @endphp
                                        <x-badge :type="$payStatusType" :dot="true">{{ ucfirst($payment->status) }}</x-badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-10 text-center text-sm text-cool-gray">
                    No payments yet.
                </div>
            @endif
        </div>
    </div>

    {{-- Room Availability Quick View --}}
    @if(auth()->user()->isStaff())
    <div class="mt-6 card !p-0">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-charcoal">Room Availability</h3>
            <a href="#" class="btn-outline btn-sm">See Details</a>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center p-4 rounded-xl bg-success-50">
                    <p class="text-2xl font-bold text-success-600">{{ $availableRooms }}</p>
                    <p class="text-xs text-success-600 mt-1">Available</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-primary-50">
                    <p class="text-2xl font-bold text-primary-600">{{ $occupiedRooms }}</p>
                    <p class="text-xs text-primary-600 mt-1">Occupied</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-warning-50">
                    <p class="text-2xl font-bold text-warning-600">{{ \App\Models\Room::where('status', 'reserved')->count() }}</p>
                    <p class="text-xs text-warning-600 mt-1">Reserved</p>
                </div>
                <div class="text-center p-4 rounded-xl bg-danger-50">
                    <p class="text-2xl font-bold text-danger-600">{{ \App\Models\Room::where('status', 'maintenance')->count() }}</p>
                    <p class="text-xs text-danger-600 mt-1">Maintenance</p>
                </div>
            </div>
        </div>
    </div>
    @endif

</x-layouts.app>
