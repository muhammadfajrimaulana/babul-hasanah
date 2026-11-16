<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Akses;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'avatar', 'jenjang'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Santri yang terhubung dengan akun wali ini (bisa lebih dari satu). */
    public function santri()
    {
        return $this->hasMany(Santri::class, 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /** Apakah user boleh membuka modul tertentu (lihat App\Support\Akses). */
    public function bisa(string $modul): bool
    {
        return Akses::bisa($this, $modul);
    }

    /** Nama route dashboard sesuai role user. */
    public function dashboardRoute(): string
    {
        return Akses::dashboardRoute($this);
    }

    /** Slug jenjang yang ditangani (khusus admin jenjang). */
    public function jenjangSlug(): ?string
    {
        return Akses::jenjangSlug($this);
    }

    /** Label jenjang untuk header dashboard. */
    public function namaJenjang(): string
    {
        return Akses::namaJenjang($this);
    }

    /** Label role yang ramah dibaca. */
    public function roleLabel(): string
    {
        return Akses::label($this->role);
    }

    public function isSuperAdmin(): bool
    {
        return Akses::superAdmin($this);
    }
}
