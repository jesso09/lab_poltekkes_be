<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_lab',
        'hari',
        'mulai',
        'selesai',
        'praktikan',
        'semester',
        'mata_kuliah',
        'plp',
    ];

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'id_lab');
    }
}