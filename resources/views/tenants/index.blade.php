<x-layouts.app :title="'Tenants'">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-charcoal">Tenant Management</h2>
            <p class="text-sm text-cool-gray">Manage all boarding house tenants</p>
        </div>
        <a href="{{ route('tenants.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Tenant
        </a>
    </div>

    {{-- Search --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('tenants.index') }}" class="flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone, or ID..." class="input flex-1" />
            <button type="submit" class="btn-primary">Search</button>
            @if(request('search'))
                <a href="{{ route('tenants.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="card !p-0">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>ID Number</th>
                        <th>Occupation</th>
                        <th>Current Room</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tenants as $tenant)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-charcoal">{{ $tenant->name }}</p>
                                        <p class="text-xs text-cool-gray">{{ $tenant->email ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $tenant->phone }}</td>
                            <td class="font-mono text-xs">{{ $tenant->id_number }}</td>
                            <td>{{ $tenant->occupation ?? '-' }}</td>
                            <td>
                                @php $currentRoom = $tenant->currentRoom(); @endphp
                                @if($currentRoom)
                                    <x-badge type="success" :dot="true">Room {{ $currentRoom->room_number }}</x-badge>
                                @else
                                    <span class="text-xs text-cool-gray">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('tenants.show', $tenant) }}" class="btn-ghost btn-sm !px-2" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('tenants.edit', $tenant) }}" class="btn-ghost btn-sm !px-2" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('Delete tenant {{ $tenant->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-ghost btn-sm !px-2 text-danger-500 hover:text-danger-600" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0 border-b-0">
                                <x-empty-state 
                                    icon="users" 
                                    title="No Tenants Found" 
                                    message="You haven't added any tenants yet. Click below to register one."
                                    actionLabel="Register Tenant"
                                    actionRoute="{{ route('tenants.create') }}"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tenants->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $tenants->links() }}</div>
        @endif
    </div>

</x-layouts.app>
