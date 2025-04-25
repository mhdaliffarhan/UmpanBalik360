<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorPenilaian extends Model
{
    use HasFactory;

    protected $fillable = ['tim_kerja_id', 'indikator'];

    public function timkKerja()
    {
        return $this->belongsTo(TimKerja::class);
    }

    public function Daftarpertanyaan()
    {
        return $this->hasMany(DaftarPertanyaan::class);
    }
}
