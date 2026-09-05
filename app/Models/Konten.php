<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konten extends Model
{
    protected $table = 'konten';
    protected $fillable = ['key', 'value', 'type'];

    // Helper cepat untuk ambil nilai konten berdasarkan key
    public static function getVal($key, $default = null)
    {
        $item = self::where('key', $key)->first();
        return $item ? $item->value : $default;
    }
}