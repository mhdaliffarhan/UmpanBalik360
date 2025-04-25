<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'penilaian_id',
        'daftar_pertanyaan_id',
    ];

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class);
    }

    public function daftarPertanyaan()
    {
        return $this->belongsTo(DaftarPertanyaan::class);
    }

    public function logNilai()
    {
        return $this->hasMany(LogNilai::class);
    }
}
