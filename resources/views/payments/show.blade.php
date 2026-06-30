<x-layouts.app :title="'Payment ' . $payment->payment_code">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Payments
            </a>
            <h2 class="text-lg font-bold text-charcoal mt-2">Payment {{ $payment->payment_code }}</h2>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->isStaff())
                <a href="{{ route('payments.edit', $payment) }}" class="btn-secondary">Edit</a>
                @if($payment->status !== 'paid')
                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Delete this payment?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger">Delete</button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-6">
            <div class="card">
                <h3 class="font-bold text-charcoal mb-4">Payment Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-cool-gray">Status</p>
                        @php
                            $pt = match($payment->status) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                'refunded' => 'info',
                                default => 'gray',
                            };
                        @endphp
                        <x-badge :type="$pt" class="mt-1">{{ ucfirst($payment->status) }}</x-badge>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Amount</p>
                        <p class="font-semibold text-xl text-charcoal">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Payment Date</p>
                        <p class="font-semibold">{{ $payment->payment_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Method</p>
                        <p class="font-semibold">{{ ucfirst($payment->payment_method) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Type</p>
                        <p class="font-semibold">{{ ucfirst($payment->payment_type) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Period</p>
                        <p class="font-semibold">{{ date('F', mktime(0, 0, 0, $payment->period_month, 1)) }} {{ $payment->period_year }}</p>
                    </div>
                </div>
                @if($payment->notes)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs text-cool-gray">Notes</p>
                        <p class="text-sm mt-1">{{ $payment->notes }}</p>
                    </div>
                @endif
            </div>

            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Related Booking</h4>
                @if($payment->booking)
                    <p class="font-mono text-sm mb-1">{{ $payment->booking->booking_code }}</p>
                    <p class="text-sm text-cool-gray mb-3">Room {{ $payment->booking->room->room_number ?? '-' }}</p>
                    <a href="{{ route('bookings.show', $payment->booking) }}" class="btn-outline w-full justify-center">View Booking</a>
                @else
                    <p class="text-sm text-cool-gray">No related booking</p>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Proof of Payment</h4>
                @if($payment->proof_of_payment)
                    <a href="{{ Storage::url($payment->proof_of_payment) }}" target="_blank">
                        <img src="{{ Storage::url($payment->proof_of_payment) }}" class="w-full rounded-lg border object-cover" style="max-height: 400px" />
                    </a>
                    <p class="text-xs text-cool-gray mt-2 text-center">Click to enlarge</p>
                @else
                    <div class="p-8 rounded-lg bg-surface-hover flex flex-col items-center justify-center text-cool-gray border border-dashed border-gray-100">
                        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-sm">No proof uploaded</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
