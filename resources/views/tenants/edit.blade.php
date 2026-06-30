<x-layouts.app :title="'Edit ' . $tenant->name">
    <div class="mb-6">
        <a href="{{ route('tenants.index') }}" class="inline-flex items-center gap-1 text-sm text-cool-gray hover:text-charcoal transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Tenants
        </a>
        <h2 class="text-lg font-bold text-charcoal mt-2">Edit {{ $tenant->name }}</h2>
        <p class="text-sm text-cool-gray">Update tenant information</p>
    </div>
    <div class="card">
        <form method="POST" action="{{ route('tenants.update', $tenant) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('tenants._form', ['tenant' => $tenant])
            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Tenant
                </button>
                <a href="{{ route('tenants.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.app>
