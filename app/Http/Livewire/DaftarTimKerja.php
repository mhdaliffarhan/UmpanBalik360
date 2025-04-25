<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TimKerja;
use App\Models\AnggotaTimKerja;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DaftarTimKerja extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Mendapatkan daftar tim kerja yang terkait dengan user berdasarkan user_id
        $daftarTimKerja = TimKerja::whereHas('anggotaTimKerja', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->withCount('anggotaTimKerja')->get(); // Menghitung jumlah anggota tim kerja

        return view('livewire.daftar-tim-kerja', [
            'daftarTimKerja' => $daftarTimKerja,
        ]);
    }
}
