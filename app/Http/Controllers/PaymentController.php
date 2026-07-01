<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::with(['tenant', 'booking.room']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_code', 'like', "%{$search}%")
                  ->orWhereHas('tenant', fn($t) => $t->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($type = $request->input('payment_type')) {
            $query->where('payment_type', $type);
        }

        if ($request->user()->isResident()) {
            $tenant = $request->user()->tenant;
            $query->where('tenant_id', $tenant?->id ?? 0);
        }

        $payments = $query->latest()->paginate(10)->withQueryString();

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request): View
    {
        $bookings = Booking::with(['tenant', 'room'])
            ->whereIn('status', ['active', 'confirmed', 'pending'])
            ->get();

        $selectedBooking = $request->input('booking_id')
            ? Booking::with(['tenant', 'room'])->find($request->input('booking_id'))
            : null;

        return view('payments.create', compact('bookings', 'selectedBooking'));
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['payment_code'] = Payment::generatePaymentCode();

        $booking = Booking::findOrFail($data['booking_id']);
        $data['tenant_id'] = $booking->tenant_id;

        if ($request->hasFile('proof_of_payment')) {
            $data['proof_of_payment'] = $request->file('proof_of_payment')->store('payments/proofs', 'public');
        }

        $payment = Payment::create($data);

        ActivityLog::log('payment_created', "Payment {$payment->payment_code} recorded — Rp " . number_format($payment->amount, 0, ',', '.'), $payment);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            $payment->status === 'paid' ? 'Payment Received' : 'Payment Pending',
            "Payment {$payment->payment_code} of Rp " . number_format($payment->amount, 0, ',', '.') . " recorded for {$payment->tenant->name}.",
            'credit-card',
            route('payments.show', $payment)
        );
        if ($payment->tenant && $payment->tenant->user) {
            \App\Notifications\SystemNotification::sendToUser(
                $payment->tenant->user,
                $payment->status === 'paid' ? 'Payment Received' : 'Payment Submitted',
                "Your payment {$payment->payment_code} of Rp " . number_format($payment->amount, 0, ',', '.') . " is {$payment->status}.",
                'credit-card',
                route('payments.show', $payment)
            );
        }

        return redirect()->route('payments.index')
            ->with('success', "Payment {$payment->payment_code} recorded successfully.");
    }

    public function show(Payment $payment): View
    {
        $payment->load(['tenant', 'booking.room']);

        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment): View
    {
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,failed,refunded'],
            'payment_method' => ['sometimes', 'in:cash,transfer,e-wallet'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('proof_of_payment')) {
            if ($payment->proof_of_payment) {
                Storage::disk('public')->delete($payment->proof_of_payment);
            }
            $validated['proof_of_payment'] = $request->file('proof_of_payment')->store('payments/proofs', 'public');
        }

        $payment->update($validated);

        ActivityLog::log('payment_updated', "Payment {$payment->payment_code} status changed to {$payment->status}", $payment);

        // Dispatch notifications
        \App\Notifications\SystemNotification::sendToOwnerAndAdmin(
            'Payment Status Updated',
            "Payment {$payment->payment_code} status changed to {$payment->status}.",
            'credit-card',
            route('payments.show', $payment)
        );
        if ($payment->tenant && $payment->tenant->user) {
            \App\Notifications\SystemNotification::sendToUser(
                $payment->tenant->user,
                'Payment Status Updated',
                "Your payment {$payment->payment_code} status has been updated to " . ucfirst($payment->status) . ".",
                'credit-card',
                route('payments.show', $payment)
            );
        }

        return redirect()->route('payments.show', $payment)
            ->with('success', "Payment {$payment->payment_code} updated successfully.");
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        if ($payment->status === 'paid') {
            return back()->with('error', 'Cannot delete a paid payment. Refund it first.');
        }

        $code = $payment->payment_code;
        $payment->delete();

        ActivityLog::log('payment_deleted', "Payment {$code} deleted");

        return redirect()->route('payments.index')
            ->with('success', "Payment {$code} deleted successfully.");
    }
}
