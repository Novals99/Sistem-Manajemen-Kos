<x-layouts.app :title="'Edit Booking'">
    <div class="mb-6">
        <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Bookings
        </a>
        <h2 class="text-lg font-bold text-charcoal mt-2">Edit Booking: {{ $booking->booking_code }}</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('bookings.update', $booking) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label for="tenant_id" class="label">Tenant</label>
                        <select id="tenant_id" name="tenant_id" class="select" required>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ (old('tenant_id', $booking->tenant_id) == $tenant->id) ? 'selected' : '' }}>
                                    {{ $tenant->name }} ({{ $tenant->id_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('tenant_id') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="room_id" class="label">Room</label>
                        <select id="room_id" name="room_id" class="select" required>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ (old('room_id', $booking->room_id) == $room->id) ? 'selected' : '' }}>
                                    Room {{ $room->room_number }} (Rp {{ number_format($room->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('room_id') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="check_in_date" class="label">Check In Date</label>
                        <input type="date" id="check_in_date" name="check_in_date" value="{{ old('check_in_date', $booking->check_in_date->format('Y-m-d')) }}" class="input" required />
                        @error('check_in_date') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="duration_months" class="label">Duration (Months)</label>
                        <input type="number" id="duration_months" name="duration_months" value="{{ old('duration_months', $booking->duration_months) }}" class="input" min="1" required />
                        @error('duration_months') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="deposit" class="label">Deposit Amount (Rp)</label>
                        <input type="number" id="deposit" name="deposit" value="{{ old('deposit', $booking->deposit) }}" class="input" min="0" required />
                        @error('deposit') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="label">Status</label>
                        <select id="status" name="status" class="select" required>
                            <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="active" {{ old('status', $booking->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label for="notes" class="label">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="input">{{ old('notes', $booking->notes) }}</textarea>
                @error('notes') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">Update Booking</button>
                <a href="{{ route('bookings.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
