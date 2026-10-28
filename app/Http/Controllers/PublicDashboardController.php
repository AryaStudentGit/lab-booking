<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Laboratory;
use Carbon\Carbon;

class PublicDashboardController extends Controller
{
    public function index()
    {
        $laboratories = Laboratory::where('is_active', true)->get();

        $schedules = Booking::with(['user', 'laboratory'])
            ->where('end_time', '>=', Carbon::now())
            ->whereIn('status', ['approved_dosen', 'approved_laboran', 'completed'])
            ->orderBy('start_time', 'asc')
            ->get();

        return view('welcome', compact('laboratories', 'schedules'));
    }
}
