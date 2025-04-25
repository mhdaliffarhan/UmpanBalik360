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

class DaftarPenilaian extends Component
{
    public $daftarIdTimKerja;
    public $daftarTimKerja;
    public User $user;
    public $daftarPenilaian;

    public function mount()
    {
        $this->user = Auth::user();

        $this->daftarIdTimKerja = AnggotaTimKerja::where('user_id', $this->user->id)
            ->pluck('tim_kerja_id');

        $this->daftarTimKerja = TimKerja::whereIn('id', $this->daftarIdTimKerja)
            ->with('struktur.penilaian')
            ->get();

        $strukturIds = Struktur::whereIn('tim_kerja_id', $this->daftarIdTimKerja)
            ->pluck('id')
            ->all();

        $penilaians = Penilaian::whereIn('struktur_id', $strukturIds)
            ->with('struktur.timKerja')
            ->get();

        $logPenilaianUser = LogPenilaian::whereIn('penilaian_id', $penilaians->pluck('id'))
            ->where('penilai_id', $this->user->id)
            ->get()
            ->groupBy('penilaian_id');

        $this->daftarPenilaian = $penilaians->map(function ($penilaian) use ($logPenilaianUser) {
            $penilaian->telahDinilai = isset($logPenilaianUser[$penilaian->id]) ? $logPenilaianUser[$penilaian->id]->where('status', 'sudah')->count() : 0;
            $penilaian->totalDinilai = isset($logPenilaianUser[$penilaian->id]) ? $logPenilaianUser[$penilaian->id]->count() : 0;
            $penilaian->jarakDeadline = Carbon::now()->diffInDays(Carbon::parse($penilaian->waktu_selesai), false);
            return $penilaian;
        });

        $this->sortPenilaian();
    }

    public function sortPenilaian()
    {
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

    public function render()
    {
        return view('livewire.daftar-penilaian', [
            'daftarTimKerja' => $this->daftarTimKerja,
            'daftarPenilaian' => $this->daftarPenilaian,
        ]);
    }
}
