<x-layouts.app :title="'Edit Issue'">
    <div class="mb-6">
        <a href="{{ route('maintenances.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Maintenance
        </a>
        <h2 class="text-lg font-bold text-charcoal mt-2">Edit Issue: {{ $maintenance->title }}</h2>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('maintenances.update', $maintenance) }}">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label for="room_id" class="label">Room</label>
                        <select id="room_id" name="room_id" class="select" required>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id', $maintenance->room_id) == $room->id ? 'selected' : '' }}>
                                    Room {{ $room->room_number }} (Floor {{ $room->floor }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="title" class="label">Issue Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $maintenance->title) }}" class="input" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="label">Category</label>
                            <select id="category" name="category" class="select" required>
                                <option value="plumbing" {{ old('category', $maintenance->category) == 'plumbing' ? 'selected' : '' }}>Plumbing</option>
                                <option value="electrical" {{ old('category', $maintenance->category) == 'electrical' ? 'selected' : '' }}>Electrical</option>
                                <option value="appliance" {{ old('category', $maintenance->category) == 'appliance' ? 'selected' : '' }}>Appliance</option>
                                <option value="furniture" {{ old('category', $maintenance->category) == 'furniture' ? 'selected' : '' }}>Furniture</option>
                                <option value="other" {{ old('category', $maintenance->category) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="priority" class="label">Priority</label>
                            <select id="priority" name="priority" class="select" required>
                                <option value="low" {{ old('priority', $maintenance->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority', $maintenance->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority', $maintenance->priority) == 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority', $maintenance->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="label">Status</label>
                            <select id="status" name="status" class="select" required>
                                <option value="reported" {{ old('status', $maintenance->status) == 'reported' ? 'selected' : '' }}>Reported</option>
                                <option value="in_progress" {{ old('status', $maintenance->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ old('status', $maintenance->status) == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>
                        <div>
                            <label for="cost" class="label">Cost (Rp)</label>
                            <input type="number" id="cost" name="cost" value="{{ old('cost', $maintenance->cost) }}" class="input" min="0" />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="description" class="label">Description</label>
                        <textarea id="description" name="description" rows="4" class="input" required>{{ old('description', $maintenance->description) }}</textarea>
                    </div>

                    <div>
                        <label for="notes" class="label">Resolution Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="input" placeholder="Staff notes regarding repair...">{{ old('notes', $maintenance->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">Update Issue</button>
                <a href="{{ route('maintenances.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
