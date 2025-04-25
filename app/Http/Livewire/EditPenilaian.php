<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Penilaian;
use RealRashid\SweetAlert\Facades\Alert;

class EditPenilaian extends Component
{
    public $namaPenilaian;
    public $showSuccesNotification;
    public $idPenilaian;
    public $penilaian = [
        'namaPenilaian' => '',
        'mulai' => '',
        'selesai' => '',
    ];

    public function mount($id)
    {
        $this->idPenilaian = $id;

        $penilaian = Penilaian::findOrFail($id);
        $this->penilaian['namaPenilaian'] = $penilaian->nama_penilaian;
        $this->penilaian['mulai'] = $penilaian->waktu_mulai;
        $this->penilaian['selesai'] = $penilaian->waktu_selesai;
        // // dd($penilaian);
        // dd($this->penilaian);
    }

    public function simpan()
    {
        $this->validateData();
        $penilaian = Penilaian::findOrFail($this->idPenilaian);
        $penilaian->nama_penilaian = $this->penilaian['namaPenilaian'];
        $penilaian->waktu_mulai = $this->penilaian['mulai'];
        $penilaian->waktu_selesai = $this->penilaian['selesai'];
        $penilaian->save();

        Alert::success('Berhasil', 'Berhasil Mengedit Penilaian');

        return redirect()->route('detail-penilaian', ['id' => $this->idPenilaian]);
    }

    public function validateData()
    {
        $this->validate([
            'penilaian.namaPenilaian' => 'required|string|max:255',
            'penilaian.mulai' => 'required|date', // Menjamin tanggal mulai diisi dan merupakan format tanggal yang valid
            'penilaian.selesai' => 'required|date|after:penilaian.mulai', // Menjamin tanggal selesai diisi, merupakan format tanggal yang valid, dan setelah tanggal mulai
        ], [
            'penilaian.namaPenilaian.required' => 'Nama Penilaian harus diisi.',
            'penilaian.mulai.required' => 'Tanggal Mulai harus diisi.',
            'penilaian.mulai.date' => 'Format Tanggal Mulai tidak valid.',
            'penilaian.selesai.required' => 'Tanggal Selesai harus diisi.',
            'penilaian.selesai.date' => 'Format Tanggal Selesai tidak valid.',
            'penilaian.selesai.after' => 'Tanggal Selesai harus setelah Tanggal Mulai.',
        ]);
    }
    public function render()
    {
        return view('livewire.edit-penilaian');
    }
}
