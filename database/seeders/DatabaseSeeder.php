<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Users ────────────────────────────────────
        $owner = User::create([
            'name' => 'Pemilik Kos',
            'email' => 'owner@kosmanager.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Admin Kos',
            'email' => 'admin@kosmanager.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567891',
            'is_active' => true,
        ]);

        $resident1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'resident',
            'phone' => '081234567892',
            'is_active' => true,
        ]);

        $resident2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'siti@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'resident',
            'phone' => '081234567893',
            'is_active' => true,
        ]);

        $resident3 = User::create([
            'name' => 'Andi Prasetyo',
            'email' => 'andi@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'resident',
            'phone' => '081234567894',
            'is_active' => true,
        ]);

        // ── Rooms ────────────────────────────────────
        $rooms = [];
        $roomData = [
            ['room_number' => '101', 'floor' => 1, 'type' => 'single', 'price' => 1500000, 'facilities' => ['AC', 'WiFi', 'Private Bathroom'], 'max_occupants' => 1],
            ['room_number' => '102', 'floor' => 1, 'type' => 'single', 'price' => 1500000, 'facilities' => ['AC', 'WiFi', 'Private Bathroom'], 'max_occupants' => 1],
            ['room_number' => '103', 'floor' => 1, 'type' => 'double', 'price' => 2000000, 'facilities' => ['AC', 'WiFi', 'Private Bathroom', 'TV'], 'max_occupants' => 2],
            ['room_number' => '104', 'floor' => 1, 'type' => 'single', 'price' => 1200000, 'facilities' => ['Fan', 'WiFi', 'Shared Bathroom'], 'max_occupants' => 1],
            ['room_number' => '201', 'floor' => 2, 'type' => 'single', 'price' => 1500000, 'facilities' => ['AC', 'WiFi', 'Private Bathroom'], 'max_occupants' => 1],
            ['room_number' => '202', 'floor' => 2, 'type' => 'double', 'price' => 2000000, 'facilities' => ['AC', 'WiFi', 'Private Bathroom', 'TV'], 'max_occupants' => 2],
            ['room_number' => '203', 'floor' => 2, 'type' => 'suite', 'price' => 3000000, 'facilities' => ['AC', 'WiFi', 'Private Bathroom', 'TV', 'Refrigerator', 'Desk'], 'max_occupants' => 2],
            ['room_number' => '204', 'floor' => 2, 'type' => 'single', 'price' => 1300000, 'facilities' => ['AC', 'WiFi', 'Shared Bathroom'], 'max_occupants' => 1],
            ['room_number' => '301', 'floor' => 3, 'type' => 'single', 'price' => 1600000, 'facilities' => ['AC', 'WiFi', 'Private Bathroom'], 'max_occupants' => 1],
            ['room_number' => '302', 'floor' => 3, 'type' => 'suite', 'price' => 3500000, 'facilities' => ['AC', 'WiFi', 'Private Bathroom', 'TV', 'Refrigerator', 'Desk', 'Balcony'], 'max_occupants' => 2],
        ];

        foreach ($roomData as $data) {
            $rooms[] = Room::create(array_merge($data, [
                'status' => 'available',
                'description' => 'Comfortable room on floor ' . $data['floor'],
            ]));
        }

        // ── Tenants ──────────────────────────────────
        $tenant1 = Tenant::create([
            'user_id' => $resident1->id,
            'name' => 'Budi Santoso',
            'email' => 'budi@gmail.com',
            'phone' => '081234567892',
            'id_number' => '3201234567890001',
            'address' => 'Jl. Merdeka No. 45, Jakarta Selatan',
            'occupation' => 'Software Developer',
            'emergency_contact' => 'Ibu Ratna',
            'emergency_phone' => '081299887766',
        ]);

        $tenant2 = Tenant::create([
            'user_id' => $resident2->id,
            'name' => 'Siti Aminah',
            'email' => 'siti@gmail.com',
            'phone' => '081234567893',
            'id_number' => '3201234567890002',
            'address' => 'Jl. Sudirman No. 12, Bandung',
            'occupation' => 'Graphic Designer',
            'emergency_contact' => 'Bapak Ahmad',
            'emergency_phone' => '081288776655',
        ]);

        $tenant3 = Tenant::create([
            'user_id' => $resident3->id,
            'name' => 'Andi Prasetyo',
            'email' => 'andi@gmail.com',
            'phone' => '081234567894',
            'id_number' => '3201234567890003',
            'address' => 'Jl. Gatot Subroto No. 78, Surabaya',
            'occupation' => 'University Student',
            'emergency_contact' => 'Ibu Dewi',
            'emergency_phone' => '081277665544',
        ]);

        // ── Bookings ─────────────────────────────────
        // Active booking 1 - Budi in Room 101
        $booking1 = Booking::create([
            'booking_code' => 'BK-20260601-001',
            'tenant_id' => $tenant1->id,
            'room_id' => $rooms[0]->id, // Room 101
            'check_in_date' => '2026-06-01',
            'check_out_date' => null,
            'duration_months' => 6,
            'monthly_rate' => 1500000,
            'deposit' => 1500000,
            'status' => 'active',
        ]);
        $rooms[0]->update(['status' => 'occupied']);

        // Active booking 2 - Siti in Room 203
        $booking2 = Booking::create([
            'booking_code' => 'BK-20260615-001',
            'tenant_id' => $tenant2->id,
            'room_id' => $rooms[6]->id, // Room 203
            'check_in_date' => '2026-06-15',
            'check_out_date' => null,
            'duration_months' => 12,
            'monthly_rate' => 3000000,
            'deposit' => 3000000,
            'status' => 'active',
        ]);
        $rooms[6]->update(['status' => 'occupied']);

        // Pending booking - Andi for Room 301
        $booking3 = Booking::create([
            'booking_code' => 'BK-20260628-001',
            'tenant_id' => $tenant3->id,
            'room_id' => $rooms[8]->id, // Room 301
            'check_in_date' => '2026-07-01',
            'check_out_date' => null,
            'duration_months' => 3,
            'monthly_rate' => 1600000,
            'deposit' => 1600000,
            'status' => 'pending',
        ]);
        $rooms[8]->update(['status' => 'reserved']);

        // ── Payments ─────────────────────────────────
        // Budi's payments
        Payment::create([
            'payment_code' => 'PAY-20260601-001',
            'booking_id' => $booking1->id,
            'tenant_id' => $tenant1->id,
            'amount' => 1500000,
            'payment_date' => '2026-06-01',
            'payment_method' => 'transfer',
            'payment_type' => 'deposit',
            'status' => 'paid',
            'period_month' => 6,
            'period_year' => 2026,
        ]);

        Payment::create([
            'payment_code' => 'PAY-20260601-002',
            'booking_id' => $booking1->id,
            'tenant_id' => $tenant1->id,
            'amount' => 1500000,
            'payment_date' => '2026-06-01',
            'payment_method' => 'transfer',
            'payment_type' => 'rent',
            'status' => 'paid',
            'period_month' => 6,
            'period_year' => 2026,
        ]);

        // Siti's payments
        Payment::create([
            'payment_code' => 'PAY-20260615-001',
            'booking_id' => $booking2->id,
            'tenant_id' => $tenant2->id,
            'amount' => 3000000,
            'payment_date' => '2026-06-15',
            'payment_method' => 'e-wallet',
            'payment_type' => 'deposit',
            'status' => 'paid',
            'period_month' => 6,
            'period_year' => 2026,
        ]);

        Payment::create([
            'payment_code' => 'PAY-20260615-002',
            'booking_id' => $booking2->id,
            'tenant_id' => $tenant2->id,
            'amount' => 3000000,
            'payment_date' => '2026-06-15',
            'payment_method' => 'e-wallet',
            'payment_type' => 'rent',
            'status' => 'paid',
            'period_month' => 6,
            'period_year' => 2026,
        ]);

        // Pending payment - July rent for Budi
        Payment::create([
            'payment_code' => 'PAY-20260630-001',
            'booking_id' => $booking1->id,
            'tenant_id' => $tenant1->id,
            'amount' => 1500000,
            'payment_date' => '2026-07-01',
            'payment_method' => 'cash',
            'payment_type' => 'rent',
            'status' => 'pending',
            'period_month' => 7,
            'period_year' => 2026,
        ]);

        // ── Maintenance ──────────────────────────────
        \App\Models\Maintenance::create([
            'room_id' => $rooms[0]->id,
            'reported_by' => $resident1->id,
            'title' => 'AC Not Cooling Properly',
            'description' => 'The air conditioner in Room 101 is not cooling properly. It runs but only blows warm air.',
            'category' => 'appliance',
            'priority' => 'high',
            'status' => 'reported',
        ]);

        \App\Models\Maintenance::create([
            'room_id' => $rooms[6]->id,
            'reported_by' => $resident2->id,
            'title' => 'Leaky Faucet',
            'description' => 'The bathroom faucet in Room 203 has been dripping constantly.',
            'category' => 'plumbing',
            'priority' => 'medium',
            'status' => 'in_progress',
        ]);

        // ── Output ───────────────────────────────────
        $this->command->info('');
        $this->command->info('🏠 KosManager Database Seeded Successfully!');
        $this->command->info('');
        $this->command->info('Login Credentials:');
        $this->command->info('┌─────────────┬─────────────────────────┬──────────┐');
        $this->command->info('│ Role        │ Email                   │ Password │');
        $this->command->info('├─────────────┼─────────────────────────┼──────────┤');
        $this->command->info('│ Owner       │ owner@kosmanager.com    │ password │');
        $this->command->info('│ Admin       │ admin@kosmanager.com    │ password │');
        $this->command->info('│ Resident    │ budi@gmail.com          │ password │');
        $this->command->info('└─────────────┴─────────────────────────┴──────────┘');
        $this->command->info('');
    }
}
