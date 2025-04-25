<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TimKerja;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;

class EditTimKerja extends Component
{
    public $namaTimKerja;
    public $deskripsiTimKerja;
    public $anggotaTimKerja = [];

    public $idTimKerja;

    public function mount($id)
    {
        $this->idTimKerja = $id;

        $timKerja = TimKerja::findOrFail($id);
        $this->namaTimKerja = $timKerja->nama_tim;
        $this->deskripsiTimKerja = $timKerja->deskripsi_tim;

        // Mendapatkan informasi anggota Tim Kerja dari database
        $anggota = $timKerja->anggotaTimKerja()->get();
        foreach ($anggota as $key => $value) {
            $this->anggotaTimKerja[] = [
                'email' => $value->user->email, // Mengambil email dari relasi user
                'nama' => $value->user->name,
                'role' => $value->role
            ];
        }
    }

    public function hapusAnggota($index)
    {
        unset($this->anggotaTimKerja[$index]);
        // Reindex array setelah penghapusan agar kunci array berurutan
        $this->anggotaTimKerja = array_values($this->anggotaTimKerja);
    }

    public function tambahInputAnggota()
    {
        $this->anggotaTimKerja[] = [
            'email' => '',
            'nama' => '',
            'role' => 'anggota'
        ];
    }

    public function simpan()
    {
        // dd($this->anggotaTimKerja);
        $this->validateData();

        // Simpan perubahan pada data Tim Kerja
        $timKerja = TimKerja::findOrFail($this->idTimKerja);
        $timKerja->nama_tim = $this->namaTimKerja;
        $timKerja->deskripsi_tim = $this->deskripsiTimKerja;
        $timKerja->save();

        // Simpan perubahan pada data Anggota Tim Kerja
        foreach ($this->anggotaTimKerja as $anggotaItem) {
            // Cari user berdasarkan email
            $user = User::where('email', $anggotaItem['email'])->first();
            if ($user) {
                // Jika user ditemukan, tambahkan atau perbarui sebagai anggota Tim Kerja
                $timKerja->anggotaTimKerja()->updateOrCreate(
                    ['user_id' => $user->id], // Cari berdasarkan user_id
                    ['role' => $anggotaItem['role']] // Simpan peran baru
                );
            }
        }
        toast('Perubahan tim kerja berhasil disimpan', 'success');

        return redirect()->route('detail-tim-kerja', ['id' => $this->idTimKerja]);
    }

    public function validateData()
    {
        // Validasi data sebelum menyimpan
        $this->validate([
            'namaTimKerja' => 'required|string|max:255',
            'anggotaTimKerja.*.email' => 'required|email',
            'anggotaTimKerja.*.role' => 'required',
        ]);
    }

    public function render()
    {
        return view('livewire.edit-timkerja', [
            'anggotaTimKerja' => $this->anggotaTimKerja,
            'id' => $this->idTimKerja
        ]);
    }
}
