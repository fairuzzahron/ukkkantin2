<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuDiskon extends Model
{

    protected $table = 'menu_diskons';

    protected $fillable = [
        'id_menu',
        'id_diskon',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    public function diskon()
    {
        return $this->belongsTo(Diskon::class, 'id_diskon');
    }
}
