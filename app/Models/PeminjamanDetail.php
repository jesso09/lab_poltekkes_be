<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_peminjaman',
        'id_alat',
        'jumlah_alat',
        'confirm_time',
        'return_time',
        'status',
    ];

    public function alat()
    {
        return $this->belongsTo(AlatLab::class, 'id_alat');
    }

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanAlat::class, 'id_peminjaman');
    }
}