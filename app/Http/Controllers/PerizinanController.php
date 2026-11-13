<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perizinan;
use Illuminate\Support\Facades\Auth;

class PerizinanController extends Controller
{
    // Menampilkan halaman form pengajuan izin untuk siswa
    public function create()
    {
        return view('perizinan.create');
    }

    // Menyimpan data pengajuan izin baru ke database
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'alasan' => 'required|string|max:255',
            'jam_keluar' => 'required|date',
        ]);

        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        // Simpan data ke tabel perizinans dengan ID user yang sedang login
        Perizinan::create([
            'user_id' => $user->id,
            'alasan' => $validated['alasan'],
            'jam_keluar' => $validated['jam_keluar'],
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan izin berhasil dikirim dan menunggu persetujuan Guru BK!');
    }
}
