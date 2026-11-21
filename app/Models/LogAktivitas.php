<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id', 'aksi', 'model_type', 'model_id', 'data',
        'alasan', 'ip', 'user_agent',
    ];

    protected $casts = ['data' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
