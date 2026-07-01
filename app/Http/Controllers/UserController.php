<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = $request->boolean('is_active', true);

        $user = User::create($data);

        ActivityLog::log('created_user', "User '{$user->name}' ({$user->role}) created", $user);

        // Dispatch notifications (Owner only)
        \App\Notifications\SystemNotification::sendToOwnerOnly(
            'User Account Created',
            "User '{$user->name}' ({$user->role}) has been created.",
            'shield',
            route('users.index')
        );

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' created successfully.");
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        ActivityLog::log('updated_user', "User '{$user->name}' updated", $user);

        // Dispatch notifications (Owner only)
        \App\Notifications\SystemNotification::sendToOwnerOnly(
            'User Account Updated',
            "User '{$user->name}' information has been updated.",
            'shield',
            route('users.index')
        );

        return redirect()->route('users.index')
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLog::log('deleted_user', "User '{$name}' deleted");

        // Dispatch notifications (Owner only)
        \App\Notifications\SystemNotification::sendToOwnerOnly(
            'User Account Deleted',
            "User '{$name}' has been deleted from the system.",
            'shield',
            route('users.index')
        );

        return redirect()->route('users.index')
            ->with('success', "User '{$name}' deleted successfully.");
    }
}
