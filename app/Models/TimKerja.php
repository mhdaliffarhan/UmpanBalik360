<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimKerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tim',
        'deskripsi_tim',
        'user_id'
    ];

    public function struktur()
    {
        return $this->hasMany(Struktur::class);
    }

    public function anggotaTimKerja()
    {
        return $this->hasMany(AnggotaTimKerja::class);
    }
}
