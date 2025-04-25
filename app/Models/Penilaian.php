<?php

namespace App\Models;

use App\Models\Pertanyaan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_penilaian',
        'struktur_id',
        'maks_responden',
        'metode',
        'waktu_mulai',
        'waktu_selesai',
    ];

    public function bobotPenilaian()
    {
        return $this->hasOne(BobotPenilaian::class);
    }

    public function pertanyaans()
    {
        return $this->hasMany(Pertanyaan::class);
    }

    public function struktur()
    {
        return $this->belongsTo(Struktur::class);
    }
}
