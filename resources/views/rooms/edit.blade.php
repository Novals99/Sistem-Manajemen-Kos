<x-layouts.app :title="'Edit Room ' . $room->room_number">

    <div class="mb-6">
        <a href="{{ route('rooms.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Rooms
        </a>
        <h2 class="text-lg font-bold text-charcoal mt-2">Edit Room {{ $room->room_number }}</h2>
        <p class="text-sm text-cool-gray">Update room information</p>
    </div>

    <div class="card">
        <form method="POST" action="{{ route('rooms.update', $room) }}">
            @csrf
            @method('PUT')

            @include('rooms._form', ['room' => $room])

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Room
                </button>
                <a href="{{ route('rooms.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

</x-layouts.app>
