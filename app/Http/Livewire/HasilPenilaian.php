<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LogPenilaian;
use App\Models\AnggotaTimKerja;
use App\Models\IndikatorPenilaian;
use App\Models\Penilaian;
use App\Models\Struktur;
use App\Models\TimKerja;
use App\Services\HasilPenilaianService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HasilPenilaianExport;
use RealRashid\SweetAlert\Facades\Alert;

class HasilPenilaian extends Component
{
    public $idPenilaian;
    public $infoPenilaian;
    public $daftarPenilaian;
    public $daftarIndikator;
    public $nilai = [];
    public $userRole;
    public $infoNilai = [];

    public function mount($id)
    {
        $this->idPenilaian = $id;
        $this->infoPenilaian = Penilaian::with('struktur.timKerja')->findOrFail($id)->toArray();

        $timKerjaId = $this->infoPenilaian['struktur']['tim_kerja']['id'] ?? null;

        $this->userRole = AnggotaTimKerja::where('user_id', auth()->user()->id)
            ->where('tim_kerja_id', $timKerjaId)
            ->value('role');

        $this->infoPenilaian['atasan'] = 40;
        $this->infoPenilaian['sebaya'] = 30;
        $this->infoPenilaian['bawahan'] = 20;
        $this->infoPenilaian['diriSendiri'] = 10;
        $this->infoPenilaian['metode'] = 'aritmatika';
        $this->infoPenilaian['jumlahIndikator'] = 0;
        $this->infoPenilaian['filter'] = 'semua';

        $this->daftarPenilaian = LogPenilaian::where('penilaian_id', $this->idPenilaian)
            ->where('status', 'sudah')
            ->with(['dinilai', 'logNilai.pertanyaan.daftarPertanyaan.indikatorPenilaian'])
            ->get()
            ->groupBy('dinilai_id')
            ->mapWithKeys(fn($item, $key) => [$key => $item])
            ->toArray();

        $this->daftarIndikator();
        $this->nilaiAkhir();
    }

    public function daftarIndikator()
    {
        $this->daftarIndikator = [];
        $indikatorCache = [];

        foreach ($this->daftarPenilaian as $logPenilaian) {
            foreach ($logPenilaian[0]['log_nilai'] as $logNilai) {
                $indikatorId = $logNilai['pertanyaan']['daftar_pertanyaan']['indikator_penilaian_id'];

                if (!isset($indikatorCache[$indikatorId])) {
                    $indikatorCache[$indikatorId] = IndikatorPenilaian::find($indikatorId)->indikator ?? 'Unknown';
                }

                $indikator = $indikatorCache[$indikatorId];

                if (!in_array($indikator, array_column($this->daftarIndikator, 'nama'))) {
                    $this->daftarIndikator[] = [
                        'id' => $indikatorId,
                        'nama' => $indikator
                    ];
                }
            }
        }
        $this->infoPenilaian['jumlahIndikator'] = count($this->daftarIndikator);
    }

    public function nilaiAkhir()
    {
        if ($this->infoPenilaian['metode'] == 'aritmatika') {
            $this->nilai = (new HasilPenilaianService($this->daftarPenilaian, $this->infoPenilaian, $this->daftarIndikator))->hitungNilaiAkhir();
        } else {
            $totalBobot = $this->infoPenilaian['atasan'] + $this->infoPenilaian['sebaya'] + $this->infoPenilaian['bawahan'] + $this->infoPenilaian['diriSendiri'];

            if ($totalBobot != 100) {
                $this->dispatchBrowserEvent('swal:warning', ['message' => 'Total bobot harus 100!']);
                return;
            }
            $this->nilai = (new HasilPenilaianService($this->daftarPenilaian, $this->infoPenilaian, $this->daftarIndikator))->hitungNilaiAkhir();
        }

        if (empty($this->nilai)) {
            Alert::error('Error', 'Data hasil penilaian tidak ada!');
            return redirect()->route('hasil-penilaian');
        }

        $this->hitungRataRata();
        $this->dispatchBrowserEvent('swal:success', ['message' => 'Berhasil menerapkan metode!']);
    }

    protected function hitungRataRata()
    {
        $this->infoNilai['rerata_indikator'] = [];

        foreach ($this->daftarIndikator as $indikator) {
            $namaIndikator = $indikator['nama'];
            $akumulasi = 0;
            $jumlahRow = 0;
            foreach ($this->nilai as $key => $item) {
                $nilai = $item['nilai'];
                $akumulasi += $nilai[$namaIndikator]['nilai_akhir'];
                $jumlahRow = $key + 1;
            }
            $rataRataIndikator = round($akumulasi / $jumlahRow, 1);
            $this->infoNilai['rerata_indikator'][$namaIndikator] = $rataRataIndikator;
        }

        $jumlahIndikator = count($this->infoNilai['rerata_indikator']);
        if ($jumlahIndikator > 0) {
            $totalRerataIndikator = array_sum($this->infoNilai['rerata_indikator']);
            $this->infoNilai['rerata_total'] = round($totalRerataIndikator / $jumlahIndikator, 1);
        }
    }

    public function exportExcel()
    {
        return Excel::download(new HasilPenilaianExport($this->nilai, $this->daftarIndikator, $this->infoNilai), 'hasil_penilaian.xlsx');
    }

    public function render()
    {
        if ($this->userRole == 'admin') {
            return view('livewire.hasil-penilaian');
        } else {
            Alert::error('Error', 'Halaman tidak ditemukan!');
            return view('components.error-page');
        }
    }
}
