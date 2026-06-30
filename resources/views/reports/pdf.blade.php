<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .header h1 { margin: 0 0 10px 0; color: #111; }
        .header p { margin: 0; color: #666; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; color: #444; }
        .footer { text-align: center; margin-top: 50px; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 20px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
        .btn-print {
            display: inline-block; padding: 10px 20px; background: #0ea5e9; color: white; 
            text-decoration: none; border-radius: 5px; margin-bottom: 20px; cursor: pointer; border: none; font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right;">
        <button onclick="window.print()" class="btn-print">Print / Save as PDF</button>
    </div>

    <div class="header">
        <h1>Boarding House Management</h1>
        <h2>{{ $title }}</h2>
        @if($startDate && $endDate)
            <p>Period: {{ $startDate }} to {{ $endDate }}</p>
        @else
            <p>Generated on: {{ now()->format('d M Y H:i') }}</p>
        @endif
    </div>

    @if($type === 'rooms')
        <table>
            <thead>
                <tr>
                    <th>Room #</th>
                    <th>Type</th>
                    <th>Floor</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $room)
                    <tr>
                        <td>{{ $room->room_number }}</td>
                        <td>{{ ucfirst($room->type) }}</td>
                        <td>{{ $room->floor }}</td>
                        <td>Rp {{ number_format($room->price, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($room->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($type === 'tenants')
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>ID Number</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $tenant)
                    <tr>
                        <td>{{ $tenant->name }}</td>
                        <td>{{ $tenant->phone }}</td>
                        <td>{{ $tenant->id_number }}</td>
                        <td>{{ $tenant->created_at->format('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($type === 'bookings')
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Tenant</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $booking)
                    <tr>
                        <td>{{ $booking->booking_code }}</td>
                        <td>{{ $booking->tenant->name ?? '-' }}</td>
                        <td>{{ $booking->room->room_number ?? '-' }}</td>
                        <td>{{ $booking->check_in_date->format('d M Y') }}</td>
                        <td>{{ ucfirst($booking->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($type === 'payments')
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Date</th>
                    <th>Tenant</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($data as $payment)
                    @if($payment->status === 'paid') @php $total += $payment->amount; @endphp @endif
                    <tr>
                        <td>{{ $payment->payment_code }}</td>
                        <td>{{ $payment->payment_date->format('d M Y') }}</td>
                        <td>{{ $payment->tenant->name ?? '-' }}</td>
                        <td>{{ ucfirst($payment->payment_type) }}</td>
                        <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($payment->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold;">Total Paid Received:</td>
                    <td colspan="2" style="font-weight: bold;">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

    @elseif($type === 'maintenance')
        <table>
            <thead>
                <tr>
                    <th>Issue</th>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                @php $totalCost = 0; @endphp
                @foreach($data as $maint)
                    @if($maint->status === 'resolved') @php $totalCost += $maint->cost; @endphp @endif
                    <tr>
                        <td>{{ $maint->title }}</td>
                        <td>{{ $maint->room->room_number ?? '-' }}</td>
                        <td>{{ $maint->created_at->format('d M Y') }}</td>
                        <td>{{ ucfirst($maint->priority) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $maint->status)) }}</td>
                        <td>Rp {{ number_format($maint->cost, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right; font-weight: bold;">Total Maintenance Cost:</td>
                    <td style="font-weight: bold;">Rp {{ number_format($totalCost, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="footer">
        Generated by Boarding House Management System on {{ now()->format('d M Y H:i:s') }}
    </div>

    <script>
        // Auto print dialog on load
        window.onload = function() {
            // window.print();
        }
    </script>
</body>
</html>
