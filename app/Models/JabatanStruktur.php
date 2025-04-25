<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JabatanStruktur extends Model
{
    use HasFactory;
    protected $fillable = ['struktur_id', 'nama_jabatan', 'atasan', 'level'];

    // Relasi untuk atasan
    public function parent()
    {
        return $this->belongsTo(JabatanStruktur::class, 'atasan');
    }

    // Relasi untuk anak jabatan
    public function children()
    {
        return $this->hasMany(JabatanStruktur::class, 'atasan');
    }

    public function struktur()
    {
        return $this->belongsTo(Struktur::class);
    }

    public function anggotaStruktur()
    {
        return $this->hasMany(AnggotaStruktur::class);
    }
}
