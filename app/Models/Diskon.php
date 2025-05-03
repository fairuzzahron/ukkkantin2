<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{

    protected $table = 'diskons';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_diskon',
        'persentase_diskon',
        'tanggal_awal',
        'tanggal_akhir',
        'id_stan'
    ];

    public function menuDiskon()
    {
        return $this->hasMany(MenuDiskon::class, 'id_diskon');
    }
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_diskons');
    }
}
