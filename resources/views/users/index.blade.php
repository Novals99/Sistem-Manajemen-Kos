<x-layouts.app :title="'User Management'">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-bold text-charcoal">User Management</h2>
            <p class="text-sm text-cool-gray">Manage system users and their roles</p>
        </div>
        <button @click="$dispatch('open-modal-create-user')" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add User
        </button>
    </div>

    {{-- Search & Filter --}}
    <div class="card mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." class="input flex-1" />
            <select name="role" class="input w-full sm:w-44" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="resident" {{ request('role') === 'resident' ? 'selected' : '' }}>Resident</option>
            </select>
            <button type="submit" class="btn-primary">Search</button>
            @if(request('search') || request('role'))
                <a href="{{ route('users.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Users Table --}}
    <div class="card !p-0">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-semibold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-charcoal">{{ $user->name }}</p>
                                        <p class="text-xs text-cool-gray">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleType = match($user->role) {
                                        'owner' => 'primary',
                                        'admin' => 'info',
                                        'resident' => 'gray',
                                        default => 'gray',
                                    };
                                @endphp
                                <x-badge :type="$roleType" :dot="true">{{ ucfirst($user->role) }}</x-badge>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <x-badge type="success" :dot="true">Active</x-badge>
                                @else
                                    <x-badge type="danger" :dot="true">Inactive</x-badge>
                                @endif
                            </td>
                            <td class="text-sm text-cool-gray">{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <button @click="$dispatch('open-modal-edit-user-{{ $user->id }}')" class="btn-ghost btn-sm !px-2" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete user {{ $user->name }}? This action cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost btn-sm !px-2 text-danger-500 hover:text-danger-600" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-0 border-b-0">
                                <x-empty-state 
                                    icon="users" 
                                    title="No Users Found" 
                                    message="No users match your search criteria."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $users->links() }}</div>
        @endif
    </div>

    {{-- Create User Modal --}}
    <x-modal name="create-user" title="Create New User" maxWidth="lg">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="label" for="create-name">Full Name</label>
                <input type="text" name="name" id="create-name" class="input w-full" placeholder="Enter full name" required />
            </div>
            <div>
                <label class="label" for="create-email">Email Address</label>
                <input type="email" name="email" id="create-email" class="input w-full" placeholder="Enter email address" required />
            </div>
            <div>
                <label class="label" for="create-password">Password</label>
                <input type="password" name="password" id="create-password" class="input w-full" placeholder="Minimum 8 characters" required minlength="8" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label" for="create-role">Role</label>
                    <select name="role" id="create-role" class="input w-full" required>
                        <option value="resident">Resident</option>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="create-phone">Phone</label>
                    <input type="text" name="phone" id="create-phone" class="input w-full" placeholder="08xxxxxxxxxx" />
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0" />
                <input type="checkbox" name="is_active" value="1" id="create-is-active" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked />
                <label for="create-is-active" class="text-sm text-charcoal">Active account</label>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="$dispatch('close-modal-create-user')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create User</button>
            </div>
        </form>
    </x-modal>

    {{-- Edit User Modals --}}
    @foreach($users as $user)
        <x-modal name="edit-user-{{ $user->id }}" title="Edit User" maxWidth="lg">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="label" for="edit-name-{{ $user->id }}">Full Name</label>
                    <input type="text" name="name" id="edit-name-{{ $user->id }}" class="input w-full" value="{{ $user->name }}" required />
                </div>
                <div>
                    <label class="label" for="edit-email-{{ $user->id }}">Email Address</label>
                    <input type="email" name="email" id="edit-email-{{ $user->id }}" class="input w-full" value="{{ $user->email }}" required />
                </div>
                <div>
                    <label class="label" for="edit-password-{{ $user->id }}">New Password</label>
                    <input type="password" name="password" id="edit-password-{{ $user->id }}" class="input w-full" placeholder="Leave blank to keep current" minlength="8" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label" for="edit-role-{{ $user->id }}">Role</label>
                        <select name="role" id="edit-role-{{ $user->id }}" class="input w-full" required>
                            <option value="resident" {{ $user->role === 'resident' ? 'selected' : '' }}>Resident</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="owner" {{ $user->role === 'owner' ? 'selected' : '' }}>Owner</option>
                        </select>
                    </div>
                    <div>
                        <label class="label" for="edit-phone-{{ $user->id }}">Phone</label>
                        <input type="text" name="phone" id="edit-phone-{{ $user->id }}" class="input w-full" value="{{ $user->phone }}" />
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" name="is_active" value="1" id="edit-is-active-{{ $user->id }}" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ $user->is_active ? 'checked' : '' }} />
                    <label for="edit-is-active-{{ $user->id }}" class="text-sm text-charcoal">Active account</label>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="$dispatch('close-modal-edit-user-{{ $user->id }}')" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Update User</button>
                </div>
            </form>
        </x-modal>
    @endforeach

</x-layouts.app>
