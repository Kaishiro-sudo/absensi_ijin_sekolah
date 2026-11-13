<?php

namespace App\Http\Controllers;

use App\Models\LogGerbang;
use App\Models\Perizinan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SatpamController extends Controller
{
    public function index(): View
    {
        $perizinans = Perizinan::with([
            'user',
            'logGerbangs' => fn ($query) => $query->orderBy('waktu_scan'),
        ])
            ->whereIn('status', ['disetujui', 'selesai'])
            ->orderByDesc('created_at')
            ->get();

        return view('satpam.dashboard', compact('perizinans'));
    }

    public function scanKeluar(Request $request, Perizinan $perizinan): RedirectResponse
    {
        return $this->recordScan($request, $perizinan, 'keluar');
    }

    public function scanMasuk(Request $request, Perizinan $perizinan): RedirectResponse
    {
        return $this->recordScan($request, $perizinan, 'masuk');
    }

    private function recordScan(Request $request, Perizinan $perizinan, string $tipe): RedirectResponse
    {
        DB::transaction(function () use ($request, $perizinan, $tipe): void {
            $izin = Perizinan::query()->lockForUpdate()->findOrFail($perizinan->id);

            if ($izin->status !== 'disetujui') {
                throw ValidationException::withMessages([
                    'perizinan' => 'Hanya izin yang disetujui dan belum selesai yang dapat dipindai.',
                ]);
            }

            $lastScan = $izin->logGerbangs()->orderByDesc('waktu_scan')->first();
            $expectedPreviousScan = $tipe === 'keluar' ? null : 'keluar';

            if ($lastScan?->tipe_scan !== $expectedPreviousScan) {
                throw ValidationException::withMessages([
                    'perizinan' => $tipe === 'keluar'
                        ? 'Izin ini sudah memiliki catatan keluar.'
                        : 'Siswa harus tercatat keluar sebelum dapat tercatat masuk.',
                ]);
            }

            LogGerbang::create([
                'perizinan_id' => $izin->id,
                'satpam_id' => $request->user()->id,
                'tipe_scan' => $tipe,
                'waktu_scan' => now(),
            ]);

            if ($tipe === 'masuk') {
                $izin->update([
                    'jam_kembali' => now(),
                    'status' => 'selesai',
                ]);
            }
        });

        return redirect()->back()->with('success', 'Scan '.$tipe.' berhasil dicatat.');
    }
}
