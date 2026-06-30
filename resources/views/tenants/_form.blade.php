@php $tenant = $tenant ?? null; @endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="space-y-5">
        <div>
            <label for="name" class="label">Full Name <span class="text-danger-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $tenant?->name) }}" class="input" placeholder="e.g. Budi Santoso" required />
            @error('name') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $tenant?->email) }}" class="input" placeholder="budi@example.com" />
            @error('email') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="phone" class="label">Phone <span class="text-danger-500">*</span></label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $tenant?->phone) }}" class="input" placeholder="08123456789" required />
            @error('phone') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="id_number" class="label">ID Number (KTP) <span class="text-danger-500">*</span></label>
            <input type="text" id="id_number" name="id_number" value="{{ old('id_number', $tenant?->id_number) }}" class="input" placeholder="3201234567890001" required />
            @error('id_number') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="id_photo" class="label">ID Photo</label>
            @if($tenant?->id_photo)
                <div class="mb-2"><img src="{{ Storage::url($tenant->id_photo) }}" class="w-32 h-20 object-cover rounded-lg border" /></div>
            @endif
            <input type="file" id="id_photo" name="id_photo" accept="image/*" class="input !p-2" />
            @error('id_photo') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="space-y-5">
        <div>
            <label for="occupation" class="label">Occupation</label>
            <input type="text" id="occupation" name="occupation" value="{{ old('occupation', $tenant?->occupation) }}" class="input" placeholder="e.g. Software Developer" />
            @error('occupation') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="address" class="label">Origin Address</label>
            <textarea id="address" name="address" rows="3" class="input" placeholder="Full address...">{{ old('address', $tenant?->address) }}</textarea>
            @error('address') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="emergency_contact" class="label">Emergency Contact Name</label>
            <input type="text" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $tenant?->emergency_contact) }}" class="input" placeholder="e.g. Ibu Ratna" />
            @error('emergency_contact') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="emergency_phone" class="label">Emergency Contact Phone</label>
            <input type="text" id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $tenant?->emergency_phone) }}" class="input" placeholder="08199887766" />
            @error('emergency_phone') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
