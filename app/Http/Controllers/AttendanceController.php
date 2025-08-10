<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Setting;

class AttendanceController extends Controller
{
    private array $whitelistedIps;

    // Constructor to initialize whitelisted IPs
    public function __construct()
    {
        $whitelistedIps = Setting::where('key', 'site.whitelisted_ip_addresses')->first();
        $this->whitelistedIps = $whitelistedIps ? explode(',', $whitelistedIps->value) : [];
    }

    // Display the attendance page
    public function index(Request $request)
    {
        $ip = $request->ip();
        $user = Auth::user();
        $today = now()->toDateString();
        $existing = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        $clockedIn = $existing && $existing->clock_in;
        $clockedOut = $existing && $existing->clock_out;
        $success = null;
        return view('attendance', compact('ip', 'clockedIn', 'clockedOut', 'success'));
    }

    // Handle clock in request
    public function clockIn(Request $request)
    {
        // Validate IP address against whitelisted IPs
        $ip = $request->ip();
        if (!in_array($ip, $this->whitelistedIps)) {
            return back()->withErrors([
                'ip' => 'Clock in is not allowed on this IP Address.',
            ]);
        }
        
        // Check if the user already clocked in today
        $user = Auth::user();
        $today = now()->toDateString();
        $existing = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if ($existing && $existing->clock_in) {
            return back()->withErrors([
                'ip' => 'You have already clocked in today.',
            ]);
        }

        // Create or update attendance record
        Attendance::updateOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['clock_in' => now(), 'ip_address' => $ip],
        );

        $clockedIn = $existing && $existing->clock_in;
        $clockedOut = $existing && $existing->clock_out;
        $success = 'You have clocked in successfully!';
        return view('attendance', compact('ip', 'clockedIn', 'clockedOut', 'success'));
    }

    // Handle clock out request
    public function clockOut(Request $request)
    {
        // Validate IP address against whitelisted IPs
        $ip = $request->ip();
        if (!in_array($ip, $this->whitelistedIps)) {
            return back()->withErrors([
                'ip' => 'Clock out is not allowed on this IP Address.',
            ]);
        }
        
        // Check if the user already clocked out today
        $user = Auth::user();
        $today = now()->toDateString();
        $existing = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if ($existing && $existing->clock_out) {
            return back()->withErrors([
                'ip' => 'You have already clocked out today.',
            ]);
        }

        if (!$existing || !$existing->clock_in) {
            return back()->withErrors([
                'ip' => 'You have not clocked in today.',
            ]);
        }

        $existing->update(['clock_out' => now()]);

        $clockedIn = $existing && $existing->clock_in;
        $clockedOut = null;
        $success = 'You have clocked out successfully!';
        return view('attendance', compact('ip', 'clockedIn', 'clockedOut', 'success'));
    }
}
