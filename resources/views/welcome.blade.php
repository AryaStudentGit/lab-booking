<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Lab - Guest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-blue-600">LabBooking Univ</span>
                </div>
                <div class="flex items-center">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium mr-4">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md font-medium hover:bg-blue-700">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="py-12 bg-blue-50">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Sistem Informasi Peminjaman Laboratorium</h1>
            <p class="text-lg text-gray-600 mb-8">Lihat ketersediaan ruangan dan ajukan peminjaman secara realtime tanpa bentrok.</p>
            @auth
                <a href="{{ route('bookings.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold text-lg shadow hover:bg-blue-700">Ajukan Peminjaman</a>
            @else
                <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold text-lg shadow hover:bg-blue-700">Ajukan Peminjaman</a>
            @endauth
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-2xl font-bold mb-4 border-b pb-2">Daftar Laboratorium</h2>
                <div class="space-y-4">
                    @foreach($laboratories as $lab)
                    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                        <h3 class="text-lg font-bold">{{ $lab->name }}</h3>
                        <p class="text-gray-600 text-sm mt-1">{{ $lab->description }}</p>
                        <div class="mt-2 text-sm text-gray-500 font-medium">Kapasitas: {{ $lab->capacity }} Orang</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h2 class="text-2xl font-bold mb-4 border-b pb-2">Jadwal Sedang & Akan Berlangsung</h2>
                @if($schedules->isEmpty())
                    <div class="bg-yellow-50 text-yellow-700 p-4 rounded-lg">Tidak ada jadwal peminjaman saat ini.</div>
                @else
                    <div class="space-y-4">
                        @foreach($schedules as $schedule)
                        <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-md font-bold text-gray-800">{{ $schedule->laboratory->name }}</h3>
                                    <p class="text-sm text-gray-600">Oleh: {{ $schedule->user->name }} ({{ $schedule->purpose }})</p>
                                </div>
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Booked</span>
                            </div>
                            <div class="mt-2 text-sm text-gray-500">
                                Waktu: {{ \Carbon\Carbon::parse($schedule->start_time)->format('d M Y, H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>