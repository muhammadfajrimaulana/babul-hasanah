<?php

namespace App\Models;

use App\Support\Jenjang;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = ['judul', 'isi', 'tanggal', 'target_jenjang', 'dibuat_oleh'];

    protected $casts = ['tanggal' => 'date'];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function labelTarget(): string
    {
        return $this->target_jenjang ? Jenjang::label($this->target_jenjang) : 'Semua Jenjang';
    }

    /** Pengumuman yang tampil untuk satu jenjang (termasuk pengumuman global). */
    public function scopeUntukJenjang(Builder $query, ?string $jenjang): Builder
    {
        $slug = Jenjang::normalisasi($jenjang);

        return $query->where(function (Builder $q) use ($slug) {
            $q->whereNull('target_jenjang');

            if ($slug) {
                $q->orWhere('target_jenjang', $slug);
            }
        });
    }
}
