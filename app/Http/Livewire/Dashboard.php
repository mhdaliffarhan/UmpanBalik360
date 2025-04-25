<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\LogNilai;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\LogPenilaian;
use App\Models\AnggotaTimKerja;
use App\Models\IndikatorPenilaian;

class Dashboard extends Component
{
    public User $user;
    public $TimKerja;
    public $daftarTimKerja;
    public $daftarPenilaian = [];
    public $jumlahKuesionerDiisi;
    public $daftarPenilaianSelesai;
    public $dataChart = [
        'labels' => ['Label 1', 'Label 2', 'Label 3'],
        'nilai_orang_lain' => [3, 5, 7],
        'nilai_diri_sendiri' => [4, 6, 8],
    ];
    public $selectedData;
    protected $listeners = ['dataChartUpdated' => 'updateChartData'];

    public function mount()
    {
        $user = auth()->user();

        // Dapatkan daftar tim kerja yang terkait dengan user berdasarkan user_id
        $this->daftarTimKerja = TimKerja::whereHas('anggotaTimKerja', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->withCount('anggotaTimKerja')->with('struktur')->with('anggotaTimKerja')->get();

        $this->user = auth()->user();

        $strukturIds = $this->daftarTimKerja->pluck('struktur.*.id')->flatten()->all();

        // Penilaian berlangsung
        $this->daftarPenilaian = Penilaian::whereIn('struktur_id', $strukturIds)
            ->where('waktu_selesai', '>=', now()->setTimezone('Asia/Jakarta')->startOfDay())
            ->with('struktur.timkerja')
            ->get()
            ->toArray();

        // Urutkan daftarPenilaian secara menurun berdasarkan waktu_selesai
        $this->daftarPenilaian = collect($this->daftarPenilaian)->sortBy('waktu_selesai')->values()->all();

        // Hitung jumlah kuesioner yang telah diisi oleh pengguna
        $this->jumlahKuesionerDiisi = LogPenilaian::where('penilai_id', $this->user->id)
            ->where('status', 'sudah')
            ->count();

        // Loop melalui setiap penilaian untuk menambahkan dataNilaiUser dan deadlinePenilaian
        foreach ($this->daftarPenilaian as &$penilaian) {
            $dataPenilaian = LogPenilaian::where('penilaian_id', $penilaian['id'])
                ->where('penilai_id', $this->user->id)
                ->selectRaw("COUNT(CASE WHEN status = 'sudah' THEN 1 END) as sudah, COUNT(CASE WHEN status = 'belum' THEN 1 END) as belum")
                ->first();

            $dataNilaiUser = [
                'sudah' => $dataPenilaian->sudah,
                'belum' => $dataPenilaian->belum,
                'total' => $dataPenilaian->sudah + $dataPenilaian->belum,
            ];

            // Hindari pembagian dengan nol
            $dataNilaiUser['persen'] = $dataNilaiUser['total'] > 0
                ? round(($dataNilaiUser['sudah'] * 100) / $dataNilaiUser['total'], 1)
                : 0;

            $penilaian['dataNilaiUser'] = $dataNilaiUser;
            $penilaian['deadlinePenilaian'] = Carbon::parse($penilaian['waktu_selesai'])->format('d-m-Y');
            $penilaian['jarakHariKeDeadline'] = Carbon::now()->diffInDays(Carbon::parse($penilaian['waktu_selesai']), true);
        }

        // Query untuk Grafik Hasil Penilaian
        $this->daftarPenilaianSelesai = Penilaian::whereIn('struktur_id', $strukturIds)
            ->where('waktu_selesai', '<', now()->setTimezone('Asia/Jakarta')->startOfDay())
            ->with(['struktur.timKerja', 'pertanyaans.daftarPertanyaan.indikatorPenilaian'])
            ->get()
            ->toArray();
        // dd($this->daftarPenilaian);

        // $this->dataChart(1);
    }


    public function dataChart($id)
    {
        // Ambil data penilaian berdasarkan ID yang dipilih
        $penilaian = Penilaian::where('id', $id)
            ->select('id', 'nama_penilaian')
            ->first()
            ->toArray();

        // Simpan data penilaian dalam properti dataChart
        $this->dataChart = $penilaian;

        $this->daftarIndikator($id);
        $this->nilai($id);
        // dd($this->dataChart);
    }

    public function daftarIndikator($id)
    {
        foreach ($this->daftarPenilaianSelesai as &$penilaian) {
            if ($penilaian['id'] == $id) {
                // Inisialisasi labels jika belum ada
                if (!isset($this->dataChart['labels'])) {
                    $this->dataChart['labels'] = [];
                }

                // Loop melalui pertanyaans untuk menambahkan indikator ke dataChart
                foreach ($penilaian['pertanyaans'] as $pertanyaan) {
                    $indikator = $pertanyaan['daftar_pertanyaan']['indikator_penilaian']['indikator'];
                    $indikatorId = $pertanyaan['daftar_pertanyaan']['indikator_penilaian']['id'];

                    // Cek apakah indikator sudah ada di labels
                    if (!in_array($indikator, $this->dataChart['labels'])) {
                        $this->dataChart['labels'][] = $indikator;
                    }
                }
            }
        }
    }

    public function nilai($id)
    {
        // Mendapatkan data nilai "Orang lain" untuk chart
        $orangLain = LogPenilaian::where('penilaian_id', $id)
            ->where('dinilai_id', '=', auth()->user()->id) // Menyaring hanya nilai dari orang lain
            ->where('role_penilai', '!=', 'diri sendiri') // Hanya perlu data dari penilai selain diri sendiri
            ->where('status', 'sudah')
            ->with('logNilai.pertanyaan.daftarPertanyaan.indikatorPenilaian')
            ->get();

        $dataNilaiOrangLain = [];
        foreach ($orangLain as $logPenilaian) {
            foreach ($logPenilaian->logNilai as $logNilai) {
                $indikatorId = $logNilai->pertanyaan->daftarPertanyaan->indikator_penilaian_id;
                $indikator = IndikatorPenilaian::findOrFail($indikatorId)->indikator;

                if (!isset($dataNilaiOrangLain[$indikator])) {
                    $dataNilaiOrangLain[$indikator] = [
                        'total_nilai' => 0,
                        'jumlah' => 0,
                    ];
                }

                $dataNilaiOrangLain[$indikator]['total_nilai'] += $logNilai->nilai;
                $dataNilaiOrangLain[$indikator]['jumlah']++;
            }
        }

        // Mendapatkan data nilai "Diri Sendiri" untuk chart
        $diriSendiri = LogPenilaian::where('penilaian_id', $id)
            ->where('dinilai_id', auth()->user()->id)
            ->where('role_penilai', 'diri sendiri')
            ->where('status', 'sudah')
            ->with('logNilai.pertanyaan.daftarPertanyaan.indikatorPenilaian')
            ->first();

        $dataNilaiDiri = [];
        foreach ($diriSendiri->logNilai as $logNilai) {
            $indikatorId = $logNilai->pertanyaan->daftarPertanyaan->indikator_penilaian_id;
            $indikator = IndikatorPenilaian::findOrFail($indikatorId)->indikator;

            if (!isset($dataNilaiDiri[$indikator])) {
                $dataNilaiDiri[$indikator] = [
                    'total_nilai' => 0,
                    'jumlah' => 0,
                ];
            }

            $dataNilaiDiri[$indikator]['total_nilai'] += $logNilai->nilai;
            $dataNilaiDiri[$indikator]['jumlah']++;
        }

        // Persiapkan data untuk chart
        $labels = [];
        $nilaiOrangLain = [];
        $nilaiDiriSendiri = [];

        foreach ($dataNilaiOrangLain as $indikator => $data) {
            $labels[] = $indikator;
            $nilaiOrangLain[] = $data['jumlah'] > 0 ? round($data['total_nilai'] / $data['jumlah'], 1) : 0;
            $nilaiDiriSendiri[] = isset($dataNilaiDiri[$indikator]) && $dataNilaiDiri[$indikator]['jumlah'] > 0
                ? round($dataNilaiDiri[$indikator]['total_nilai'] / $dataNilaiDiri[$indikator]['jumlah'], 1)
                : 0;
        }

        // Atur data untuk chart
        $this->dataChart = [
            'labels' => $labels,
            'nilai_orang_lain' => $nilaiOrangLain,
            'nilai_diri_sendiri' => $nilaiDiriSendiri,
        ];

        // dd($this->dataChart);
    }


    public function render()
    {
        return view('livewire.dashboard', [
            'daftarTimKerja' => $this->daftarTimKerja,
            'daftarPenilaian' => $this->daftarPenilaian,
            'data' => $this->dataChart
        ]);
    }

    public function updateChartData($data)
    {
        $this->dataChart = $data;
    }

    public function changeData($id)
    {
        if ($id == 0) {
            $newDataChart  = [
                'labels' => ['Label 1', 'Label 2', 'Label 3', 'Label 4'],
                'nilai_orang_lain' => [3, 5, 7, 5],
                'nilai_diri_sendiri' => [4, 6, 8, 5],
            ];
        } else {
            $this->dataChart($id);
            // dd($this->dataChart);

            $labels = $this->dataChart['labels'];
            $newDataChart = $this->dataChart;
        }

        // dd($newDataChart);

        // Emit event dataChart yang telah diperbarui
        $this->emit('dataChartUpdated', $newDataChart);
    }
}
