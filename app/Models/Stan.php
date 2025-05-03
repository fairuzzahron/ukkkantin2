<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stan extends Model
{
    protected $fillable = [
        'nama_stan',
        'nama_pemilik',
        'telp',
        'id_user',
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
