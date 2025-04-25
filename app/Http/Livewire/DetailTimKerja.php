<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Struktur;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\LogPenilaian;
use Livewire\WithPagination;
use App\Models\AnggotaStruktur;
use App\Models\AnggotaTimKerja;
use App\Models\JabatanStruktur;
use App\Models\DaftarPertanyaan;
use App\Models\IndikatorPenilaian;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\DB;

class DetailTimKerja extends Component
{
    use WithPagination;

    public $idTimKerja;
    public $infoTimKerja;
    public $activeTab = 'struktur-tim-kerja';
    public $userRole;
    public $daftarStruktur = [];
    public $selectedStruktur = '';
    public $jabatanStruktur = [];
    public $indikatorPenilaian = [];
    public $daftarPenilaian;

    public $errorMessage = null;

    public function mount($id)
    {
        try {
            // Identifikasi Id Tim Kerja
            $this->idTimKerja = $id;

            // Identifikasi Role User
            $this->userRole = AnggotaTimKerja::where('user_id', auth()->user()->id)
                ->where('tim_kerja_id', $this->idTimKerja)
                ->value('role');

            if (!$this->userRole) {
                $this->errorMessage = 'Anda tidak memiliki akses ke Tim Kerja ini.';
                return;
            }

            // Identifikasi Struktur Tim kerja
            $this->infoTimKerja = TimKerja::findOrFail($id);
            $struktur = Struktur::where('tim_kerja_id', $id)->get();

            // Mengambil hanya nama struktur dari setiap data struktur
            $this->daftarStruktur = $struktur->pluck('nama_struktur')->toArray();

            // Mengambil data indikatorPenilaian dengan ID indikator dan pertanyaan
            $daftarIndikator = IndikatorPenilaian::where('tim_kerja_id', $id)->get();
            foreach ($daftarIndikator as $indikator) {
                $daftarPertanyaan = DaftarPertanyaan::where('indikator_penilaian_id', $indikator->id)->get();
                $pertanyaan = [];
                foreach ($daftarPertanyaan as $dp) {
                    $pertanyaan[] = [
                        'id' => $dp->id,
                        'pertanyaan' => $dp->pertanyaan,
                    ];
                }
                $this->indikatorPenilaian[] = [
                    'id' => $indikator->id,
                    'indikator' => $indikator->indikator,
                    'pertanyaan' => $pertanyaan,
                ];
            }
            $this->importData($id);

            $this->dataPenilaian($id);
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat memuat data: ' . $e->getMessage();
        }
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function importData($idTimKerja)
    {
        // Mendapatkan semua struktur tim kerja
        $struktur = Struktur::where('tim_kerja_id', $idTimKerja)->get();

        // Inisialisasi array jabatanStruktur
        $this->jabatanStruktur = [];

        // Iterasi melalui setiap struktur
        foreach ($struktur as $s) {
            // Mendapatkan jabatan dalam struktur tersebut
            $jabatan = JabatanStruktur::where('struktur_id', $s->id)->orderBy('level')->get();

            $strukturData = [];
            foreach ($jabatan as $jab) {
                $atasanNama = ''; // Inisialisasi variabel untuk menyimpan nama jabatan atasan

                // Jika jabatan memiliki atasan, tetapkan nama jabatan atasan
                if ($jab->atasan != null) {
                    $atasan = JabatanStruktur::find($jab->atasan); // Cari jabatan atasan berdasarkan ID

                    // Jika jabatan atasan ditemukan, tetapkan nama jabatannya
                    if ($atasan) {
                        $atasanNama = $atasan->nama_jabatan;
                    }
                }

                // Menambahkan data jabatan ke dalam array struktur
                $strukturData[] = [
                    'level' => $jab->level,
                    'nama_jabatan' => $jab->nama_jabatan,
                    'pejabat' => $this->getPejabat($jab->id), // Memanggil fungsi getPejabat untuk mendapatkan pejabat jabatan
                    'atasan' => $atasanNama, // Menyimpan nama jabatan atasan
                ];
            }

            // Menambahkan data struktur beserta jabatannya ke dalam array jabatanStruktur
            $this->jabatanStruktur[] = [
                'nama_struktur' => $s->nama_struktur,
                'jabatan' => $strukturData,
            ];
        }

        // Set default selectedStruktur jika tidak ada data
        $this->selectedStruktur = $this->daftarStruktur[0] ?? '';

        // Mendebug hasil jabatan struktur
        // dd($this->jabatanStruktur);
    }

    public function dataPenilaian($idTimKerja)
    {
        $strukturIds = Struktur::where('tim_kerja_id', $idTimKerja)
            ->pluck('id')
            ->all();

        // dd($strukturIds);

        $this->daftarPenilaian = Penilaian::whereIn('struktur_id', $strukturIds)
            ->where('waktu_selesai', '>', now()) // Memeriksa waktu selesai lebih besar dari waktu saat ini
            ->orderBy('waktu_selesai', 'asc')
            ->limit(4)
            ->with('struktur')->get()
            ->map(function ($penilaian) {
                $penilaian->telahDinilai = LogPenilaian::where('penilaian_id', $penilaian->id)
                    ->where('penilai_id', auth()->user()->id)
                    ->where('status', 'sudah')
                    ->count();

                $penilaian->totalDinilai = LogPenilaian::where('penilaian_id', $penilaian->id)
                    ->where('penilai_id', auth()->user()->id)
                    ->count();

                $penilaian->jarakDeadline = Carbon::now()->diffInDays(Carbon::parse($penilaian->waktu_selesai), false);

                return $penilaian;
            });

        // dd($this->daftarPenilaian);

        // $this->daftarPenilaian = Penilaian::where('struktur_id')
    }

    public function getPejabat($jabatanId)
    {
        // Mendapatkan anggota Tim Kerja ID berdasarkan jabatan ID
        $anggotaTimKerjaId = AnggotaStruktur::where('jabatan_struktur_id', $jabatanId)->pluck('anggota_tim_kerja_id')->toArray();

        // Mendapatkan user_id berdasarkan anggota_tim_kerja_id
        $userIds = AnggotaTimKerja::whereIn('id', $anggotaTimKerjaId)->pluck('user_id')->toArray();

        // Mendapatkan email pejabat berdasarkan user_id
        $emails = User::whereIn('id', $userIds)->pluck('name')->toArray();

        return $emails;
    }

    public function tambahIndikator()
    {
        $this->indikatorPenilaian[] = [
            'indikator' => '',
            'pertanyaan' => [''],
        ];
    }

    public function tambahPertanyaan($index)
    {
        $this->indikatorPenilaian[$index]['pertanyaan'][] = [
            'id' => null,
            'pertanyaan' => '',
        ];
    }

    public function hapusPertanyaan($indikatorKey, $pertanyaanIndex)
    {
        unset($this->indikatorPenilaian[$indikatorKey]['pertanyaan'][$pertanyaanIndex]);
        // Reindex array setelah penghapusan agar kunci array berurutan
        $this->indikatorPenilaian[$indikatorKey]['pertanyaan'] = array_values($this->indikatorPenilaian[$indikatorKey]['pertanyaan']);
    }

    public function hapusIndikator($indikatorKey)
    {
        unset($this->indikatorPenilaian[$indikatorKey]);
        // Reindex array setelah penghapusan agar kunci array berurutan
        $this->indikatorPenilaian = array_values($this->indikatorPenilaian);
    }

    public function simpanPerubahanIndikator()
    {
        DB::beginTransaction();

        try {
            // Ambil semua indikator penilaian yang ada di database untuk tim kerja ini
            $existingIndikators = IndikatorPenilaian::where('tim_kerja_id', $this->idTimKerja)->get()->keyBy('id');

            // Simpan id indikator yang sudah diproses
            $processedIndikatorIds = [];

            foreach ($this->indikatorPenilaian as $indikatorData) {
                if (isset($indikatorData['id'])) {
                    // Update indikator yang sudah ada berdasarkan ID
                    $indikator = IndikatorPenilaian::find($indikatorData['id']);
                    if ($indikator) {
                        $indikator->indikator = $indikatorData['indikator'];
                        $indikator->save();
                    } else {
                        // Jika tidak ditemukan, buat baru
                        $indikator = new IndikatorPenilaian();
                        $indikator->tim_kerja_id = $this->idTimKerja;
                        $indikator->indikator = $indikatorData['indikator'];
                        $indikator->save();
                    }
                } else {
                    // Buat indikator baru jika tidak ada ID
                    $indikator = new IndikatorPenilaian();
                    $indikator->tim_kerja_id = $this->idTimKerja;
                    $indikator->indikator = $indikatorData['indikator'];
                    $indikator->save();
                }

                $processedIndikatorIds[] = $indikator->id;

                // Ambil daftar pertanyaan yang ada di database untuk indikator ini
                $existingPertanyaans = DaftarPertanyaan::where('indikator_penilaian_id', $indikator->id)->get()->keyBy('id');

                // Simpan id pertanyaan yang sudah diproses
                $processedPertanyaanIds = [];

                foreach ($indikatorData['pertanyaan'] as $pertanyaanData) {
                    if (isset($pertanyaanData['id'])) {
                        // Update pertanyaan yang sudah ada berdasarkan ID
                        $pertanyaan = DaftarPertanyaan::find($pertanyaanData['id']);
                        if ($pertanyaan) {
                            $pertanyaan->pertanyaan = $pertanyaanData['pertanyaan'];
                            $pertanyaan->save();
                        } else {
                            // Jika tidak ditemukan, buat baru
                            $pertanyaan = new DaftarPertanyaan();
                            $pertanyaan->indikator_penilaian_id = $indikator->id;
                            $pertanyaan->pertanyaan = $pertanyaanData['pertanyaan'];
                            $pertanyaan->save();
                        }
                    } else {
                        // Buat pertanyaan baru jika tidak ada ID
                        $pertanyaan = new DaftarPertanyaan();
                        $pertanyaan->indikator_penilaian_id = $indikator->id;
                        $pertanyaan->pertanyaan = $pertanyaanData['pertanyaan'];
                        $pertanyaan->save();
                    }

                    $processedPertanyaanIds[] = $pertanyaan->id;
                }

                // Hapus pertanyaan yang tidak ada di $processedPertanyaanIds
                DaftarPertanyaan::where('indikator_penilaian_id', $indikator->id)
                    ->whereNotIn('id', $processedPertanyaanIds)
                    ->delete();
            }

            // Hapus indikator yang tidak ada di $processedIndikatorIds
            $indikatorIdsToDelete = IndikatorPenilaian::where('tim_kerja_id', $this->idTimKerja)
                ->whereNotIn('id', $processedIndikatorIds)
                ->pluck('id')
                ->toArray();

            // Hapus daftar pertanyaan yang terkait dengan indikator yang akan dihapus
            DaftarPertanyaan::whereIn('indikator_penilaian_id', $indikatorIdsToDelete)->delete();

            // Hapus indikator yang tidak ada di $processedIndikatorIds
            IndikatorPenilaian::whereIn('id', $indikatorIdsToDelete)->delete();

            DB::commit();

            toast('Perubahan berhasil disimpan!', 'success');
            $this->activeTab = 'indikator-penilaian';
            return redirect()->route('detail-tim-kerja', [
                'id' => $this->idTimKerja
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $errorMessage = 'Terjadi kesalahan saat menyimpan perubahan.';
            $exceptionMessage = $e->getMessage();

            // Contoh penanganan error spesifik
            if (strpos($exceptionMessage, 'SQLSTATE[23000]') !== false) {
                // Cek apakah error terkait foreign key constraint (misal tidak bisa hapus karena data terkait)
                if (strpos($exceptionMessage, 'Cannot delete or update a parent row') !== false) {
                    $errorMessage = 'Tidak dapat menghapus pertanyaan. Pertanyaan yang ingin anda hapus sedang digunakan dalam penilaian.';
                } else {
                    $errorMessage = 'Terjadi konflik data pada database. Mohon periksa kembali input Anda.';
                }
            } elseif (strpos($exceptionMessage, 'validation') !== false) {
                $errorMessage = 'Validasi data gagal. Mohon periksa kembali input Anda.';
            }

            Alert::error('Error', $errorMessage . ' Detail: ' . $exceptionMessage);
            return redirect()->route('detail-tim-kerja', [
                'id' => $this->idTimKerja
            ]);
        }
    }

    public function konfirmasiSimpan()
    {
        // Implementasi logika penyimpanan perubahan ke database
        // Misalnya, Anda dapat mengakses dan memanipulasi $this->indikatorPenilaian untuk menyimpan perubahan
        // Setelah penyimpanan berhasil, Anda dapat menampilkan pesan sukses menggunakan toast
        $this->alert('success', 'Perubahan berhasil disimpan!');
    }


    public function render()
    {
        if ($this->errorMessage) {
            Alert::error('Error', $this->errorMessage);
            return view('components.error-page', ['message' => $this->errorMessage]);
        }

        return view('livewire.detail-tim-kerja', [
            'infoTimKerja' => $this->infoTimKerja,
            'daftarStruktur' => $this->daftarStruktur,
            'selectedStruktur' => $this->selectedStruktur,
            'jabatanStruktur' => $this->jabatanStruktur[$this->selectedStruktur] ?? [],
            'indikatorPenilaian' => $this->indikatorPenilaian,
            'activeTab' => $this->activeTab,
        ]);
    }
}
