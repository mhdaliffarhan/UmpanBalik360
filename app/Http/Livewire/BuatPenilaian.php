<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\Pertanyaan;
use App\Models\LogPenilaian;
use App\Models\BobotPenilaian;
use App\Models\AnggotaStruktur;
use App\Models\AnggotaTimKerja;
use App\Models\JabatanStruktur;
use App\Models\DaftarPertanyaan;
use App\Models\IndikatorPenilaian;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class BuatPenilaian extends Component
{
    public User $user;
    public $showSuccesNotification = false;
    public $daftarTimKerja;
    public $daftarIdTimKerja;
    public $penilaian = [
        'timKerja' => '',
        'namaPenilaian' => '',
        'deskripsiPenilaian' => '',
        'maksResponden' => '10',
        'mulai' => '',
        'selesai' => '',
    ];
    public $formStep = 1;

    // Step 2
    public $indikatorPenilaian = [];

    public function mount()
    {
        $this->user = auth()->user();

        // Mengambil daftar ID tim kerja yang diikuti oleh user
        $this->daftarIdTimKerja = AnggotaTimKerja::where('user_id', $this->user->id)
            ->where('role', 'admin')
            ->pluck('tim_kerja_id');

        // Mengambil data tim kerja berdasarkan daftar ID tim kerja
        $this->daftarTimKerja = TimKerja::whereIn('id', $this->daftarIdTimKerja)->with('struktur')->get();
        // dd($this->daftarTimKerja);
    }

    public function getIndikatorPenilaian($idTimKerja)
    {
        $indikatorPenilaian = IndikatorPenilaian::where('tim_kerja_id', $idTimKerja)->get();
        foreach ($indikatorPenilaian as $indikator) {
            $existingIndex = $this->findIndikatorIndex($indikator->indikator);

            if ($existingIndex === null) {
                $pertanyaans = DaftarPertanyaan::where('indikator_penilaian_id', $indikator->id)->get();

                $pertanyaanArray = [];

                foreach ($pertanyaans as $pertanyaan) {
                    $pertanyaanArray[] = [
                        'id_pertanyaan' => $pertanyaan->id,
                        'pertanyaan' => $pertanyaan->pertanyaan,
                        'status_check' => true,
                    ];
                }

                // Tambahkan data indikator dan pertanyaan ke dalam array
                $this->indikatorPenilaian[] = [
                    'indikator' => $indikator->indikator,
                    'pertanyaan' => $pertanyaanArray,
                ];
            }
        }
        // dd($this->indikatorPenilaian);
    }

    private function findIndikatorIndex($indikatorName)
    {
        foreach ($this->indikatorPenilaian as $index => $indikator) {
            if ($indikator['indikator'] === $indikatorName) {
                return $index;
            }
        }
        return null;
    }

    public function stepSelanjutnya()
    {
        // dd($this->penilaian);
        if ($this->formStep == 1) {
            $this->validateStep1();
            $this->getIndikatorPenilaian($this->penilaian['timKerja']);
        }
        $this->formStep++;
    }

    public function stepSebelumnya()
    {
        $this->formStep--;
    }

    public function validateStep1()
    {
        $this->showSuccesNotification = true;

        // Validasi total bobot penilaian
        // $totalBobot = $this->penilaian['atasan'] + $this->penilaian['sebaya'] + $this->penilaian['bawahan'] + $this->penilaian['diriSendiri'];
        // if ($totalBobot != 100) {
        //     $this->addError('total_bobot_error', 'Total bobot penilaian harus 100 %.');
        // }

        $this->validate([
            'penilaian.timKerja' => 'required', // Menjamin pemilihan tim kerja
            'penilaian.namaPenilaian' => 'required', // Menjamin nama penilaian diisi
            'penilaian.mulai' => 'required|date', // Menjamin tanggal mulai diisi dan merupakan format tanggal yang valid
            'penilaian.selesai' => 'required|date|after:penilaian.mulai', // Menjamin tanggal selesai diisi, merupakan format tanggal yang valid, dan setelah tanggal mulai
        ], [
            'penilaian.timKerja.required' => 'Tim Kerja harus dipilih.',
            'penilaian.namaPenilaian.required' => 'Nama Penilaian harus diisi.',
            'penilaian.*.required' => 'Field :attribute harus diisi.',
            'penilaian.*.numeric' => 'Field :attribute harus berupa angka.',
            'penilaian.*.min' => 'Field :attribute tidak boleh kurang dari 0.',
            'penilaian.mulai.required' => 'Tanggal Mulai harus diisi.',
            'penilaian.mulai.date' => 'Format Tanggal Mulai tidak valid.',
            'penilaian.selesai.required' => 'Tanggal Selesai harus diisi.',
            'penilaian.selesai.date' => 'Format Tanggal Selesai tidak valid.',
            'penilaian.selesai.after' => 'Tanggal Selesai harus setelah Tanggal Mulai.',
        ]);
    }

    public function validateStep2()
    {
        // validasi jika tidak ada satupun yang memiliki status checked !
    }

    public function save()
    {
        // // Validasi step 2
        $this->validateStep2();

        // Simpan data ke database
        $penilaian = Penilaian::create([
            'nama_penilaian' => $this->penilaian['namaPenilaian'],
            'struktur_id' => $this->penilaian['struktur'],
            'maks_responden' => $this->penilaian['maks-responden'],
            'waktu_mulai' => $this->penilaian['mulai'],
            'waktu_selesai' => $this->penilaian['selesai'],
        ]);

        foreach ($this->indikatorPenilaian as $indikator) {
            foreach ($indikator['pertanyaan'] as $pertanyaan) {
                if ($pertanyaan['status_check'] == 'checked') {
                    Pertanyaan::create([
                        'penilaian_id' => $penilaian->id,
                        'daftar_pertanyaan_id' => $pertanyaan['id_pertanyaan'],
                    ]);
                }
            }
        }

        // // Tambahkan log penilaian
        $this->addLogPenilaian($penilaian->id);
        Alert::success('Berhasil', 'Berhasil Membuat Penilaian');

        // Kirim Email ke peserta penilaian
        // $this->penilaianMail();

        // Reset data penilaian
        $this->resetPenilaianData();
    }

    private function addLogPenilaian($idPenilaian)
    {
        $logPenilaianData = []; // Variabel untuk mengumpulkan data LogPenilaian

        $jabatanStruktur = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])->get();
        foreach ($jabatanStruktur as $key => $jabatan) {
            $anggotaStruktur = AnggotaStruktur::where('jabatan_struktur_id', $jabatan->id)->with('anggotaTimKerja')->get();
            foreach ($anggotaStruktur as $a => $anggota) {
                $penilai_id = $anggota->anggotaTimKerja->user_id;
                $email_user = User::where('id', $penilai_id)->pluck('email')->first();
                $this->penilaianMail($email_user);

                // BAWAHAN KE ATASAN
                $jabatanPenilai = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])
                    ->where('id', $anggota->jabatan_struktur_id)
                    ->get();

                foreach ($jabatanPenilai as $f => $jabPenilai) {
                    if ($jabPenilai->atasan != null) {
                        $jabatanDinilai = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])
                            ->where('id', $jabPenilai->atasan)
                            ->get();

                        foreach ($jabatanDinilai as $g => $jabDinilai) {
                            $daftarDinilai = AnggotaStruktur::where('jabatan_struktur_id', $jabDinilai->id)->with('anggotaTimKerja')->get();

                            foreach ($daftarDinilai as $e => $dinilai) {
                                $logPenilaianData[] = [
                                    'penilaian_id' => $idPenilaian,
                                    'penilai_id' => $penilai_id,
                                    'role_penilai' => 'bawahan',
                                    'dinilai_id' => $dinilai->anggotaTimKerja->user_id,
                                    'status' => 'belum',
                                ];
                            }
                        }
                    }
                }

                // SEBAYA
                $jabatanPenilai = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])
                    ->where('id', $anggota->jabatan_struktur_id)
                    ->first();

                $jabatanSebaya = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])
                    ->where('atasan', $jabatanPenilai->atasan)
                    ->get();

                foreach ($jabatanSebaya as $d => $sebaya) {
                    $daftarDinilai = AnggotaStruktur::where('jabatan_struktur_id', $sebaya->id)->with('anggotaTimKerja')->get();

                    foreach ($daftarDinilai as $e => $dinilai) {
                        if ($penilai_id == $dinilai->anggotaTimKerja->user_id) {
                            $existingLog = collect($logPenilaianData)->where('penilai_id', $penilai_id)
                                ->where('penilaian_id', $idPenilaian)
                                ->where('dinilai_id', $dinilai->anggotaTimKerja->user_id)
                                ->where('role_penilai', 'diri sendiri')
                                ->isNotEmpty();

                            // dd($existingLog);
                            if (!$existingLog) {
                                $logPenilaianData[] = [
                                    'penilaian_id' => $idPenilaian,
                                    'penilai_id' => $penilai_id,
                                    'role_penilai' => 'diri sendiri',
                                    'dinilai_id' => $dinilai->anggotaTimKerja->user_id,
                                    'status' => 'belum',
                                ];
                            }
                        } else {
                            $logPenilaianData[] = [
                                'penilaian_id' => $idPenilaian,
                                'penilai_id' => $penilai_id,
                                'role_penilai' => 'sebaya',
                                'dinilai_id' => $dinilai->anggotaTimKerja->user_id,
                                'status' => 'belum',
                            ];
                        }
                    }
                }

                // ATASAN KE BAWAHAN
                $daftarJabatanBawahan = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])
                    ->where('atasan', $anggota->jabatan_struktur_id)
                    ->get();

                foreach ($daftarJabatanBawahan as $b => $bawahan) {
                    $daftarDinilai = AnggotaStruktur::where('jabatan_struktur_id', $bawahan->id)->with('anggotaTimKerja')->get();

                    foreach ($daftarDinilai as $c => $dinilai) {
                        $logPenilaianData[] = [
                            'penilaian_id' => $idPenilaian,
                            'penilai_id' => $penilai_id,
                            'role_penilai' => 'atasan',
                            'dinilai_id' => $dinilai->anggotaTimKerja->user_id,
                            'status' => 'belum',
                        ];
                    }
                }
            }
        }
        // Kelompokkan data berdasarkan penilai_id
        $groupedLogPenilaian = collect($logPenilaianData)->groupBy('penilai_id');
        $maks_responden = $this->penilaian['maks-responden'];

        $finalLogPenilaianData = [];

        foreach ($groupedLogPenilaian as $penilai_id => $logPenilaian) {
            // Pisahkan log dengan role_penilai 'diri sendiri'
            $diriSendiriLog = $logPenilaian->where('role_penilai', 'diri sendiri');
            $otherLogs = $logPenilaian->where('role_penilai', '!=', 'diri sendiri');
            // Jika jumlah log melebihi maks_responden
            if ($logPenilaian->count() > $maks_responden) {
                // Jika log 'diri sendiri' ada, pastikan termasuk dalam sampel
                if ($diriSendiriLog->isNotEmpty()) {
                    // Ambil sampel acak dari log lainnya
                    $otherLogs = $otherLogs->shuffle()->take($maks_responden - 1);
                    // Gabungkan log 'diri sendiri' dengan sampel acak lainnya
                    $logPenilaian = $diriSendiriLog->concat($otherLogs);
                } else {
                    // Jika tidak ada log 'diri sendiri', cukup ambil sampel acak sebanyak maks_responden
                    $logPenilaian = $logPenilaian->shuffle()->take($maks_responden);
                }
            }

            // Gabungkan data yang sudah difilter ke dalam array finalLogPenilaianData
            $finalLogPenilaianData = array_merge($finalLogPenilaianData, $logPenilaian->toArray());
        }
        // Batch insert data LogPenilaian ke dalam database
        LogPenilaian::insert($finalLogPenilaianData);
    }

    private function penilaianMail($email)
    {
        $tim_kerja = TimKerja::where('id', $this->penilaian['timKerja'])
            ->pluck('nama_tim')
            ->first();

        $penilaianDetails = [
            'nama_penilaian' => $this->penilaian['namaPenilaian'],
            'tim_kerja' => $tim_kerja,
            'tanggal_mulai' => date('d-m-Y', strtotime($this->penilaian['mulai'])),
            'tanggal_selesai' => date('d-m-Y', strtotime($this->penilaian['selesai'])),
            'link' => 'www.umpanbalik360derajat.my.id/login'
        ];

        // dd($email);q
        // Mengirim email ke satu penerima

        // Mail::to($email)->send(new \App\Mail\PenilaianNotification($penilaianDetails));
    }

    private function resetPenilaianData()
    {
        // Reset semua data penilaian
        $this->penilaian = [
            'timKerja' => '',
            'namaPenilaian' => '',
            'deskripsiPenilaian' => '',
            'atasan' => '40',
            'sebaya' => '30',
            'bawahan' => '20',
            'diriSendiri' => '10',
            'mulai' => '',
            'selesai' => '',
        ];

        $this->indikatorPenilaian = [];
        $this->formStep = 1;


        return redirect()->route('penilaian');
    }

    public function render()
    {
        return view('livewire.buat-penilaian', [
            'daftarTimKerja' => $this->daftarTimKerja,
        ]);
    }
}

// kelompokkan data berdasarkan penilai_id
// tetapkan maks_responden

// buat finalLogPenilaianData sebagai array kosong

// untuk setiap grup logPenilaian berdasarkan penilai_id:
//     pisahkan log dengan role_penilai 'diri sendiri'
//     pisahkan log lainnya
    
//     jika jumlah log > maks_responden:
//         jika ada log 'diri sendiri':
//             ambil sampel acak dari log lainnya sebanyak maks_responden - 1
//             gabungkan log 'diri sendiri' dengan sampel acak lainnya
//         jika tidak ada log 'diri sendiri':
//             ambil sampel acak sebanyak maks_responden
    
//     gabungkan log yang sudah difilter ke finalLogPenilaianData