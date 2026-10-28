<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Peminjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">Daftar Peminjaman</h3>
                        @if(auth()->user()->role == 'mahasiswa')
                            <a href="{{ route('bookings.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Ajukan Peminjaman Baru</a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Lab</th>
                                    <th class="py-2 px-4 border-b text-left">Pemohon</th>
                                    <th class="py-2 px-4 border-b text-left">Waktu</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                <tr>
                                    <td class="py-2 px-4 border-b">{{ $booking->laboratory->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $booking->user->name }}</td>
                                    <td class="py-2 px-4 border-b text-sm">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('d/m/Y H:i') }} - <br>
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </td>
                                    <td class="py-2 px-4 border-b">
                                        <span class="px-2 py-1 rounded text-xs text-white
                                            @if($booking->status == 'pending') bg-yellow-500
                                            @elseif($booking->status == 'approved_dosen') bg-blue-500
                                            @elseif($booking->status == 'approved_laboran' || $booking->status == 'completed') bg-green-500
                                            @else bg-red-500 @endif
                                        ">
                                            {{ strtoupper($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        @if(auth()->user()->role == 'dosen' && $booking->status == 'pending')
                                            <form action="{{ route('bookings.status', $booking->id) }}" method="POST" class="inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="approved_dosen">
                                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">Setujui</button>
                                            </form>
                                        @endif
                                        @if(auth()->user()->role == 'laboran' && $booking->status == 'approved_dosen')
                                            <form action="{{ route('bookings.status', $booking->id) }}" method="POST" class="inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="approved_laboran">
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">Validasi Alat</button>
                                            </form>
                                        @endif
                                        @if(in_array(auth()->user()->role, ['dosen', 'laboran']) && in_array($booking->status, ['pending', 'approved_dosen']))
                                            <form action="{{ route('bookings.status', $booking->id) }}" method="POST" class="inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">Tolak</button>
                                            </form>
                                        @endif
                                        @if($booking->status == 'pending' || $booking->status == 'approved_dosen' || $booking->status == 'approved_laboran')
                                            <span class="text-xs text-gray-500 italic">Menunggu</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">Belum ada data peminjaman.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
