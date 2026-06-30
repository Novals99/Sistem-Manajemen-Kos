<x-layouts.app :title="'Issue: ' . $maintenance->title">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('maintenances.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Maintenance
            </a>
            <h2 class="text-lg font-bold text-charcoal mt-2">{{ $maintenance->title }}</h2>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->isStaff())
                @if($maintenance->status !== 'resolved')
                    <button type="button" onclick="document.getElementById('resolve-modal').showModal()" class="btn-success">Mark Resolved</button>
                @endif
                <a href="{{ route('maintenances.edit', $maintenance) }}" class="btn-secondary">Edit</a>
                @if($maintenance->status !== 'in_progress')
                    <form action="{{ route('maintenances.destroy', $maintenance) }}" method="POST" onsubmit="return confirm('Delete this issue?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger">Delete</button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="card">
                <h3 class="font-bold text-charcoal mb-4">Issue Details</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div>
                        <p class="text-xs text-cool-gray mb-1">Status</p>
                        @php
                            $st = match($maintenance->status) {
                                'resolved' => 'success',
                                'in_progress' => 'info',
                                'reported' => 'warning',
                                default => 'gray',
                            };
                        @endphp
                        <x-badge :type="$st" :dot="true">{{ str_replace('_', ' ', ucfirst($maintenance->status)) }}</x-badge>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray mb-1">Priority</p>
                        @php
                            $pt = match($maintenance->priority) {
                                'urgent' => 'danger',
                                'high' => 'warning',
                                'medium' => 'info',
                                default => 'gray',
                            };
                        @endphp
                        <x-badge :type="$pt">{{ ucfirst($maintenance->priority) }}</x-badge>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray mb-1">Category</p>
                        <p class="font-medium text-charcoal">{{ ucfirst($maintenance->category) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-cool-gray mb-1">Date Reported</p>
                        <p class="font-medium text-charcoal">{{ $maintenance->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <p class="text-xs text-cool-gray mb-2">Description</p>
                    <p class="text-sm text-charcoal whitespace-pre-wrap">{{ $maintenance->description }}</p>
                </div>
                
                @if($maintenance->notes)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-cool-gray mb-2">Resolution Notes</p>
                    <p class="text-sm text-charcoal whitespace-pre-wrap">{{ $maintenance->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Room Info</h4>
                <div class="p-4 rounded-xl bg-primary-50 text-center mb-3">
                    <p class="text-2xl font-bold text-primary-600">Room {{ $maintenance->room->room_number }}</p>
                    <p class="text-xs text-primary-600 mt-1">Floor {{ $maintenance->room->floor }} · {{ ucfirst($maintenance->room->type) }}</p>
                </div>
                <a href="{{ route('rooms.show', $maintenance->room) }}" class="btn-outline w-full justify-center">View Room</a>
            </div>

            <div class="card">
                <h4 class="font-bold text-charcoal mb-3">Reported By</h4>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-surface-hover flex items-center justify-center font-bold">
                        {{ strtoupper(substr($maintenance->reporter->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-charcoal">{{ $maintenance->reporter->name }}</p>
                        <p class="text-xs text-cool-gray">{{ ucfirst($maintenance->reporter->role) }}</p>
                    </div>
                </div>
            </div>
            
            @if($maintenance->status === 'resolved')
            <div class="card bg-success-50 border-success-100">
                <h4 class="font-bold text-success-800 mb-2">Resolution Details</h4>
                <p class="text-sm text-success-700 mb-1">Resolved: {{ $maintenance->resolved_at->format('d M Y H:i') }}</p>
                <p class="text-sm font-semibold text-success-700">Cost: Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Resolve Modal --}}
    @if(auth()->user()->isStaff() && $maintenance->status !== 'resolved')
    <dialog id="resolve-modal" class="modal p-0 rounded-xl shadow-xl border border-gray-100 backdrop:bg-gray-900/50 backdrop:backdrop-blur-sm">
        <div class="w-full max-w-md bg-surface">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg">Mark as Resolved</h3>
                <form method="dialog"><button class="text-cool-gray hover:text-charcoal">✕</button></form>
            </div>
            <form action="{{ route('maintenances.resolve', $maintenance) }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="cost" class="label">Repair Cost (Rp)</label>
                        <input type="number" id="cost" name="cost" class="input" min="0" value="0" />
                    </div>
                    <div>
                        <label for="notes" class="label">Resolution Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="input" placeholder="What was fixed..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <form method="dialog"><button type="button" onclick="document.getElementById('resolve-modal').close()" class="btn-secondary">Cancel</button></form>
                    <button type="submit" class="btn-success">Resolve Issue</button>
                </div>
            </form>
        </div>
    </dialog>
    @endif
</x-layouts.app>
