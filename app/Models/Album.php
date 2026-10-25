<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['judul', 'kategori', 'tanggal', 'deskripsi', 'sampul'];

    protected $casts = ['tanggal' => 'date'];

    public function media()
    {
        return $this->hasMany(AlbumMedia::class)->orderBy('urutan');
    }
}
