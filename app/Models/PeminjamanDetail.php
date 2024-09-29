<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_lab',
        'nama_alat',
        'jumlah_alat',
        'nama_peminjam',
        'role_peminjam',
        'confirm_time',
        'return_time',
        'keterangan',
        'status',
    ];
}
