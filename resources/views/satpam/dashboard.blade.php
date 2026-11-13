<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Satpam - Absensi Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <main class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">
        <header class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Satpam</h1>
                <p class="text-sm text-gray-500">Verifikasi izin dan catat siswa saat keluar atau kembali.</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-600">
                    Logout
                </button>
            </form>
        </header>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 text-sm uppercase">
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Alasan</th>
                        <th class="py-3 px-4">Rencana Keluar</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Catatan Scan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    @forelse($perizinans as $perizinan)
                        @php($lastScan = $perizinan->logGerbangs->last())
                        <tr>
                            <td class="py-3 px-4 font-semibold text-gray-800">
                                {{ $perizinan->user->name ?? 'Siswa tidak ditemukan' }}
                            </td>
                            <td class="py-3 px-4">{{ $perizinan->alasan }}</td>
                            <td class="py-3 px-4">
                                {{ $perizinan->jam_keluar?->format('d-m-Y H:i') ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                {{ $perizinan->status === 'selesai' ? 'Selesai' : 'Disetujui' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($lastScan)
                                    {{ ucfirst($lastScan->tipe_scan) }}:
                                    {{ $lastScan->waktu_scan->format('d-m-Y H:i') }}
                                @else
                                    Belum ada scan
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($perizinan->status === 'disetujui' && ! $lastScan)
                                    <form action="{{ route('satpam.perizinan.keluar', $perizinan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                            Catat Keluar
                                        </button>
                                    </form>
                                @elseif($perizinan->status === 'disetujui' && $lastScan->tipe_scan === 'keluar')
                                    <form action="{{ route('satpam.perizinan.masuk', $perizinan) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700">
                                            Catat Kembali
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 italic text-xs">Selesai diproses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-400">
                                Belum ada izin yang disetujui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
