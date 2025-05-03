<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'nama_makanan',
        'harga',
        'harga_asli',
        'jenis',
        'foto',
        'deskripsi',
        'id_stan',
    ];

    public function menuDiskon()
    {
        return $this->hasMany(MenuDiskon::class, 'id_menu');
    }
    public function diskons()
    {
        return $this->belongsToMany(Diskon::class, 'menu_diskons');
    }
}
