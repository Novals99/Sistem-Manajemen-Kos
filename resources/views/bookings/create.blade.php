<x-layouts.app :title="'Create Booking'">
    <div class="mb-6">
        <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Bookings
        </a>
        <h2 class="text-lg font-bold text-charcoal mt-2">Create New Booking</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('bookings.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label for="tenant_id" class="label">Tenant <span class="text-danger-500">*</span></label>
                        <select id="tenant_id" name="tenant_id" class="select" required>
                            <option value="">Select Tenant</option>
                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->name }} ({{ $tenant->id_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('tenant_id') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="room_id" class="label">Room <span class="text-danger-500">*</span></label>
                        <select id="room_id" name="room_id" class="select" required>
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    Room {{ $room->room_number }} (Rp {{ number_format($room->price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('room_id') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="check_in_date" class="label">Check In Date <span class="text-danger-500">*</span></label>
                        <input type="date" id="check_in_date" name="check_in_date" value="{{ old('check_in_date') }}" class="input" required />
                        @error('check_in_date') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="duration_months" class="label">Duration (Months) <span class="text-danger-500">*</span></label>
                        <input type="number" id="duration_months" name="duration_months" value="{{ old('duration_months', 1) }}" class="input" min="1" required />
                        @error('duration_months') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="deposit" class="label">Deposit Amount (Rp) <span class="text-danger-500">*</span></label>
                        <input type="number" id="deposit" name="deposit" value="{{ old('deposit', 0) }}" class="input" min="0" required />
                        @error('deposit') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <label for="notes" class="label">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="input" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">Create Booking</button>
                <a href="{{ route('bookings.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
