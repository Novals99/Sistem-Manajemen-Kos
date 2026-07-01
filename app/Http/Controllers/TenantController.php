<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\ActivityLog;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tenant::with('user');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $tenants = $query->latest()->paginate(10)->withQueryString();

        return view('tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('tenants.create');
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('id_photo')) {
            $data['id_photo'] = $request->file('id_photo')->store('tenants/id-photos', 'public');
        }

        $tenant = Tenant::create($data);

        ActivityLog::log('created_tenant', "Tenant {$tenant->name} created", $tenant);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'New Tenant Registered',
            "Tenant {$tenant->name} has been registered in the system.",
            'users',
            route('tenants.show', $tenant)
        );

        return redirect()->route('tenants.index')
            ->with('success', "Tenant {$tenant->name} created successfully.");
    }

    public function show(Tenant $tenant): View
    {
        $tenant->load(['bookings.room', 'bookings.payments', 'payments', 'user']);

        return view('tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant): View
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('id_photo')) {
            if ($tenant->id_photo) {
                Storage::disk('public')->delete($tenant->id_photo);
            }
            $data['id_photo'] = $request->file('id_photo')->store('tenants/id-photos', 'public');
        }

        $tenant->update($data);

        ActivityLog::log('updated_tenant', "Tenant {$tenant->name} updated", $tenant);

        return redirect()->route('tenants.index')
            ->with('success', "Tenant {$tenant->name} updated successfully.");
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $activeBooking = $tenant->bookings()->where('status', 'active')->exists();

        if ($activeBooking) {
            return back()->with('error', 'Cannot delete a tenant with an active booking.');
        }

        $name = $tenant->name;
        $tenant->delete(); // soft delete

        ActivityLog::log('deleted_tenant', "Tenant {$name} deleted");

        return redirect()->route('tenants.index')
            ->with('success', "Tenant {$name} deleted successfully.");
    }
}
