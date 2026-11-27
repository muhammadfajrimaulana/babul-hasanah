<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kurikulum extends Model
{
    protected $table = 'kurikulum';

    protected $fillable = [
        'jenjang', 'tahun_ajaran', 'semester', 'nama', 'deskripsi',
        'status', 'created_by',
    ];

    public function item()
    {
        return $this->hasMany(KurikulumItem::class, 'kurikulum_id')->orderBy('urutan');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
