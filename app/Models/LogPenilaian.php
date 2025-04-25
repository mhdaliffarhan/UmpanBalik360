<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPenilaian extends Model
{
    use HasFactory;

    protected $fillable = [
        'penilaian_id',
        'penilai_id',
        'dinilai_id',
        'role_penilai',
        'status',
    ];

    public function penilai()
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function dinilai()
    {
        return $this->belongsTo(User::class, 'dinilai_id');
    }

    public function logNilai()
    {
        return $this->hasMany(LogNilai::class);
    }
}
