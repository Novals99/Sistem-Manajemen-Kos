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

        // ── Resident Dashboard ──────────────────────────
        if ($user->isResident()) {
            return $this->residentDashboard($user);
        }

        // ── Staff Dashboard (Owner & Admin) ─────────────
        return $this->staffDashboard();
    }

    private function staffDashboard(): View
    {
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

    private function residentDashboard($user): View
    {
        $tenant = $user->tenant;

        // Current active booking & room info
        $activeBooking = $tenant
            ? $tenant->bookings()->with('room')->where('status', 'active')->first()
            : null;

        $myRoom = $activeBooking?->room?->room_number ?? '—';
        $bookingStatus = $activeBooking?->status ?? 'No Booking';
        $contractEndDate = $activeBooking?->check_out_date
            ?? ($activeBooking ? $activeBooking->check_in_date?->copy()->addMonths($activeBooking->duration_months) : null);

        // Outstanding balance
        $outstandingBalance = 0;
        if ($tenant) {
            $activeBookings = $tenant->bookings()->whereIn('status', ['active', 'pending', 'confirmed'])->get();
            foreach ($activeBookings as $booking) {
                $outstandingBalance += $booking->outstandingBalance();
            }
        }

        // Next payment due
        $nextPaymentDue = $tenant
            ? Payment::where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->orderBy('payment_date')
                ->first()
            : null;

        // Maintenance counts
        $openMaintenance = $user->maintenancesReported()
            ->whereIn('status', ['reported', 'in_progress'])->count();
        $closedMaintenance = $user->maintenancesReported()
            ->where('status', 'resolved')->count();

        // Personal tables
        $myBookings = $tenant
            ? Booking::with('room')
                ->where('tenant_id', $tenant->id)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $myPayments = $tenant
            ? Payment::with(['booking.room'])
                ->where('tenant_id', $tenant->id)
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $myMaintenances = Maintenance::with('room')
            ->where('reported_by', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'myRoom',
            'bookingStatus',
            'contractEndDate',
            'outstandingBalance',
            'nextPaymentDue',
            'openMaintenance',
            'closedMaintenance',
            'myBookings',
            'myPayments',
            'myMaintenances',
        ));
    }
}

