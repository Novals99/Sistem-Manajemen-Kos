<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Maintenance;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        // Common stats
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $totalTenants = Tenant::count();

        // Financial stats (current month)
        $currentMonthRevenue = Payment::where('status', 'paid')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $pendingPayments = Payment::where('status', 'pending')->count();

        // Active bookings
        $activeBookings = Booking::where('status', 'active')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        // Maintenance
        $pendingMaintenance = Maintenance::whereIn('status', ['reported', 'in_progress'])->count();

        // Recent bookings
        $recentBookings = Booking::with(['tenant', 'room'])
            ->latest()
            ->take(5)
            ->get();

        // Recent payments
        $recentPayments = Payment::with(['tenant', 'booking'])
            ->latest()
            ->take(5)
            ->get();

        // Occupancy rate
        $occupancyRate = $totalRooms > 0
            ? round(($occupiedRooms / $totalRooms) * 100, 1)
            : 0;

        return view('dashboard', compact(
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'totalTenants',
            'currentMonthRevenue',
            'pendingPayments',
            'activeBookings',
            'pendingBookings',
            'pendingMaintenance',
            'recentBookings',
            'recentPayments',
            'occupancyRate',
        ));
    }
}
