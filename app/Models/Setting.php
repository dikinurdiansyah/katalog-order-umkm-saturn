<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    // Otomatis mengubah data JSON menjadi array PHP saat diambil
    protected $casts = [
        'value' => 'array',
    ];
}
