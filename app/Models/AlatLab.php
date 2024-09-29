<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatLab extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_lab',
        'foto_alat',
        'nama_alat',
        'jumlah',
        'keterangan',
    ];
}
