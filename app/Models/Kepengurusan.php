<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kepengurusan extends Model
{
    protected $table = 'kepengurusan';
    protected $fillable = ['nama', 'jabatan', 'foto', 'urutan'];
}