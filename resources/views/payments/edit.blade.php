<x-layouts.app :title="'Edit Payment'">
    <div class="mb-6">
        <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Payments
        </a>
        <h2 class="text-lg font-bold text-charcoal mt-2">Edit Payment: {{ $payment->payment_code }}</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('payments.update', $payment) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-cool-gray">Booking</p>
                        <p class="font-semibold">{{ $payment->booking->booking_code ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Amount</p>
                        <p class="font-semibold text-lg">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray">Type & Period</p>
                        <p class="font-semibold">{{ ucfirst($payment->payment_type) }} ({{ date('F', mktime(0, 0, 0, $payment->period_month, 1)) }} {{ $payment->period_year }})</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="status" class="label">Status</label>
                        <select id="status" name="status" class="select" required>
                            <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ old('status', $payment->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ old('status', $payment->status) == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ old('status', $payment->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                        @error('status') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="label">Method</label>
                        <select id="payment_method" name="payment_method" class="select" required>
                            <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ old('payment_method', $payment->payment_method) == 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="e-wallet" {{ old('payment_method', $payment->payment_method) == 'e-wallet' ? 'selected' : '' }}>E-Wallet / QRIS</option>
                        </select>
                        @error('payment_method') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="proof_of_payment" class="label">Update Proof of Payment</label>
                        @if($payment->proof_of_payment)
                            <div class="mb-2"><img src="{{ Storage::url($payment->proof_of_payment) }}" class="h-20 object-contain rounded border" /></div>
                        @endif
                        <input type="file" id="proof_of_payment" name="proof_of_payment" class="input !p-2" accept="image/*" />
                        @error('proof_of_payment') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label for="notes" class="label">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="input">{{ old('notes', $payment->notes) }}</textarea>
                @error('notes') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">Update Payment</button>
                <a href="{{ route('payments.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
