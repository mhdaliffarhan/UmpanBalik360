<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaStruktur extends Model
{
    use HasFactory;

    protected $fillable = [
        'anggota_tim_kerja_id',
        'jabatan_struktur_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function struktur()
    {
        return $this->belongsTo(Struktur::class);
    }

    public function jabatanStruktur()
    {
        return $this->belongsTo(JabatanStruktur::class);
    }

    public function anggotaTimKerja()
    {
        return $this->belongsTo(AnggotaTimKerja::class);
    }
}
