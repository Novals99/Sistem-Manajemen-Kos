<x-layouts.app :title="'Booking ' . $booking->booking_code">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Bookings
            </a>
            <h2 class="text-lg font-bold text-charcoal mt-2">Booking {{ $booking->booking_code }}</h2>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->isStaff())
                @if(in_array($booking->status, ['pending', 'confirmed']))
                    <form action="{{ route('bookings.check-in', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-success">Check In</button>
                    </form>
                @endif
                @if($booking->status === 'active')
                    <form action="{{ route('bookings.check-out', $booking) }}" method="POST" onsubmit="return confirm('Check out this tenant?')">
                        @csrf
                        <button type="submit" class="btn-outline">Check Out</button>
                    </form>
                @endif
                @if(!in_array($booking->status, ['completed', 'cancelled']))
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Cancel this booking?')">
                        @csrf
                        <button type="submit" class="btn-danger">Cancel Booking</button>
                    </form>
                @endif
                <a href="{{ route('bookings.edit', $booking) }}" class="btn-secondary">Edit</a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <h3 class="text-lg font-bold text-charcoal mb-4">Booking Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-cool-gray">Status</p>
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
                        <x-badge :type="$statusType" class="mt-1">{{ ucfirst($booking->status) }}</x-badge>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Monthly Rate</p>
                        <p class="font-semibold text-charcoal">Rp {{ number_format($booking->monthly_rate, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Check In</p>
                        <p class="font-semibold text-charcoal">{{ $booking->check_in_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Check Out</p>
                        <p class="font-semibold text-charcoal">{{ $booking->check_out_date ? $booking->check_out_date->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Duration</p>
                        <p class="font-semibold text-charcoal">{{ $booking->duration_months }} Months</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Deposit</p>
                        <p class="font-semibold text-charcoal">Rp {{ number_format($booking->deposit, 0, ',', '.') }}</p>
                    </div>
                </div>
                @if($booking->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-cool-gray">Notes</p>
                        <p class="text-sm text-charcoal mt-1">{{ $booking->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="card !p-0">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-charcoal">Payment History</h3>
                    <a href="{{ route('payments.create', ['booking_id' => $booking->id]) }}" class="btn-primary btn-sm">Add Payment</a>
                </div>
                @if($booking->payments->count())
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($booking->payments->sortByDesc('payment_date') as $payment)
                                    <tr>
                                        <td class="font-mono text-xs">{{ $payment->payment_code }}</td>
                                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                        <td>{{ ucfirst($payment->payment_type) }}</td>
                                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $pt = match($payment->status) {
                                                    'paid' => 'success',
                                                    'pending' => 'warning',
                                                    'failed' => 'danger',
                                                    'refunded' => 'info',
                                                    default => 'gray',
                                                };
                                            @endphp
                                            <x-badge :type="$pt" :dot="true">{{ ucfirst($payment->status) }}</x-badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-sm text-cool-gray">No payments found.</div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Room Info</h4>
                <div class="p-4 rounded-xl bg-primary-50 text-center mb-3">
                    <p class="text-2xl font-bold text-primary-600">Room {{ $booking->room->room_number }}</p>
                    <p class="text-xs text-primary-600 mt-1">Floor {{ $booking->room->floor }} · {{ ucfirst($booking->room->type) }}</p>
                </div>
                <a href="{{ route('rooms.show', $booking->room) }}" class="btn-outline w-full justify-center">View Room</a>
            </div>

            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Tenant Info</h4>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 rounded-full bg-success-50 flex items-center justify-center text-success-600 font-bold text-lg">
                        {{ strtoupper(substr($booking->tenant->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-charcoal">{{ $booking->tenant->name }}</p>
                        <p class="text-xs text-cool-gray">{{ $booking->tenant->phone }}</p>
                    </div>
                </div>
                <a href="{{ route('tenants.show', $booking->tenant) }}" class="btn-outline w-full justify-center">View Tenant</a>
            </div>
        </div>
    </div>
</x-layouts.app>
