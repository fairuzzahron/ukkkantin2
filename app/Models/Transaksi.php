<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'tanggal',
        'id_stan',
        'id_siswa',
        'status'
    ];

    // Relationship with Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    // Relationship with Stan
    public function stan()
    {
        return $this->belongsTo(Stan::class, 'id_stan');
    }

    // Relationship with DetailTransaksi
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi');
    }
    
}
