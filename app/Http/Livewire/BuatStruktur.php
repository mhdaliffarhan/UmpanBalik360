<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Struktur;
use App\Models\TimKerja;
use App\Models\AnggotaStruktur;
use App\Models\AnggotaTimKerja;
use App\Models\JabatanStruktur;
use RealRashid\SweetAlert\Facades\Alert;

class BuatStruktur extends Component
{

    public $showSuccesNotification = false;

    public $anggotaTimKerja;

    public $idTimKerja;
    // Struktur Baru
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

    public function mount($id)
    {
        $this->idTimKerja = $id;
        $timKerja = TimKerja::findOrFail($id);

        // Mengambil data anggota tim kerja sesuai dengan id tim kerja
        $anggotaTimKerja = AnggotaTimKerja::where('tim_kerja_id', $id)->get();

        $anggota = $timKerja->anggotaTimKerja()->get();
        foreach ($anggota as $key => $value) {
            $this->anggotaTimKerja[] = [
                'email' => $value->user->email, // Mengambil email dari relasi user
                'nama' => $value->user->name,
                'role' => $value->role
            ];
        }
    }


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

    private function validateData()
    {

        $this->showSuccesNotification = true;

        $this->validate([
            'namaStruktur' => 'required|string|max:255',
        ], [
            'namaStruktur' => 'Isian Nama Struktur harus diisi'
        ]);

        // Additional validation for each level and jabatan
        foreach ($this->jabatan as $level => $jabatanLevel) {
            foreach ($jabatanLevel as $key => $jabatan) {
                $this->validate([
                    "jabatan.$level.$key.nama_jabatan" => 'required|string|max:255',
                    "jabatan.$level.$key.pejabat.*" => 'required|email',

                ], [
                    "jabatan.$level.$key.nama_jabatan.required" => 'Isian nama jabatan harus diisi',
                    "jabatan.$level.$key.pejabat.*.required" => 'Isian pejabat harus diisi',
                ]);

                if ($level > 0) {
                    $this->validate([
                        "jabatan.$level.$key.atasan" => 'required|string|max:255', // Assuming atasan is a string field
                    ], [
                        "jabatan.$level.$key.atasan.required" => 'Isian atasan harus diisi',
                    ]);
                } else {
                }
            }
        }
    }


    public function saveStruktur()
    {
        $atasanLevel1 = $this->jabatan[0][0]['nama_jabatan'];
        foreach ($this->jabatan[1] as $key => $value) {
            $this->jabatan[1][$key]['atasan'] = $atasanLevel1;
        }

        $this->jabatan[0][0]['atasan'] = null;

        $this->validateData();

        $struktur = Struktur::create([
            'nama_struktur' => $this->namaStruktur,
            'tim_kerja_id' => $this->idTimKerja,
            'jumlah_level' => count($this->jabatan),
        ]);

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
                    // $emailPejabat[] = $jabatanItem['pejabat'];
                    foreach ($jabatanItem['pejabat'] as $pejabatEmail) {
                        $pejabat = User::where('email', $pejabatEmail)->first();
                        if ($pejabat) {
                            // Jika pejabat ditemukan, s    impan data AnggotaStruktur
                            AnggotaStruktur::create([
                                'anggota_tim_kerja_id' => $pejabat->anggotaTimKerja->where('tim_kerja_id', $this->idTimKerja)->first()->id,
                                'jabatan_struktur_id' => $jabatanStruktur->id,
                            ]);
                        }
                    }
                } else {
                    $pejabat = User::where('email', $jabatanItem['pejabat'])->first();
                    if ($pejabat) {
                        // Jika pejabat ditemukan, simpan data AnggotaStruktur
                        AnggotaStruktur::create([
                            'anggota_tim_kerja_id' => $pejabat->anggotaTimKerja->where('tim_kerja_id', $this->idTimKerja)->first()->id,
                            'jabatan_struktur_id' => $jabatanStruktur->id,
                        ]);
                    }
                }
            }
        }

        // Set pesan keberhasilan untuk ditampilkan
        Alert::success('Berhasil', 'Berhasil Membuat Struktur');

        // Reset form setelah penyimpanan berhasil
        $this->resetForm();
    }

    private function resetForm()
    {
        // Reset semua properti atau lakukan aksi sesuai kebutuhan
        $this->reset([
            'namaStruktur',
            'jabatan',
        ]);

        return redirect()->route('detail-tim-kerja', ['id' => $this->idTimKerja]);
    }

    public function render()
    {
        return view('livewire.buat-struktur', [
            'anggotaTimKerja' => $this->anggotaTimKerja,
            'idTimKerja' => $this->idTimKerja,
        ]);
    }
}
