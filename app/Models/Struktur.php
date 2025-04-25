<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Struktur extends Model
{
    use HasFactory;
    protected $fillable = ['nama_struktur', 'tim_kerja_id', 'jumlah_level'];

    public function timKerja()
    {
        return $this->belongsTo(TimKerja::class);
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }

    public function jabatanStruktur()
    {
        return $this->hasMany(JabatanStruktur::class);
    }
}
