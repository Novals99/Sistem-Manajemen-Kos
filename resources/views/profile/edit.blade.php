<x-layouts.app :title="'My Profile'">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-charcoal">My Profile</h2>
        <p class="text-sm text-cool-gray">Manage your personal information and security settings</p>
    </div>

    {{-- Error messages --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-danger-50 border border-danger-500/20 text-danger-600 text-sm">
            <p class="font-bold mb-1">Please fix the following validation errors:</p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Photo Upload & Information --}}
        <div class="lg:col-span-2 space-y-6">
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="font-bold text-charcoal mb-1">Profile Information</h3>
                    <p class="text-xs text-cool-gray">Update your personal account details</p>
                </div>

                {{-- Avatar Section --}}
                <div class="flex flex-col sm:flex-row items-center gap-6 pb-6 border-b border-border-theme/40">
                    <div class="w-20 h-20 rounded-full overflow-hidden bg-primary-100 border border-border-theme/60 flex items-center justify-center flex-shrink-0 relative shadow-sm">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover" />
                        @else
                            <span class="text-2xl font-bold text-primary-600">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 space-y-3">
                        <label class="block text-xs font-semibold text-charcoal" for="avatar">Upload Profile Photo</label>
                        <input type="file" id="avatar" name="avatar" class="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 text-xs text-cool-gray cursor-pointer" accept="image/jpeg,image/png,image/jpg,image/webp" />
                        <p class="text-[10px] text-cool-gray">Supports JPG, PNG, or WEBP up to 2MB</p>
                        
                        @if(auth()->user()->avatar)
                            <label class="flex items-center gap-2 text-xs text-danger-500 font-semibold cursor-pointer mt-2">
                                <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-danger-500 focus:ring-danger-500/20" />
                                Remove current photo
                            </label>
                        @endif
                    </div>
                </div>

                {{-- Input Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="label">Full Name <span class="text-danger-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="input" required />
                    </div>

                    <div>
                        <label for="email" class="label">Email Address <span class="text-danger-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="input" required />
                    </div>

                    <div>
                        <label for="phone" class="label">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="input" placeholder="e.g. 081234567890" />
                    </div>

                    <div>
                        <label for="gender" class="label">Gender</label>
                        <select id="gender" name="gender" class="select">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="label">Address</label>
                        <textarea id="address" name="address" rows="3" class="input" placeholder="Origin or current address...">{{ old('address', $user->address) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-theme/40">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <a href="{{ route('dashboard') }}" class="btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        {{-- Account Meta & Password Security --}}
        <div class="space-y-6">
            {{-- Account Information Meta (Read-only) --}}
            <div class="card space-y-4">
                <div>
                    <h3 class="font-bold text-charcoal mb-1">Account Info</h3>
                    <p class="text-xs text-cool-gray">Your system permissions level</p>
                </div>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center pb-2 border-b border-border-theme/40">
                        <span class="text-xs text-cool-gray">Role</span>
                        <x-badge type="primary">{{ ucfirst($user->role) }}</x-badge>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-cool-gray">Registered</span>
                        <span class="font-medium text-charcoal">{{ $user->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Change Password --}}
            <form method="POST" action="{{ route('profile.password') }}" class="card space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="font-bold text-charcoal mb-1">Security</h3>
                    <p class="text-xs text-cool-gray">Change your account password securely</p>
                </div>

                <div>
                    <label for="current_password" class="label">Current Password <span class="text-danger-500">*</span></label>
                    <input type="password" id="current_password" name="current_password" class="input" required />
                </div>

                <div>
                    <label for="new_password" class="label">New Password <span class="text-danger-500">*</span></label>
                    <input type="password" id="new_password" name="new_password" class="input" placeholder="Min. 8 characters" required />
                </div>

                <div>
                    <label for="new_password_confirmation" class="label">Confirm New Password <span class="text-danger-500">*</span></label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="input" required />
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-border-theme/40">
                    <button type="submit" class="btn-primary w-full">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
