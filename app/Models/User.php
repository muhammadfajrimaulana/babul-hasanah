<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Akses;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password', 'role', 'phone', 'avatar', 'jenjang',
    'is_active', 'must_change_password', 'temp_password_expires_at', 'last_login_at',
])]
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
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
            'temp_password_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    /** Santri yang terhubung dengan akun wali ini (relasi utama lama). */
    public function santri()
    {
        return $this->hasMany(Santri::class, 'user_id');
    }

    /** Semua santri yang tertaut lewat pivot (mendukung ayah & ibu terpisah). */
    public function anak()
    {
        return $this->belongsToMany(Santri::class, 'santri_wali', 'user_id', 'santri_id')
            ->withPivot(['hubungan', 'utama'])
            ->withTimestamps();
    }

    /**
     * Query gabungan anak: tautan lama (santri.user_id) + pivot santri_wali.
     *
     * Dipakai portal wali agar anak yang ditautkan lewat ayah/ibu terpisah
     * tetap tampil meskipun bukan wali utama.
     */
    public function anakQuery(): Builder
    {
        return Santri::query()->where(function (Builder $q) {
            $q->where('user_id', $this->id)
                ->orWhereIn('id', $this->anak()->select('santri.id'));
        });
    }

    /** @return Collection<int, Santri> */
    public function daftarAnak(): Collection
    {
        return $this->anakQuery()->orderBy('nama_lengkap')->get();
    }

    /** Apakah santri tertentu milik akun wali ini. */
    public function memilikiSantri(int $santriId): bool
    {
        return $this->anakQuery()->whereKey($santriId)->exists();
    }

    /** Data alumni milik akun ini (role alumni). */
    public function alumni()
    {
        return $this->hasOne(Alumni::class, 'user_id');
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

    public function isAdminJenjang(): bool
    {
        return Akses::normalisasi($this->role) === 'admin';
    }

    public function isAlumni(): bool
    {
        return Akses::normalisasi($this->role) === 'alumni';
    }

    /** Akun masih memakai password sementara yang sudah kedaluwarsa. */
    public function passwordSementaraKedaluwarsa(): bool
    {
        return $this->must_change_password
            && $this->temp_password_expires_at
            && now()->greaterThan($this->temp_password_expires_at);
    }
}
