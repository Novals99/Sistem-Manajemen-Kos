@php
    $room = $room ?? null;
    $facilities = ['AC', 'WiFi', 'Private Bathroom', 'Shared Bathroom', 'TV', 'Refrigerator', 'Desk', 'Balcony', 'Fan', 'Wardrobe', 'Kitchen', 'Parking'];
    $selectedFacilities = old('facilities', $room?->facilities ?? []);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Left Column --}}
    <div class="space-y-5">
        <div>
            <label for="room_number" class="label">Room Number <span class="text-danger-500">*</span></label>
            <input type="text" id="room_number" name="room_number" value="{{ old('room_number', $room?->room_number) }}" class="input" placeholder="e.g. 101" required />
            @error('room_number') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="floor" class="label">Floor <span class="text-danger-500">*</span></label>
            <input type="number" id="floor" name="floor" value="{{ old('floor', $room?->floor ?? 1) }}" class="input" min="1" max="99" required />
            @error('floor') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="type" class="label">Room Type <span class="text-danger-500">*</span></label>
            <select id="type" name="type" class="select" required>
                @foreach(['single' => 'Single', 'double' => 'Double', 'suite' => 'Suite'] as $val => $lbl)
                    <option value="{{ $val }}" {{ old('type', $room?->type) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
            @error('type') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="price" class="label">Price per Month (Rp) <span class="text-danger-500">*</span></label>
            <input type="number" id="price" name="price" value="{{ old('price', $room?->price) }}" class="input" min="0" step="50000" placeholder="1500000" required />
            @error('price') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="max_occupants" class="label">Max Occupants <span class="text-danger-500">*</span></label>
            <input type="number" id="max_occupants" name="max_occupants" value="{{ old('max_occupants', $room?->max_occupants ?? 1) }}" class="input" min="1" max="10" required />
            @error('max_occupants') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>

        @if($room)
        <div>
            <label for="status" class="label">Status <span class="text-danger-500">*</span></label>
            <select id="status" name="status" class="select" required>
                @foreach(['available' => 'Available', 'occupied' => 'Occupied', 'maintenance' => 'Maintenance', 'reserved' => 'Reserved'] as $val => $lbl)
                    <option value="{{ $val }}" {{ old('status', $room->status) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>
            @error('status') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
        @endif
    </div>

    {{-- Right Column --}}
    <div class="space-y-5">
        <div>
            <label for="description" class="label">Description</label>
            <textarea id="description" name="description" rows="4" class="input" placeholder="Room description...">{{ old('description', $room?->description) }}</textarea>
            @error('description') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="label">Facilities</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-1">
                @foreach($facilities as $facility)
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-charcoal">
                        <input type="checkbox" name="facilities[]" value="{{ $facility }}"
                            {{ in_array($facility, $selectedFacilities) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-primary-500 focus:ring-primary-500/20" />
                        {{ $facility }}
                    </label>
                @endforeach
            </div>
            @error('facilities') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
