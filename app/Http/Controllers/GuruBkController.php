<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Perizinan;

class GuruBkController extends Controller
{
    public function index()
    {
        $perizinans = Perizinan::with('user')->orderBy('created_at', 'desc')->get();
        return view('guru.dashboard', compact('perizinans'));
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        $perizinan = Perizinan::findOrFail($id);
        $perizinan->status = $validated['status'];
        $perizinan->save();

        return redirect()->back()->with('success', 'Status izin berhasil diperbarui!');
    }
}
