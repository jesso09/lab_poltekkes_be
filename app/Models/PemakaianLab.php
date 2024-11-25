<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemakaianLab extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_user',
        'id_lab',
        'tanggal_pemakaian',
        'confirm_time',
        'jam_mulai',
        'jam_selesai',
        'kegiatan',
        'status',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'id_lab');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
