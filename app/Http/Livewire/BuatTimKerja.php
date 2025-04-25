<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Struktur;
use App\Models\TimKerja;
use App\Models\Pertanyaan;
use Livewire\WithFileUploads;
use App\Models\AnggotaStruktur;
use App\Models\AnggotaTimKerja;
use App\Models\JabatanStruktur;
use App\Models\DaftarPertanyaan;
use App\Models\IndikatorPenilaian;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImporAnggotaTimKerja;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BuatTimKerja extends Component
{
    use WithFileUploads;
    public $file;
    public $showSuccesNotification = false;

    // Properti untuk langkah multistep form
    public $formStep = 1;

    public $testing;

    // Properti step 1
    public $namaTimKerja;
    public $deskripsiTimKerja;
    public $anggotaTimKerja = [[
        'email' => '',
        'nama' => '',
        'role' => ''
    ]];

    public $atasanLevel1;

    // Properti Step 2
    public $level = 2;
    public $namaStruktur;
    public $jabatan = [
        [

            [
                'nama_jabatan' => '',
                'pejabat' => '',
                'atasan' => ''
            ]
        ],
        [
            [
                'nama_jabatan' => '',
                'pejabat' => '',
                'atasan' => ''
            ]
        ]
    ];

    // Properti step 3
    public $indikatorPenilaian = [
        [
            'indikator' => 'Kepemimpinan',
            'pertanyaan' => [
                'Kemampuan mempengaruhi orang lain untuk mencapai tujuan organisasi.',
                'Kemampuan mengarahkan karyawan lain untuk mencapai tujuan organisasi.',
                'Menjadi contoh dalam komitmen untuk mencapai misi organisasi.',
                'Mempercayai rekan kerja dan bawahan untuk menyelesaikan pekerjaan yang diberikan.',
                'Kemampuan menyampaikan visi dan misi organisasi dengan jelas.',
                'Mampu meningkatkan semangat kerja rekan kerja.'
            ]
        ],
        [
            'indikator' => 'Kolaborasi',
            'pertanyaan' => [
                'Kemampuan bekerja harmonis dengan rekan kerja.',
                'Menjaga hubungan baik dengan rekan kerja.',
                'Menghargai umpan balik dari rekan kerja.',
                'Berkontribusi dalam kegiatan organisasi.'
            ]
        ],
        [
            'indikator' => 'Manajemen Diri',
            'pertanyaan' => [
                'Memiliki pemahaman yang baik tentang pekerjaan utama.',
                'Mengelola untuk mencapai standar kehadiran minimum.',
                'Bertanggung jawab terhadap pekerjaan sendiri.',
                'Memiliki sikap/kualitas pribadi yang baik.',
                'Membuat rencana kerja yang baik.',
                'Menyelesaikan pekerjaan dengan jujur dan adil.',
                'Memperlihatkan inisiatif untuk menyelesaikan pekerjaan dengan lebih efektif dan/atau efisien.'
            ]
        ],
        [
            'indikator' => 'Komunikasi',
            'pertanyaan' => [
                'Menyampaikan pesan secara lisan dengan jelas dan efektif.',
                'Memberikan informasi dengan akurat.'
            ]
        ],
        [
            'indikator' => 'Visi',
            'pertanyaan' => [
                'Niat untuk mencapai tujuan organisasi.',
                'Bekerja secara terorganisir untuk mencapai tujuan organisasi.',
                'Mampu melihat masalah dari berbagai sudut pandang.'
            ]
        ],
        [
            'indikator' => 'Keterampilan Organisasi',
            'pertanyaan' => [
                'Menggunakan fasilitas secara optimal untuk mencapai tujuan organisasi.',
                'Mampu mengelola dan mengalokasikan fasilitas untuk mencapai tujuan organisasi.',
                'Mengakui kontribusi orang lain dalam mencapai tujuan organisasi.'
            ]
        ],
        [
            'indikator' => 'Pengambilan Keputusan',
            'pertanyaan' => [
                'Memperhatikan ide orang lain selama pengambilan keputusan.',
                'Konsisten dalam pengambilan keputusan.',
                'Mengambil keputusan secara objektif.',
                'Mempertimbangkan secara hati-hati dan menyeluruh dalam pengambilan keputusan.'
            ]
        ],
        [
            'indikator' => 'Keahlian',
            'pertanyaan' => [
                'Memiliki pengetahuan yang luas tentang pekerjaan dan di luar itu.',
                'Mampu memberikan saran sesuai dengan keahlian Anda.'
            ]
        ],
        [
            'indikator' => 'Kemampuan Beradaptasi',
            'pertanyaan' => [
                'Mampu menyesuaikan diri dengan lingkungan kerja.',
                'Mampu beradaptasi dengan pekerjaan baru.'
            ]
        ]
    ];

    public function mount()
    {
        $userEmail = Auth::user()->email;
        $userName = Auth::user()->name;
        $this->anggotaTimKerja = [
            [
                'email' => $userEmail,
                'nama' => $userName,
                'role' => 'admin'
            ]
        ];
    }


    public function exportTemplate()
    {
        $templatePath = 'public/templates/template_anggota_tim_kerja.xlsx';
        $templateFile = Storage::path($templatePath);

        return response()->download($templateFile, 'template_anggota_tim_kerja.xlsx');
    }

    public function imporExcel()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Impor data dari Excel
        $impor = new ImporAnggotaTimKerja;
        $data = Excel::toCollection($impor, $this->file->getRealPath());

        // Memeriksa setiap baris data yang diimpor
        foreach ($data->first() as $index => $row) {
            // Skip the header row
            if ($index === 0) {
                continue;
            }

            // dd($row);
            // Jika email tidak kosong dan belum ada di dalam $this->anggotaTimKerja, tambahkan ke daftar anggota tim kerja
            if (!empty($row['0']) && !$this->isEmailDuplicate($row['0'])) {
                $this->anggotaTimKerja[] = [
                    'email' => $row['0'],
                    'nama' => $row['1'], // Kolom kedua untuk nama
                    'role' => 'anggota', // Isi role sesuai kebutuhan
                ];
            }
        }
        // dd($this->anggotaTimKerja);
        // Berikan pesan sukses jika berhasil diimpor
        toast('Berhasil impor excel', 'success');
    }


    private function isEmailDuplicate($email)
    {
        // Loop through $this->anggotaTimKerja to check if $email already exists
        foreach ($this->anggotaTimKerja as $anggota) {
            if ($anggota['email'] === $email) {
                return true;
            }
        }
        return false;
    }


    public function hapusAnggota($index)
    {
        // Emit event JavaScript untuk memunculkan peringatan penghapusan
        $this->emit('hapus-anggota', ['id' => $index]);

        unset($this->anggotaTimKerja[$index]);
        // Reindex array setelah penghapusan agar kunci array berurutan
        $this->anggotaTimKerja = array_values($this->anggotaTimKerja);

        // Menampilkan pesan toast bahwa anggota telah dihapus
        // toast('Anggota tim kerja telah dihapus', 'success');
        $this->dispatchBrowserEvent('swal:success', ['message' => 'Anggota tim kerja telah dihapus!']);
    }

    public function tambahInputAnggota()
    {
        $this->anggotaTimKerja[] = [
            'email' => '',
            'nama' => '',
            'role' => 'anggota'
        ];
    }


    // STEP 2
    public function tambahJabatan($i)
    {
        // Tambahkan elemen baru ke dalam array jabatan pada level $i
        $newJabatan = [
            'nama_jabatan' => '',
            'pejabat' => '',
            'atasan' => '',
        ];

        array_push($this->jabatan[$i], $newJabatan);
    }

    public function hapusJabatan($i)
    {
        array_pop($this->jabatan[$i]);
    }

    public function tambahLevel()
    {
        $newLevel = [

            [
                'nama_jabatan' => '',
                'pejabat' => '',
                'atasan' => ''
            ]
        ];

        array_push($this->jabatan, $newLevel);
    }

    public function hapusLevel()
    {
        if (count($this->jabatan) > 2) {
            array_pop($this->jabatan);
        }
    }


    // STEP 3

    public function tambahIndikator()
    {
        $this->indikatorPenilaian[] = [
            'indikator' => '',
            'pertanyaan' => [''],
        ];
    }

    public function tambahPertanyaan($index)
    {
        $this->indikatorPenilaian[$index]['pertanyaan'][] = '';
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
        Alert::success('Berhasil', 'Indikator berhasil dihapus');
        // toast('Indikator berhasil dihapus!', 'success');
    }


    public function stepSelanjutnya()
    {
        try {
            if ($this->formStep == 1) {
                $this->validasiStep1();
                // Short berdasarkan nama
                $this->anggotaTimKerja = collect($this->anggotaTimKerja)->sortBy('nama')->values()->toArray();
            } else if ($this->formStep == 2) {
                // input Atasan Level 1
                $this->atasanLevel1 = $this->jabatan[0][0]['nama_jabatan'];
                foreach ($this->jabatan[1] as $key => $value) {
                    $this->jabatan[1][$key]['atasan'] = $this->atasanLevel1;
                }
                $this->jabatan[0][0]['atasan'] = null;
                $this->validasiStep2();
            }
            $this->formStep++;
        } catch (\Exception $e) {
            // Jika terjadi kesalahan validasi, tampilkan pesan error
            $this->dispatchBrowserEvent('swal:warning', ['message' => $e->getMessage()]);
        }
    }

    public function stepSebelumnya()
    {
        $this->formStep--;
    }

    public function validasiStep1()
    {

        // Validasi
        $this->validate(
            [
                'namaTimKerja' => 'required',
                'anggotaTimKerja.*.email' => 'required|email',
                'anggotaTimKerja.*.role' => 'required',
            ],
            [
                'namaTimKerja.required' => 'Nama tim kerja harus diisi',
                'anggotaTimKerja.*.email.required' => 'Anggota Tim Kerja tidak boleh kosong',
                'anggotaTimKerja.*.email.email' => 'Email Anggota Tim Kerja belum sesuai format',
                'anggotaTimKerja.*.role.required' => 'Role Anggota Tim kerja harus dipilih',
            ]
        );

        $this->showSuccesNotification = true;
    }

    private function validasiStep2()
    {
        $this->validate([
            'namaStruktur' => 'required|string|max:255',
        ], [
            'namaStruktur' => 'Isian Nama Struktur harus diisi'
        ]);

        // dd($this->jabatan);
        // Additional validation for each level and jabatan
        foreach ($this->jabatan as $level => $jabatanLevel) {
            foreach ($jabatanLevel as $key => $jabatan) {
                $rules = [
                    "jabatan.$level.$key.nama_jabatan" => 'required|string|max:255',
                ];

                // Tambahkan aturan validasi untuk pejabat
                if (is_array($jabatan['pejabat'])) {
                    // Jika pejabat adalah array, setiap email harus valid
                    foreach ($jabatan['pejabat'] as $index => $email) {
                        $rules["jabatan.$level.$key.pejabat.$index"] = 'required|email';
                    }
                } else {
                    // Jika pejabat adalah email tunggal, validasi email tunggal
                    $rules["jabatan.$level.$key.pejabat"] = 'required|email';
                }

                // Jalankan validasi
                $this->validate($rules, [
                    "jabatan.$level.$key.nama_jabatan.required" => 'Isian nama jabatan harus diisi',
                    "jabatan.$level.$key.pejabat.*.required" => 'Isian pejabat harus diisi',
                    "jabatan.$level.$key.pejabat.*.email" => 'Isian pejabat harus berupa alamat email yang valid',
                ]);

                if ($level > 0) {
                    $this->validate([
                        "jabatan.$level.$key.atasan" => 'required|string|max:255',
                    ], [
                        "jabatan.$level.$key.atasan.required" => 'Isian atasan harus diisi',
                    ]);
                }
            }
        }
    }

    public function validasiStep3()
    {
        $this->validate([
            'indikatorPenilaian.*.indikator' => 'required|string|max:255',
            'indikatorPenilaian.*.pertanyaan.*' => 'required|string|max:255',
        ], [
            'indikatorPenilaian.*.indikator' => 'Isian indikator harus diisi',
            'indikatorPenilaian.*.pertanyaan.*' => 'Isian pertanyaan harus diisi',
        ]);
    }


    public function saveTimKerja()
    {
        // Validasi data sebelum menyimpan
        $this->validateData();

        // Simpan data TimKerja
        $timKerja = TimKerja::create([
            'nama_tim' => $this->namaTimKerja,
            'deskripsi_tim' => $this->deskripsiTimKerja,
            'user_id' => auth()->id(),
        ]);

        foreach ($this->anggotaTimKerja as $anggota) {
            // Memeriksa apakah user sudah ada berdasarkan email
            $user = User::where('email', $anggota['email'])->first();

            if ($user) {
                $user_id = $user->id;
            } else {
                // Jika user belum ada, buat user baru
                $newUser = User::create([
                    'name' => $anggota['nama'],
                    'email' => $anggota['email'],
                    'status' => 'unregistered',
                ]);
                $user_id = $newUser->id;
            }
            AnggotaTimKerja::create([
                'user_id' => $user_id,
                'tim_kerja_id' => $timKerja->id,
                'role' => $anggota['role'],
            ]);
        }

        // Simpan data Struktur
        $struktur = Struktur::create([
            'nama_struktur' => $this->namaStruktur,
            'tim_kerja_id' => $timKerja->id,
            'jumlah_level' => count($this->jabatan),
        ]);

        // Buatkan cara mengambil id tim kerja disini


        // Simpan data JabatanStruktur dan AnggotaStruktur
        foreach ($this->jabatan as $level => $jabatanLevel) {
            foreach ($jabatanLevel as $key => $jabatanItem) {
                // Cari ID jabatan atasan berdasarkan nama jabatan
                $atasan = JabatanStruktur::where('nama_jabatan', $jabatanItem['atasan'])
                    ->where('struktur_id', $struktur->id)
                    ->first();
                $atasanId = $atasan ? $atasan->id : null;
                // Simpan data JabatanStruktur
                $jabatanStruktur = JabatanStruktur::create([
                    'struktur_id' => $struktur->id,
                    'nama_jabatan' => $jabatanItem['nama_jabatan'],
                    'atasan' => $atasanId,
                    'level' => $level + 1,
                ]);

                // Simpan data AnggotaStruktur
                if (is_array($jabatanItem['pejabat'])) {

                    foreach ($jabatanItem['pejabat'] as $pejabatEmail) {

                        $pejabat = User::where('email', $pejabatEmail)->first();
                        if ($pejabat) {
                            // Jika pejabat ditemukan, s    impan data AnggotaStruktur
                            AnggotaStruktur::create([
                                'anggota_tim_kerja_id' => $pejabat->anggotaTimKerja->where('user_id', $pejabat->id)->where('tim_kerja_id', $timKerja->id)->first()->id,
                                'jabatan_struktur_id' => $jabatanStruktur->id,
                            ]);
                        }
                    }
                } else {
                    $pejabat = User::where('email', $jabatanItem['pejabat'])->first();
                    if ($pejabat) {
                        // Jika pejabat ditemukan, simpan data AnggotaStruktur
                        AnggotaStruktur::create([
                            'anggota_tim_kerja_id' => $pejabat->anggotaTimKerja->where('user_id', $pejabat->id)->where('tim_kerja_id', $timKerja->id)->first()->id,
                            'jabatan_struktur_id' => $jabatanStruktur->id,
                        ]);
                    }
                }
            }
        }

        // Simpan data IndikatorPenilaian dan Pertanyaan
        foreach ($this->indikatorPenilaian as $indikatorItem) {
            $indikator = IndikatorPenilaian::create([
                'tim_kerja_id' => $timKerja->id,
                'indikator' => $indikatorItem['indikator'],
            ]);

            foreach ($indikatorItem['pertanyaan'] as $pertanyaanItem) {
                DaftarPertanyaan::create([
                    'indikator_penilaian_id' => $indikator->id,
                    'pertanyaan' => $pertanyaanItem,
                ]);
            }
        }

        // Set pesan keberhasilan untuk ditampilkan
        Alert::success('Berhasil', 'Berhasil Membuat Tim Kerja');

        // Reset form setelah penyimpanan berhasil
        $this->resetForm();
    }


    private function validateData()
    {
        $this->validasiStep1();
        $this->validasiStep2();
        $this->validasiStep3();
    }

    private function resetForm()
    {
        // Reset semua properti atau lakukan aksi sesuai kebutuhan
        $this->reset([
            'formStep',
            'namaTimKerja',
            'deskripsiTimKerja',
            'anggotaTimKerja',
            'namaStruktur',
            'jabatan',
        ]);

        // Ganti dengan navigasi atau aksi yang sesuai setelah penyimpanan berhasil
        // Misalnya, arahkan pengguna ke halaman tertentu
        return redirect()->route('tim-kerja'); // Sesuaikan dengan nama route yang sesuai
    }


    public function render()
    {
        return view('livewire.buat-tim-kerja', [
            'anggotaTimKerja' => $this->anggotaTimKerja,
            'jabatan' => $this->jabatan,
            'indikatorPenilaian' => $this->indikatorPenilaian,
        ]);
    }
}
