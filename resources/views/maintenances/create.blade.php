<x-layouts.app :title="'Report Issue'">
    <div class="mb-6">
        <a href="{{ route('maintenances.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Maintenance
        </a>
        <h2 class="text-lg font-bold text-charcoal mt-2">Report Maintenance Issue</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('maintenances.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label for="room_id" class="label">Room <span class="text-danger-500">*</span></label>
                        <select id="room_id" name="room_id" class="select" required>
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    Room {{ $room->room_number }} (Floor {{ $room->floor }})
                                </option>
                            @endforeach
                        </select>
                        @error('room_id') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="title" class="label">Issue Title <span class="text-danger-500">*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" class="input" placeholder="e.g. AC not cooling" required />
                        @error('title') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="label">Category <span class="text-danger-500">*</span></label>
                            <select id="category" name="category" class="select" required>
                                <option value="plumbing" {{ old('category') == 'plumbing' ? 'selected' : '' }}>Plumbing</option>
                                <option value="electrical" {{ old('category') == 'electrical' ? 'selected' : '' }}>Electrical</option>
                                <option value="appliance" {{ old('category') == 'appliance' ? 'selected' : '' }}>Appliance</option>
                                <option value="furniture" {{ old('category') == 'furniture' ? 'selected' : '' }}>Furniture</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="priority" class="label">Priority <span class="text-danger-500">*</span></label>
                            <select id="priority" name="priority" class="select" required>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description" class="label">Detailed Description <span class="text-danger-500">*</span></label>
                    <textarea id="description" name="description" rows="7" class="input" placeholder="Please describe the issue in detail..." required>{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-danger-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">Submit Request</button>
                <a href="{{ route('maintenances.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
