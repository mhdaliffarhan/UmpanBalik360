<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Struktur;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\LogPenilaian;
use App\Models\AnggotaTimKerja;
use RealRashid\SweetAlert\Facades\Alert;

class DetailPenilaian extends Component
{
    public $idPenilaian;
    public $infoPenilaian;
    public $activeTab = 'daftar-penilaian';
    public $userRole;
    public $idUser;
    public $daftarDinilai;

    public $jumlahBelumMenilai;
    public $jumlahSudahMenilai;
    public $roles = ['atasan', 'sebaya', 'bawahan', 'diri sendiri'];

    public $dataNilaiUser = [
        'belum' => '',
        'sudah' => '',
        'total' => ''
    ];

    public function mount($id)
    {
        $this->idPenilaian = $id;

        try {
            $getPenilaian = Penilaian::where('id', $this->idPenilaian)->firstOrFail();
            $getStruktur = Struktur::where('id', $getPenilaian->struktur_id)->firstOrFail();
            $getTimKerja = TimKerja::where('id', $getStruktur->tim_kerja_id)->firstOrFail();

            // Identifikasi Role User
            $this->userRole = AnggotaTimKerja::where('user_id', auth()->user()->id)
                ->where('tim_kerja_id', $getTimKerja->id)
                ->value('role');

            // Daftar anggota penilaian
            $this->idUser = auth()->user()->id;
            $this->daftarDinilai = LogPenilaian::where('penilaian_id', $this->idPenilaian)
                ->where('penilai_id', $this->idUser)
                ->with('dinilai')
                ->select('id', 'dinilai_id', 'role_penilai', 'status')
                ->get();

            $this->infoPenilaian = Penilaian::findOrFail($id);

            // Data progres penilaian
            $dataPenilaian = LogPenilaian::where('penilaian_id', $this->idPenilaian)
                ->where('penilai_id', $this->idUser)
                ->selectRaw("COUNT(CASE WHEN status = 'sudah' THEN 1 END) as sudah, COUNT(CASE WHEN status = 'belum' THEN 1 END) as belum")
                ->firstOrFail();

            $this->dataNilaiUser['sudah'] = $dataPenilaian->sudah;
            $this->dataNilaiUser['belum'] = $dataPenilaian->belum;
            $this->dataNilaiUser['total'] = $dataPenilaian->sudah + $dataPenilaian->belum;

            // Menghindari pembagian dengan nol
            $this->dataNilaiUser['persen'] = $this->dataNilaiUser['total'] > 0
                ? round(($this->dataNilaiUser['sudah'] * 100) / $this->dataNilaiUser['total'], 1)
                : 0;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Menangani kesalahan jika model tidak ditemukan
            Alert::error('Error', 'Halaman tidak ditemukan!');
            return redirect()->route('penilaian'); // Ubah 'dashboard' sesuai dengan rute Anda
        } catch (\Exception $e) {
            // Menangani kesalahan umum lainnya
            Alert::error('Error', 'Terjadi kesalahan saat memuat data!');
            return redirect()->route('penilaian'); // Ubah 'dashboard' sesuai dengan rute Anda
        }

        // dd($this->daftarDinilai);
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        if ($this->userRole) {
            return view('livewire.detail-penilaian');
        } else {
            Alert::error('Error', 'Halaman tidak ditemukan!');
            return view('components.error-page');
        }
    }
}
