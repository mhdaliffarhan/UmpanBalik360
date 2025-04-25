<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BobotPenilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'penilaian_id',
        'atasan',
        'sebaya',
        'bawahan',
        'diri_sendiri',
    ];

    public function penilaian()
    {
        return $this->belongsTo(Penilaian::class);
    }
}
