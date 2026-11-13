<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru Piket - Absensi Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6">
        
        <!-- Header & Tombol Logout -->
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Guru Piket</h1>
                <p class="text-sm text-gray-500">Kelola dan setujui perizinan keluar-masuk siswa SMKN 10 Semarang.</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-600 transition">
                    Logout
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabel Daftar Izin -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 text-sm uppercase">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Alasan</th>
                        <th class="py-3 px-4">Rencana Jam Keluar</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    @forelse($perizinans as $index => $p)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-800">{{ $p->user->name ?? 'Siswa Tidak Ditemukan' }}</td>
                            <td class="py-3 px-4">{{ $p->alasan }}</td>
                            <td class="py-3 px-4">{{ $p->jam_keluar ? $p->jam_keluar->format('d-m-Y H:i') : '-' }}</td>
                            <td class="py-3 px-4">
                                @if($p->status == 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-1 rounded-full font-semibold">Pending</span>
                                @elseif($p->status == 'disetujui')
                                    <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-semibold">Disetujui</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-semibold">Ditolak</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($p->status == 'pending')
                                    <div class="flex justify-center space-x-2">
                                        <form action="{{ route('guru.perizinan.update', $p->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="disetujui">
                                            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 transition">Setujui</button>
                                        </form>
                                        <form action="{{ route('guru.perizinan.update', $p->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="ditolak">
                                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 transition">Tolak</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-xs">Selesai diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-400">Belum ada pengajuan izin dari siswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>