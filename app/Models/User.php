<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\Akses;
use App\Support\RoleSistem;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'phone', 'avatar', 'jenjang'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

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
            'verified_at' => 'datetime',
            'password_generated_at' => 'datetime',
        ];
    }

    /** Status akun: `active` atau `pending`. */
    public function statusAkun(): string
    {
        $status = strtolower((string) ($this->status ?? ''));

        return $status === 'active' ? 'active' : 'pending';
    }

    /** Apakah akun sudah boleh masuk (sudah diverifikasi). */
    public function sudahAktif(): bool
    {
        return $this->statusAkun() === 'active';
    }

    /** Apakah akun masih menunggu verifikasi Admin Jenjang. */
    public function menungguVerifikasi(): bool
    {
        return ! $this->sudahAktif();
    }

    /** Label status ramah-baca (Aktif / Pending). */
    public function labelStatus(): string
    {
        return $this->sudahAktif() ? 'Aktif' : 'Pending';
    }

    /** Santri yang terhubung dengan akun wali ini (bisa lebih dari satu). */
    public function santri()
    {
        return $this->hasMany(Santri::class, 'user_id');
    }

    /** Siswa (skema Modul 1) yang terhubung dengan akun wali ini. */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /**
     * Nama peran resmi (Spatie) milik pengguna.
     *
     * Bila akun lama belum memiliki peran Spatie, nilainya ditebak dari kolom
     * `role` + `jenjang` supaya tampilan tetap benar sebelum seeder dijalankan.
     */
    public function peranSistem(): ?string
    {
        $dariSpatie = RoleSistem::normalisasi($this->getRoleNames()->first());

        return $dariSpatie ?: RoleSistem::dariLegacy($this->role, $this->jenjang);
    }

    /** Label peran resmi untuk tampilan. */
    public function labelPeran(): string
    {
        $peran = $this->peranSistem();

        return $peran ? RoleSistem::label($peran) : Akses::label($this->role);
    }

    /**
     * Tetapkan peran resmi + sinkronkan kolom lama.
     * Untuk Admin Jenjang, sertakan jenjang yang ditangani (slug).
     */
    public function setPeran(?string $role, ?string $jenjang = null): void
    {
        RoleSistem::sinkron($this, $role, $jenjang);
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
