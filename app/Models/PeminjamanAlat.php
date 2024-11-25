<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanAlat extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_peminjam',
        'id_lab',
        'start_borrow',
        'end_borrow',
        'confirm_time',
        'return_time',
        'keterangan',
    ];
    public function peminjam()
    {
        return $this->belongsTo(User::class, 'id_peminjam');
    }
    
    public function lab()
    {
        return $this->belongsTo(Lab::class, 'id_lab');
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(PeminjamanDetail::class, 'id_peminjaman');
    }
}
