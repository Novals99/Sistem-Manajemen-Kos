<x-layouts.app :title="'Maintenance'">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-charcoal">Maintenance Management</h2>
            <p class="text-sm text-cool-gray">Manage room repair and maintenance requests</p>
        </div>
        <a href="{{ route('maintenances.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Report Issue
        </a>
    </div>

    <div class="card mb-6">
        <form method="GET" action="{{ route('maintenances.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search issues..." class="input flex-1" />
            <select name="status" class="select w-40" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="reported" {{ request('status') === 'reported' ? 'selected' : '' }}>Reported</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
            </select>
            <select name="priority" class="select w-40" onchange="this.form.submit()">
                <option value="">All Priorities</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            </select>
            <button type="submit" class="btn-primary">Search</button>
            @if(request('search') || request('status') || request('priority'))
                <a href="{{ route('maintenances.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    <div class="card !p-0">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Issue Title</th>
                        <th>Room</th>
                        <th>Reported By</th>
                        <th>Date</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($maintenances as $maint)
                        <tr>
                            <td class="font-medium text-charcoal">{{ $maint->title }}</td>
                            <td>Room {{ $maint->room->room_number ?? '-' }}</td>
                            <td>{{ $maint->reporter->name ?? '-' }}</td>
                            <td>{{ $maint->created_at->format('d M Y') }}</td>
                            <td>
                                @php
                                    $pt = match($maint->priority) {
                                        'urgent' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'info',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-badge :type="$pt">{{ ucfirst($maint->priority) }}</x-badge>
                            </td>
                            <td>
                                @php
                                    $st = match($maint->status) {
                                        'resolved' => 'success',
                                        'in_progress' => 'info',
                                        'reported' => 'warning',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-badge :type="$st" :dot="true">{{ str_replace('_', ' ', ucfirst($maint->status)) }}</x-badge>
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('maintenances.show', $maint) }}" class="btn-ghost btn-sm !px-2" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    @if(auth()->user()->isStaff())
                                        <a href="{{ route('maintenances.edit', $maint) }}" class="btn-ghost btn-sm !px-2" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0 border-b-0">
                                <x-empty-state 
                                    icon="folder-open" 
                                    title="No Maintenance Records Found" 
                                    message="Great! There are no reported issues at the moment."
                                    actionLabel="Report Issue"
                                    actionRoute="{{ route('maintenances.create') }}"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($maintenances->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $maintenances->links() }}</div>
        @endif
    </div>
</x-layouts.app>
