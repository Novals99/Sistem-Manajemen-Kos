<x-layouts.app :title="'Reports'">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-charcoal">Reports</h2>
        <p class="text-sm text-cool-gray">Generate and export system reports</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="font-bold text-charcoal mb-4">Generate Report</h3>
            <form action="{{ route('reports.export.pdf') }}" method="GET" target="_blank">
                <div class="space-y-4">
                    <div>
                        <label for="type" class="label">Report Type <span class="text-danger-500">*</span></label>
                        <select id="type" name="type" class="select" required>
                            <option value="rooms">Rooms Status Report</option>
                            <option value="tenants">Tenants Directory</option>
                            <option value="bookings">Bookings History</option>
                            <option value="payments">Financial Payments</option>
                            <option value="maintenance">Maintenance Issues</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="label">Start Date (Optional)</label>
                            <input type="date" id="start_date" name="start_date" class="input" />
                        </div>
                        <div>
                            <label for="end_date" class="label">End Date (Optional)</label>
                            <input type="date" id="end_date" name="end_date" class="input" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="btn-primary w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Generate Printable PDF
                    </button>
                    {{-- Note: Excel requires external package --}}
                    <button type="button" onclick="alert('Excel export feature is coming soon!')" class="btn-outline w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Excel
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            <div class="p-4 rounded-xl border border-gray-100 bg-surface flex items-start gap-4">
                <div class="p-3 bg-primary-50 rounded-lg text-primary-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h4 class="font-semibold text-charcoal">Financial Report</h4>
                    <p class="text-sm text-cool-gray">Detailed view of all received payments, pending dues, and penalties.</p>
                </div>
            </div>
            <div class="p-4 rounded-xl border border-gray-100 bg-surface flex items-start gap-4">
                <div class="p-3 bg-success-50 rounded-lg text-success-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h4 class="font-semibold text-charcoal">Occupancy Status</h4>
                    <p class="text-sm text-cool-gray">Current room availability, upcoming check-outs, and maintenance blocks.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
