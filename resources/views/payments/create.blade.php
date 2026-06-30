<x-layouts.app :title="'Record Payment'">
    <div class="mb-6">
        <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Payments
        </a>
        <h2 class="text-lg font-bold text-charcoal mt-2">Record New Payment</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label for="booking_id" class="label">Booking <span class="text-danger-500">*</span></label>
                        <select id="booking_id" name="booking_id" class="select" required>
                            <option value="">Select Booking</option>
                            @foreach($bookings as $booking)
                                <option value="{{ $booking->id }}" {{ (old('booking_id') == $booking->id || ($selectedBooking && $selectedBooking->id == $booking->id)) ? 'selected' : '' }}>
                                    {{ $booking->booking_code }} - {{ $booking->tenant->name }} (Room {{ $booking->room->room_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('booking_id') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="amount" class="label">Amount (Rp) <span class="text-danger-500">*</span></label>
                        <input type="number" id="amount" name="amount" value="{{ old('amount') }}" class="input" min="1" required />
                        @error('amount') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="period_month" class="label">Month <span class="text-danger-500">*</span></label>
                            <select id="period_month" name="period_month" class="select" required>
                                @for($i=1; $i<=12; $i++)
                                    <option value="{{ $i }}" {{ old('period_month', now()->month) == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="period_year" class="label">Year <span class="text-danger-500">*</span></label>
                            <input type="number" id="period_year" name="period_year" value="{{ old('period_year', now()->year) }}" class="input" min="2020" max="2099" required />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="payment_date" class="label">Payment Date <span class="text-danger-500">*</span></label>
                            <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" class="input" required />
                            @error('payment_date') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="payment_method" class="label">Method <span class="text-danger-500">*</span></label>
                            <select id="payment_method" name="payment_method" class="select" required>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="e-wallet" {{ old('payment_method') == 'e-wallet' ? 'selected' : '' }}>E-Wallet / QRIS</option>
                            </select>
                            @error('payment_method') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="payment_type" class="label">Type <span class="text-danger-500">*</span></label>
                            <select id="payment_type" name="payment_type" class="select" required>
                                <option value="rent" {{ old('payment_type') == 'rent' ? 'selected' : '' }}>Rent</option>
                                <option value="deposit" {{ old('payment_type') == 'deposit' ? 'selected' : '' }}>Deposit</option>
                                <option value="penalty" {{ old('payment_type') == 'penalty' ? 'selected' : '' }}>Penalty</option>
                                <option value="other" {{ old('payment_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('payment_type') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="status" class="label">Status <span class="text-danger-500">*</span></label>
                            <select id="status" name="status" class="select" required>
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                            @error('status') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="proof_of_payment" class="label">Proof of Payment</label>
                        <input type="file" id="proof_of_payment" name="proof_of_payment" class="input !p-2" accept="image/*" />
                        @error('proof_of_payment') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label for="notes" class="label">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="input">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">Record Payment</button>
                <a href="{{ route('payments.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
