<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnggotaTimKerja extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'tim_kerja_id', 'status', 'role'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function timKerja()
    {
        return $this->belongsTo(TimKerja::class);
    }
}
