<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Models\ActivityLog;
use App\Models\Maintenance;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Maintenance::with(['room', 'reporter']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$search}%"))
                  ->orWhereHas('reporter', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($request->user()->isResident()) {
            $query->where('reported_by', $request->user()->id);
        }

        $maintenances = $query->latest()->paginate(10)->withQueryString();

        return view('maintenances.index', compact('maintenances'));
    }

    public function create(): View
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('maintenances.create', compact('rooms'));
    }

    public function store(StoreMaintenanceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Ensure residents can only report for their current room (if enforcing)
        // Here we just let the request handle validation, and set reporter
        $data['reported_by'] = $request->user()->id;
        $data['status'] = 'reported';

        $maintenance = Maintenance::create($data);

        ActivityLog::log('created_maintenance', "Maintenance request '{$maintenance->title}' submitted for Room {$maintenance->room->room_number}", $maintenance);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'New Maintenance Request',
            "Maintenance request '{$maintenance->title}' submitted for Room {$maintenance->room->room_number}.",
            'wrench',
            route('maintenances.show', $maintenance)
        );
        if ($maintenance->reporter) {
            \App\Notifications\SystemNotification::sendToUser(
                $maintenance->reporter,
                'Maintenance Request Submitted',
                "Your request '{$maintenance->title}' has been submitted.",
                'wrench',
                route('maintenances.show', $maintenance)
            );
        }

        return redirect()->route('maintenances.index')
            ->with('success', 'Maintenance request submitted successfully.');
    }

    public function show(Maintenance $maintenance): View
    {
        $maintenance->load(['room', 'reporter']);
        return view('maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance): View
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('maintenances.edit', compact('maintenance', 'rooms'));
    }

    public function update(UpdateMaintenanceRequest $request, Maintenance $maintenance): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['status']) && $data['status'] === 'resolved' && $maintenance->status !== 'resolved') {
            $data['resolved_at'] = now();
        }

        $maintenance->update($data);

        ActivityLog::log('updated_maintenance', "Maintenance request '{$maintenance->title}' updated to {$maintenance->status}", $maintenance);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'Maintenance Request Updated',
            "Maintenance request '{$maintenance->title}' updated to {$maintenance->status}.",
            'wrench',
            route('maintenances.show', $maintenance)
        );
        if ($maintenance->reporter) {
            \App\Notifications\SystemNotification::sendToUser(
                $maintenance->reporter,
                'Maintenance Request Updated',
                "Your request '{$maintenance->title}' status is now: " . ucfirst(str_replace('_', ' ', $maintenance->status)) . ".",
                'wrench',
                route('maintenances.show', $maintenance)
            );
        }

        return redirect()->route('maintenances.show', $maintenance)
            ->with('success', 'Maintenance request updated successfully.');
    }

    public function destroy(Maintenance $maintenance): RedirectResponse
    {
        if ($maintenance->status === 'in_progress') {
            return back()->with('error', 'Cannot delete a maintenance request that is in progress.');
        }

        $title = $maintenance->title;
        $maintenance->delete();

        ActivityLog::log('deleted_maintenance', "Maintenance request '{$title}' deleted");

        return redirect()->route('maintenances.index')
            ->with('success', 'Maintenance request deleted successfully.');
    }

    public function resolve(Request $request, Maintenance $maintenance): RedirectResponse
    {
        $validated = $request->validate([
            'cost' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $maintenance->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'cost' => $validated['cost'] ?? 0,
            'notes' => $validated['notes'] ?? $maintenance->notes,
        ]);

        ActivityLog::log('resolved_maintenance', "Maintenance request '{$maintenance->title}' resolved", $maintenance);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'Maintenance Request Completed',
            "Maintenance request '{$maintenance->title}' has been resolved.",
            'wrench',
            route('maintenances.show', $maintenance)
        );
        if ($maintenance->reporter) {
            \App\Notifications\SystemNotification::sendToUser(
                $maintenance->reporter,
                'Maintenance Completed',
                "Your request '{$maintenance->title}' has been resolved/completed.",
                'wrench',
                route('maintenances.show', $maintenance)
            );
        }

        return back()->with('success', 'Maintenance request marked as resolved.');
    }
}
