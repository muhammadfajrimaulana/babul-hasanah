<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Catatan pada kalender dashboard admin.
 */
class CatatanKalender extends Model
{
    protected $table = 'catatan_kalender';

    protected $fillable = ['user_id', 'tanggal', 'catatan', 'warna'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /** Admin yang menulis catatan. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
