<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogNilai extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_penilaian_id',
        'pertanyaans_id',
        'nilai',
    ];

    public function logPenilaian()
    {
        return $this->belongsTo(LogPenilaian::class);
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'pertanyaans_id');
    }
}
