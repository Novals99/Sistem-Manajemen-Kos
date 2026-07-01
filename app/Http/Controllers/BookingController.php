<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::with(['tenant', 'room']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('tenant', fn($t) => $t->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Residents can only see their own bookings
        if ($request->user()->isResident()) {
            $tenant = $request->user()->tenant;
            $query->where('tenant_id', $tenant?->id ?? 0);
        }

        $bookings = $query->latest()->paginate(10)->withQueryString();

        return view('bookings.index', compact('bookings'));
    }

    public function create(): View
    {
        $rooms = Room::where('status', 'available')->orderBy('room_number')->get();
        $tenants = Tenant::orderBy('name')->get();

        return view('bookings.create', compact('rooms', 'tenants'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['booking_code'] = Booking::generateBookingCode();

        $room = Room::findOrFail($data['room_id']);
        $data['monthly_rate'] = $room->price;

        $booking = Booking::create($data);

        // Reserve the room
        $room->update(['status' => 'reserved']);

        ActivityLog::log('created_booking', "Booking {$booking->booking_code} created for {$booking->tenant->name}", $booking);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'New Booking Created',
            "Booking {$booking->booking_code} has been created for {$booking->tenant->name}.",
            'calendar',
            route('bookings.show', $booking)
        );
        if ($booking->tenant && $booking->tenant->user) {
            \App\Notifications\SystemNotification::sendToUser(
                $booking->tenant->user,
                'Booking Created',
                "Your booking {$booking->booking_code} for Room {$room->room_number} is pending approval.",
                'calendar',
                route('bookings.show', $booking)
            );
        }

        return redirect()->route('bookings.index')
            ->with('success', "Booking {$booking->booking_code} created successfully.");
    }

    public function show(Booking $booking): View
    {
        $booking->load(['tenant', 'room', 'payments']);

        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking): View
    {
        $rooms = Room::where('status', 'available')
            ->orWhere('id', $booking->room_id)
            ->orderBy('room_number')
            ->get();
        $tenants = Tenant::orderBy('name')->get();

        return view('bookings.edit', compact('booking', 'rooms', 'tenants'));
    }

    public function update(UpdateBookingRequest $request, Booking $booking): RedirectResponse
    {
        $data = $request->validated();

        // If room changed, update room statuses
        if (isset($data['room_id']) && $data['room_id'] != $booking->room_id) {
            $booking->room->update(['status' => 'available']);
            Room::findOrFail($data['room_id'])->update(['status' => $booking->status === 'active' ? 'occupied' : 'reserved']);
            $data['monthly_rate'] = Room::findOrFail($data['room_id'])->price;
        }

        $booking->update($data);

        ActivityLog::log('updated_booking', "Booking {$booking->booking_code} updated", $booking);

        return redirect()->route('bookings.show', $booking)
            ->with('success', "Booking {$booking->booking_code} updated successfully.");
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        if ($booking->status === 'active') {
            return back()->with('error', 'Cannot delete an active booking. Check out first.');
        }

        $booking->room->update(['status' => 'available']);
        $code = $booking->booking_code;
        $booking->delete();

        ActivityLog::log('deleted_booking', "Booking {$code} deleted");

        return redirect()->route('bookings.index')
            ->with('success', "Booking {$code} deleted successfully.");
    }

    // ── Custom Actions ───────────────────────────────

    public function checkIn(Booking $booking): RedirectResponse
    {
        if (! in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed bookings can be checked in.');
        }

        $booking->update([
            'status' => 'active',
            'check_in_date' => now()->toDateString(),
        ]);
        $booking->room->update(['status' => 'occupied']);

        ActivityLog::log('check_in', "{$booking->tenant->name} checked into Room {$booking->room->room_number}", $booking);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'Tenant Checked In',
            "{$booking->tenant->name} has checked into Room {$booking->room->room_number}.",
            'calendar',
            route('bookings.show', $booking)
        );
        if ($booking->tenant && $booking->tenant->user) {
            \App\Notifications\SystemNotification::sendToUser(
                $booking->tenant->user,
                'Booking Approved',
                "Your booking {$booking->booking_code} has been approved. Welcome to Room {$booking->room->room_number}!",
                'calendar',
                route('bookings.show', $booking)
            );
        }

        return back()->with('success', "{$booking->tenant->name} has been checked in to Room {$booking->room->room_number}.");
    }

    public function checkOut(Booking $booking): RedirectResponse
    {
        if ($booking->status !== 'active') {
            return back()->with('error', 'Only active bookings can be checked out.');
        }

        $booking->update([
            'status' => 'completed',
            'check_out_date' => now()->toDateString(),
        ]);
        $booking->room->update(['status' => 'available']);

        ActivityLog::log('check_out', "{$booking->tenant->name} checked out of Room {$booking->room->room_number}", $booking);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'Tenant Checked Out',
            "{$booking->tenant->name} has checked out of Room {$booking->room->room_number}.",
            'calendar',
            route('bookings.show', $booking)
        );
        if ($booking->tenant && $booking->tenant->user) {
            \App\Notifications\SystemNotification::sendToUser(
                $booking->tenant->user,
                'Checked Out',
                "You have been checked out of Room {$booking->room->room_number}.",
                'calendar',
                route('bookings.show', $booking)
            );
        }

        return back()->with('success', "{$booking->tenant->name} has been checked out of Room {$booking->room->room_number}.");
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);
        $booking->room->update(['status' => 'available']);

        ActivityLog::log('cancelled_booking', "Booking {$booking->booking_code} cancelled", $booking);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'Booking Cancelled',
            "Booking {$booking->booking_code} has been cancelled.",
            'calendar',
            route('bookings.show', $booking)
        );
        if ($booking->tenant && $booking->tenant->user) {
            \App\Notifications\SystemNotification::sendToUser(
                $booking->tenant->user,
                'Booking Cancelled',
                "Your booking {$booking->booking_code} has been cancelled.",
                'calendar',
                route('bookings.show', $booking)
            );
        }

        return back()->with('success', "Booking {$booking->booking_code} has been cancelled.");
    }
}
