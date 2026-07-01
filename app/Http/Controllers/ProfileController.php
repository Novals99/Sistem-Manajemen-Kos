<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(): View
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user profile information.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        // Handle avatar removal
        if ($request->boolean('remove_avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = null;
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        unset($data['remove_avatar']);

        $user->update($data);

        ActivityLog::log('updated_profile', "User '{$user->name}' updated their profile details", $user);

        // Also trigger a system notification so it's fully integrated
        \App\Notifications\SystemNotification::sendToUser(
            $user,
            'Profile Updated',
            'Your profile details have been successfully updated.',
            'users',
            route('profile.edit')
        );

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user password.
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->input('new_password')),
        ]);

        ActivityLog::log('changed_password', "User '{$user->name}' updated their account password", $user);

        // Trigger a system notification
        \App\Notifications\SystemNotification::sendToUser(
            $user,
            'Password Changed',
            'Your account password has been updated successfully.',
            'shield',
            route('profile.edit')
        );

        return redirect()->route('profile.edit')
            ->with('success', 'Password changed successfully.');
    }
}
