<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Struktur;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\LogPenilaian;
use App\Models\AnggotaTimKerja;
use RealRashid\SweetAlert\Facades\Alert;

class Monitoring extends Component
{

    public $idPenilaian;
    public $infoPenilaian;
    public $daftarPenilaian;
    public $userRole;
    public $idUser;
    public $dataNilai = [
        'belum' => '',
        'sudah' => '',
        'total' => ''
    ];

    public function mount($id)
    {
        $this->idPenilaian = $id;
        $this->infoPenilaian = Penilaian::findOrFail($id);

        // dd($this->infoPenilaian);
        $getStruktur = Struktur::where('id', $this->infoPenilaian->struktur_id)->first();
        $getTimKerja = TimKerja::where('id', $getStruktur->tim_kerja_id)->first();

        // Identifikasi Role User
        $this->userRole = AnggotaTimKerja::where('user_id', auth()->user()->id)
            ->where('tim_kerja_id', $getTimKerja->id)
            ->value('role');

        // dd($this->userRole);



        // Daftar anggota penilaian
        $this->idUser = auth()->user()->id;

        // Mengambil data progres penilaian untuk setiap penilai
        $this->daftarPenilaian = LogPenilaian::where('penilaian_id', $this->idPenilaian)
            ->with('penilai')
            ->select('penilai_id')
            ->selectRaw("COUNT(CASE WHEN status = 'sudah' THEN 1 END) as sudah")
            ->selectRaw("COUNT(CASE WHEN status = 'belum' THEN 1 END) as belum")
            ->selectRaw("COUNT(*) as total")
            ->groupBy('penilai_id')
            ->get();

        // Iterasi untuk menambahkan penilai dan data penilaian ke dalam daftar
        foreach ($this->daftarPenilaian as $penilaian) {
            $penilaian->persen = $penilaian->total > 0
                ? round(($penilaian->sudah * 100) / $penilaian->total, 1)
                : 0;
        }

        // dd($this->daftarPenilaian);

        // Data progres penilaian
        $dataPenilaian = LogPenilaian::where('penilaian_id', $this->idPenilaian)
            ->selectRaw("COUNT(CASE WHEN status = 'sudah' THEN 1 END) as sudah, COUNT(CASE WHEN status = 'belum' THEN 1 END) as belum")
            ->first();

        $this->dataNilai['sudah'] = $dataPenilaian->sudah;
        $this->dataNilai['belum'] = $dataPenilaian->belum;
        $this->dataNilai['total'] = $dataPenilaian->sudah + $dataPenilaian->belum;

        // Menghindari pembagian dengan nol
        $this->dataNilai['persen'] = $this->dataNilai['total'] > 0
            ? round(($this->dataNilai['sudah'] * 100) / $this->dataNilai['total'], 1)
            : 0;
    }

    public function render()
    {
        if ($this->userRole == 'admin') {
            return view('livewire.monitoring');
        } else {
            Alert::error('Error', 'Halaman tidak ditemukan!');
            return view('components.error-page');
        }
    }
}
