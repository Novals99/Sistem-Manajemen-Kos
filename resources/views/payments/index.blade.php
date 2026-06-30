<x-layouts.app :title="'Payments'">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-charcoal">Payment Management</h2>
            <p class="text-sm text-cool-gray">Manage rent and deposit payments</p>
        </div>
        @if(auth()->user()->isStaff())
        <a href="{{ route('payments.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Payment
        </a>
        @endif
    </div>

    <div class="card mb-6">
        <form method="GET" action="{{ route('payments.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by code or tenant..." class="input flex-1" />
            <select name="status" class="select w-40" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
            <select name="payment_type" class="select w-40" onchange="this.form.submit()">
                <option value="">All Types</option>
                <option value="rent" {{ request('payment_type') === 'rent' ? 'selected' : '' }}>Rent</option>
                <option value="deposit" {{ request('payment_type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                <option value="penalty" {{ request('payment_type') === 'penalty' ? 'selected' : '' }}>Penalty</option>
                <option value="other" {{ request('payment_type') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <button type="submit" class="btn-primary">Search</button>
            @if(request('search') || request('status') || request('payment_type'))
                <a href="{{ route('payments.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card !p-0">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Date</th>
                        <th>Tenant</th>
                        <th>Booking / Room</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="font-mono text-xs">{{ $payment->payment_code }}</td>
                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                            <td>{{ $payment->tenant->name ?? '-' }}</td>
                            <td>
                                @if($payment->booking)
                                    <a href="{{ route('bookings.show', $payment->booking) }}" class="text-primary-600 hover:underline">
                                        {{ $payment->booking->booking_code }}
                                    </a><br>
                                    <span class="text-xs text-cool-gray">Room {{ $payment->booking->room->room_number ?? '-' }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ ucfirst($payment->payment_type) }}</td>
                            <td class="font-semibold text-charcoal">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
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
                            <td>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('payments.show', $payment) }}" class="btn-ghost btn-sm !px-2" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if(auth()->user()->isStaff())
                                        <a href="{{ route('payments.edit', $payment) }}" class="btn-ghost btn-sm !px-2" title="Edit">
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
                                    icon="folder-open" 
                                    title="No Payments Found" 
                                    message="No payments have been recorded for the selected filters."
                                    actionLabel="Record Payment"
                                    actionRoute="{{ route('payments.create') }}"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $payments->links() }}</div>
        @endif
    </div>
</x-layouts.app>
