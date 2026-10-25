<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumMedia extends Model
{
    protected $table = 'album_media';

    protected $fillable = ['album_id', 'tipe', 'path', 'url', 'poster', 'keterangan', 'urutan'];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
