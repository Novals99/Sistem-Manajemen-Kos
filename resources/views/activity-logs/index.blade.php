<x-layouts.app :title="'Activity Logs'">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-charcoal">System Activity Log</h2>
        <p class="text-sm text-cool-gray">Audit trail of all actions performed in the system</p>
    </div>

    <div class="card mb-6">
        <form method="GET" action="{{ route('activity-logs.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description, event, or user..." class="input flex-1" />
            <input type="date" name="date" value="{{ request('date') }}" class="input w-48" />
            
            <button type="submit" class="btn-primary">Search</button>
            <button type="submit" name="export" value="pdf" formtarget="_blank" class="btn-secondary">Export PDF</button>
            
            @if(request('search') || request('date'))
                <a href="{{ route('activity-logs.index') }}" class="btn-outline">Reset</a>
            @endif
        </form>
    </div>

    <div class="card !p-0">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Event</th>
                        <th>Description</th>
                        <th>Model</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-xs text-cool-gray whitespace-nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="font-medium text-charcoal">{{ $log->user->name ?? 'System / Guest' }}</td>
                            <td>
                                @if($log->user)
                                    <span class="badge badge-gray text-[10px] uppercase tracking-wider">{{ $log->user->role }}</span>
                                @else
                                    <span class="text-cool-gray">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $eventColor = match($log->event) {
                                        'created' => 'badge-success',
                                        'updated' => 'badge-info',
                                        'deleted' => 'badge-danger',
                                        default => 'badge-gray',
                                    };
                                @endphp
                                <span class="badge {{ $eventColor }} font-mono">{{ $log->event }}</span>
                            </td>
                            <td class="text-sm font-medium">{{ $log->description }}</td>
                            <td class="text-xs text-cool-gray">{{ $log->subject_type ? class_basename($log->subject_type) . ' #' . $log->subject_id : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-0 border-b-0">
                                <x-empty-state 
                                    icon="search" 
                                    title="No Activity Logs Found" 
                                    message="No system activity matches your current filters."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $logs->links() }}</div>
        @endif
    </div>
</x-layouts.app>
