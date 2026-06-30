<x-layouts.app :title="'Rooms'">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-charcoal">Room Management</h2>
            <p class="text-sm text-cool-gray">Manage all boarding house rooms</p>
        </div>
        <a href="{{ route('rooms.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Room
        </a>
    </div>

    {{-- Filters --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('rooms.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search rooms..." class="input" />
            </div>
            <div>
                <select name="status" class="select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    @foreach(['available', 'occupied', 'maintenance', 'reserved'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="type" class="select" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach(['single', 'double', 'suite'] as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="floor" class="select" onchange="this.form.submit()">
                    <option value="">All Floors</option>
                    @foreach($floors as $f)
                        <option value="{{ $f }}" {{ request('floor') == $f ? 'selected' : '' }}>Floor {{ $f }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-primary flex-1">Filter</button>
                <a href="{{ route('rooms.index') }}" class="btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $total = \App\Models\Room::count();
            $available = \App\Models\Room::where('status','available')->count();
            $occupied = \App\Models\Room::where('status','occupied')->count();
            $maint = \App\Models\Room::where('status','maintenance')->count();
        @endphp
        <div class="card !py-4 text-center">
            <p class="text-2xl font-bold text-charcoal">{{ $total }}</p>
            <p class="text-xs text-cool-gray">Total Rooms</p>
        </div>
        <div class="card !py-4 text-center">
            <p class="text-2xl font-bold text-success-600">{{ $available }}</p>
            <p class="text-xs text-cool-gray">Available</p>
        </div>
        <div class="card !py-4 text-center">
            <p class="text-2xl font-bold text-primary-600">{{ $occupied }}</p>
            <p class="text-xs text-cool-gray">Occupied</p>
        </div>
        <div class="card !py-4 text-center">
            <p class="text-2xl font-bold text-warning-600">{{ $maint }}</p>
            <p class="text-xs text-cool-gray">Maintenance</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="card !p-0">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Room #</th>
                        <th>Floor</th>
                        <th>Type</th>
                        <th>Price / Month</th>
                        <th>Facilities</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td class="font-semibold">{{ $room->room_number }}</td>
                            <td>{{ $room->floor }}</td>
                            <td>{{ ucfirst($room->type) }}</td>
                            <td class="font-semibold">Rp {{ number_format($room->price, 0, ',', '.') }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(($room->facilities ?? []) as $facility)
                                        <span class="badge-gray !text-[10px] !px-1.5 !py-0.5">{{ $facility }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusType = match($room->status) {
                                        'available' => 'success',
                                        'occupied' => 'primary',
                                        'maintenance' => 'warning',
                                        'reserved' => 'info',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-badge :type="$statusType" :dot="true">{{ ucfirst($room->status) }}</x-badge>
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('rooms.show', $room) }}" class="btn-ghost btn-sm !px-2" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('rooms.edit', $room) }}" class="btn-ghost btn-sm !px-2" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Delete room {{ $room->room_number }}?')">
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
                            <td colspan="7" class="p-0 border-b-0">
                                <x-empty-state 
                                    icon="folder-open" 
                                    title="No Rooms Found" 
                                    message="Get started by adding your first room to the boarding house."
                                    actionLabel="Add Room"
                                    actionRoute="{{ route('rooms.create') }}"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rooms->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $rooms->links() }}
            </div>
        @endif
    </div>

</x-layouts.app>
