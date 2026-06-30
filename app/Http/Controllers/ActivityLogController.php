<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('event', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($date = $request->input('date')) {
            $query->whereDate('created_at', $date);
        }

        // Handle Export
        if ($request->has('export') && $request->export === 'pdf') {
            $logs = $query->latest()->get();
            return view('activity-logs.pdf', compact('logs', 'date'));
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        return view('activity-logs.index', compact('logs'));
    }
}
