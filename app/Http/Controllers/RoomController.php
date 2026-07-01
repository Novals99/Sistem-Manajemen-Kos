<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\ActivityLog;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $query = Room::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('room_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        if ($floor = $request->input('floor')) {
            $query->where('floor', $floor);
        }

        $rooms = $query->orderBy('room_number')->paginate(10)->withQueryString();
        $floors = Room::distinct()->pluck('floor')->sort();

        return view('rooms.index', compact('rooms', 'floors'));
    }

    public function create(): View
    {
        return view('rooms.create');
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['facilities'] = $request->input('facilities', []);

        $room = Room::create($data);

        ActivityLog::log('created_room', "Room {$room->room_number} created", $room);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'New Room Created',
            "Room {$room->room_number} (Floor {$room->floor}) has been created.",
            'building',
            route('rooms.show', $room)
        );

        return redirect()->route('rooms.index')
            ->with('success', "Room {$room->room_number} created successfully.");
    }

    public function show(Room $room): View
    {
        $room->load(['bookings.tenant', 'bookings.payments', 'maintenances.reporter']);

        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $data = $request->validated();
        $data['facilities'] = $request->input('facilities', []);

        $room->update($data);

        ActivityLog::log('updated_room', "Room {$room->room_number} updated", $room);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'Room Updated',
            "Room {$room->room_number} information has been updated.",
            'building',
            route('rooms.show', $room)
        );

        return redirect()->route('rooms.index')
            ->with('success', "Room {$room->room_number} updated successfully.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        if ($room->status === 'occupied') {
            return back()->with('error', 'Cannot delete an occupied room.');
        }

        $roomNumber = $room->room_number;
        $room->delete();

        ActivityLog::log('deleted_room', "Room {$roomNumber} deleted");

        return redirect()->route('rooms.index')
            ->with('success', "Room {$roomNumber} deleted successfully.");
    }
}
