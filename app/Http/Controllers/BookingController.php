<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Laboratory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'mahasiswa') {
            $bookings = Booking::with(['laboratory', 'user'])->where('user_id', $user->id)->latest()->get();
        } else {
            $bookings = Booking::with(['laboratory', 'user'])->latest()->get();
        }

        return view('dashboard', compact('bookings'));
    }

    public function create()
    {
        $laboratories = Laboratory::where('is_active', true)->get();

        return view('bookings.create', compact('laboratories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'laboratory_id' => 'required|exists:laboratories,id',
            'start_time' => 'required|date|after_or_equal:today',
            'end_time' => 'required|date|after:start_time',
            'purpose' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $lab = Laboratory::where('id', $request->laboratory_id)->lockForUpdate()->firstOrFail();

            $overlap = Booking::where('laboratory_id', $request->laboratory_id)
                ->whereIn('status', ['pending', 'approved_dosen', 'approved_laboran'])
                ->where(function ($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('start_time', '<=', $request->start_time)
                                ->where('end_time', '>=', $request->end_time);
                        });
                })
                ->exists();

            if ($overlap) {
                DB::rollBack();

                return back()->withInput()->withErrors(['error' => 'Jadwal bentrok dengan peminjaman lain. Silakan pilih waktu lain.']);
            }

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'laboratory_id' => $request->laboratory_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'purpose' => $request->purpose,
                'status' => 'pending',
                'equipment_checklist' => ['komputer' => 'baik', 'ac' => 'baik', 'kebersihan' => 'baik'],
            ]);

            AuditLog::create([
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'action' => 'created',
                'status_from' => null,
                'status_to' => 'pending',
                'notes' => 'Pengajuan peminjaman baru oleh '.Auth::user()->name,
            ]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Peminjaman berhasil diajukan dan menunggu validasi.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan sistem: '.$e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:approved_dosen,approved_laboran,rejected,completed',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        if ($user->role === 'dosen' && ! in_array($request->status, ['approved_dosen', 'rejected'])) {
            abort(403);
        }
        if ($user->role === 'laboran' && ! in_array($request->status, ['approved_laboran', 'rejected', 'completed'])) {
            abort(403);
        }
        if ($user->role === 'mahasiswa') {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $booking = Booking::where('id', $booking->id)->lockForUpdate()->firstOrFail();

            $oldStatus = $booking->status;
            $booking->status = $request->status;
            $booking->save();

            AuditLog::create([
                'booking_id' => $booking->id,
                'user_id' => $user->id,
                'action' => 'status_updated',
                'status_from' => $oldStatus,
                'status_to' => $request->status,
                'notes' => $request->notes ?? 'Status diupdate oleh '.$user->name,
            ]);

            DB::commit();

            return back()->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Gagal memperbarui status.']);
        }
    }
}
