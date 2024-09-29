<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanAlat extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_lab',
        'id_alat',
        'id_peminjam',
        'jumlah_alat',
        'confirm_time',
        'return_time',
        'keterangan',
        'status',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'id_lab');
    }
    public function alat()
    {
        return $this->belongsTo(AlatLab::class, 'id_alat');
    }
    public function peminjam()
    {
        return $this->belongsTo(User::class, 'id_peminjam');
    }
}
