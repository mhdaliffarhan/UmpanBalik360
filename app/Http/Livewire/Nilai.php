<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\LogNilai;
use App\Models\Penilaian;
use App\Models\Pertanyaan;

use App\Models\LogPenilaian;
use App\Models\DaftarPertanyaan;
use function Laravel\Prompts\select;
use RealRashid\SweetAlert\Facades\Alert;

class Nilai extends Component
{
    public $currentPage = 1; // Halaman saat ini
    public $maxPage; // Jumlah maksimum halaman
    public $logPenilaian;
    public $idLogPenilaian;
    public $idPenilaian;
    public $idDinilai;
    public $idPenilai;
    public $infoDinilai;
    public $infoPenilaian;
    public $daftarPertanyaan = [];
    public $indikatorPenilaian = [];
    public $errors = [];

    public function mount($id)
    {

        $this->logPenilaian = LogPenilaian::where('id', $id)
            ->with('dinilai')
            ->first();

        if (!$this->logPenilaian) {
            Alert::warning('Error', 'Halaman tidak ditemukan!');
            return view('components.error-page');
        }
        $this->idLogPenilaian = $this->logPenilaian->id;
        $this->idDinilai = $this->logPenilaian->dinilai_id;
        $this->idPenilaian = $this->logPenilaian->penilaian_id;
        $this->idPenilai = $this->logPenilaian->penilai_id;

        $this->infoPenilaian = Penilaian::where('id', $this->idPenilaian)->first();
        $time = now()->setTimezone('Asia/Jakarta')->startOfDay();
        // dd($time);
        if (Carbon::parse($this->infoPenilaian->waktu_selesai) < $time) {
            Alert::error('Error', 'Waktu penilaian telah berakhir!');
            return redirect()->route('detail-penilaian', ['id' => $this->idPenilaian]);
        }

        $this->infoDinilai = User::where('id', $this->idDinilai)->first();
        // dd($this->infoDinilai);

        // Daftar Pertanyaan
        $daftarPertanyaanId = Pertanyaan::where('penilaian_id', $this->idPenilaian)
            ->pluck('daftar_pertanyaan_id')
            ->toArray();


        // dd($daftarPertanyaanId);
        $this->daftarPertanyaan = DaftarPertanyaan::whereIn('id', $daftarPertanyaanId)
            ->with('indikatorPenilaian')
            ->select('id', 'indikator_penilaian_id', 'pertanyaan')
            ->get();

        $indikatorPenilaian = [];

        // Lakukan pengelompokan pertanyaan berdasarkan indikator penilaian
        foreach ($this->daftarPertanyaan as $pertanyaan) {
            // Ambil indikator penilaian dari setiap pertanyaan
            $indikator = $pertanyaan->indikatorPenilaian->indikator;

            // Tambahkan pertanyaan ke dalam array indikatorPenilaian berdasarkan indikatornya
            $indikatorPenilaian[$indikator][] = [
                'daftar_pertanyaan_id' => $pertanyaan->id,
                'pertanyaan' => $pertanyaan->pertanyaan,
                'nilai' => ''
            ];
        }

        // Ubah struktur data menjadi array yang sesuai
        $indikatorPenilaian = array_map(function ($indikator, $pertanyaan) {
            return [
                'indikator' => $indikator,
                $pertanyaan
            ];
        }, array_keys($indikatorPenilaian), $indikatorPenilaian);

        $this->indikatorPenilaian = $indikatorPenilaian;
        // dd($this->daftarPertanyaan);
        // dd($this->indikatorPenilaian);
        $this->maxPage = count($this->indikatorPenilaian);
        // dd($this->maxPage);
    }

    public function next()
    {
        if ($this->validatePage($this->currentPage)) {
            if ($this->currentPage < $this->maxPage) {
                $this->currentPage++;
            }
        }
    }

    public function back()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function validatePage($page)
    {
        $this->errors = [];
        $currentIndikator = $this->indikatorPenilaian[$page - 1];

        foreach ($currentIndikator[0] as $index => $pertanyaan) {
            if ($pertanyaan['nilai'] === '') {
                $this->errors[$index] = 'Nilai harus diisi.';
            }
        }

        if (!empty($this->errors)) {
            // Alert::warning('Warning Title', 'Harap isi semua nilai sebelum melanjutkan.');
            $this->dispatchBrowserEvent('swal:warning', ['message' => 'Harap isi semua nilai sebelum melanjutkan.']);
            return false;
        }

        return true;
    }

    public function confirmSave()
    {
        if (!$this->validateInputs()) {
            return;
        }
        $this->dispatchBrowserEvent('confirm-save');
    }

    public function save()
    {

        foreach ($this->indikatorPenilaian as $indikator) {
            foreach ($indikator[0] as $pertanyaan) {
                $logNilai = new LogNilai();
                $logNilai->log_penilaian_id = $this->idLogPenilaian;
                $logNilai->pertanyaans_id = $pertanyaan['daftar_pertanyaan_id'];
                $logNilai->nilai = $pertanyaan['nilai'];
                $logNilai->save();
            }
        }
        $logPenilaian = LogPenilaian::find($this->idLogPenilaian);
        $logPenilaian->status = 'sudah';
        $logPenilaian->save();

        Alert::success('Berhasil', 'Kuesioner penilaian berhasil disimpan!');
        return redirect()->route('detail-penilaian', ['id' => $this->idPenilaian]);
    }

    public function validateInputs()
    {
        foreach ($this->indikatorPenilaian as $page => $indikator) {
            if (!$this->validatePage($page + 1)) {
                $this->currentPage = $page + 1;
                return false;
            }
        }
        return true;
    }


    public function render()
    {
        if (($this->idPenilai == auth()->user()->id) && ($this->logPenilaian->status == 'belum')) {
            return view('livewire.nilai');
        } else {
            Alert::error('Error', 'Halaman tidak ditemukan!');
            return view('components.error-page');
        }
    }
}
