<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Struktur;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\LogPenilaian;
use App\Models\AnggotaTimKerja;
use Illuminate\Support\Facades\Auth;

class DaftarMonitoring extends Component
{
    public $daftarIdTimKerja;
    public $daftarTimKerja;
    public User $user;
    public $daftarPenilaian;

    public function mount()
    {
        // Mendapatkan user yang sedang login
        $this->user = Auth::user();

        // Mengambil daftar ID tim kerja yang diikuti oleh user
        $this->daftarIdTimKerja = AnggotaTimKerja::where('user_id', $this->user->id)
            ->where('role', 'admin')
            ->pluck('tim_kerja_id');

        // Mengambil data tim kerja berdasarkan daftar ID tim kerja
        // menggunakan eager loading untuk mengambil relasi struktur dan penilaian
        $this->daftarTimKerja = TimKerja::whereIn('id', $this->daftarIdTimKerja)
            ->with('struktur.penilaian') // Menambahkan eager loading untuk relasi struktur dan penilaian
            ->get();


        // Inisialisasi daftarPenilaian sebagai koleksi kosong
        $this->daftarPenilaian = collect();

        foreach ($this->daftarIdTimKerja as $idTimKerja) {
            $this->dataPenilaian($idTimKerja);
        }

        $this->shortPenilaian();
        // dd($this->daftarPenilaian);
    }

    public function shortPenilaian()
    {
        // Shorting Data
        $this->daftarPenilaian = $this->daftarPenilaian->sort(function ($a, $b) {
            if ($a->jarakDeadline >= 0 && $b->jarakDeadline >= 0) {
                return $a->jarakDeadline <=> $b->jarakDeadline;
            } elseif ($a->jarakDeadline < 0 && $b->jarakDeadline < 0) {
                return $b->jarakDeadline <=> $a->jarakDeadline;
            } else {
                return $a->jarakDeadline >= 0 ? -1 : 1;
            }
        })->values();
    }

    public function dataPenilaian($idTimKerja)
    {
        $strukturIds = Struktur::where('tim_kerja_id', $idTimKerja)
            ->pluck('id')
            ->all();

        // dd($strukturIds);

        $penilaians = Penilaian::whereIn('struktur_id', $strukturIds)
            // ->where('waktu_selesai', '>=', now()->setTimezone('Asia/Jakarta')->startOfDay()) // Memeriksa waktu selesai lebih besar dari waktu saat ini
            ->orderBy('waktu_selesai', 'asc')
            // ->limit(4)
            ->with('struktur.timKerja')
            ->get()
            ->map(function ($penilaian) {
                $penilaian->telahDinilai = LogPenilaian::where('penilaian_id', $penilaian->id)
                    ->where('status', 'sudah')
                    ->count();

                $penilaian->totalDinilai = LogPenilaian::where('penilaian_id', $penilaian->id)
                    ->count();

                $penilaian->jarakDeadline = Carbon::now()->diffInDays(Carbon::parse($penilaian->waktu_selesai), false);

                $penilaian->persen = $penilaian->totalDinilai > 0 ? round(($penilaian->telahDinilai * 100) / $penilaian->totalDinilai, 1) : 0;

                return $penilaian;
            });

        // Gabungkan hasil penilaian dengan daftarPenilaian yang sudah ada
        $this->daftarPenilaian = $this->daftarPenilaian->merge($penilaians);

        // dd($this->daftarPenilaian);

        // $this->daftarPenilaian = Penilaian::where('struktur_id')
    }

    public function render()
    {

        return view('livewire.daftar-monitoring');
    }
}
