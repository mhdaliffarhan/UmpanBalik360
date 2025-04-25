<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarPertanyaan extends Model
{
    use HasFactory;

    protected $fillable = ['indikator_penilaian_id', 'pertanyaan'];

    public function indikatorPenilaian()
    {
        return $this->belongsTo(IndikatorPenilaian::class);
    }
}
