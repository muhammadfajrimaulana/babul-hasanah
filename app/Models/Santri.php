<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Santri extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Menyesuaikan dengan nama tabel di database migrasi kita
    protected $table = 'santri';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_LULUS = 'lulus';

    public const STATUS_KELUAR = 'keluar';

    public const STATUS_ARSIP = 'arsip';

    /** Pilihan jenis kelamin untuk komposisi santri. */
    public const JENIS_KELAMIN = [
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
    ];

    protected $fillable = [
        'user_id',
        'pendaftaran_online_id',
        'nomor_induk',
        'nama_lengkap',
        'jenis_kelamin',
        'jenjang_pendidikan',
        'kelas',
        'kelas_id',
        'kamar',
        'status_kelulusan',
        'tahun_lulus',
        'alamat',
        'nama_wali',
        'email_santri',
    ];

    /** Apakah kolom tertentu sudah tersedia (sebelum migrasi dijalankan). */
    public static function siapKolom(string $kolom): bool
    {
        try {
            return Schema::hasColumn('santri', $kolom);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /** Email santri dipakai sebagai username akun alumni saat kelulusan. */
    public static function siapEmailSantri(): bool
    {
        return self::siapKolom('email_santri');
    }

    /** Apakah kolom jenis kelamin sudah tersedia (sebelum migrasi). */
    public static function siapJenisKelamin(): bool
    {
        return self::siapKolom('jenis_kelamin');
    }

    /** Terima "L"/"P" maupun label lengkap dari CSV atau arsip PPDB. */
    public static function normalisasiJenisKelamin(mixed $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }

        $teks = strtolower(trim((string) $nilai));

        return match (true) {
            $teks === 'l', str_starts_with($teks, 'laki'), $teks === 'pria' => 'L',
            $teks === 'p', str_starts_with($teks, 'perempuan'), $teks === 'wanita' => 'P',
            default => null,
        };
    }

    public function labelJenisKelamin(): string
    {
        return self::JENIS_KELAMIN[$this->jenis_kelamin] ?? '-';
    }

    // Relasi ke tabel users (akun wali utama)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Arsip pendaftaran online yang menjadi sumber data santri ini
    public function pendaftaranOnline()
    {
        return $this->belongsTo(PendaftaranOnline::class, 'pendaftaran_online_id');
    }

    /** Semua akun wali (ayah/ibu/wali) yang tertaut ke santri ini. */
    public function waliUsers()
    {
        return $this->belongsToMany(User::class, 'santri_wali', 'santri_id', 'user_id')
            ->withPivot(['hubungan', 'utama'])
            ->withTimestamps();
    }

    /** Kelas resmi (satu kelas = satu wali kelas). */
    public function kelasModel()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke tabel absensi
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'santri_id');
    }

    /** Nilai akademik santri (semua kategori & semester). */
    public function nilai()
    {
        return $this->hasMany(NilaiSantri::class, 'santri_id');
    }

    /** Rapor rekap per tahun ajaran & semester. */
    public function rapor()
    {
        return $this->hasMany(RaporSantri::class, 'santri_id');
    }

    /** Hafalan / setoran santri. */
    public function hafalan()
    {
        return $this->hasMany(Hafalan::class, 'santri_id');
    }

    /** Pelanggaran santri (tetap terlihat oleh alumni). */
    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'santri_id');
    }

    /** Izin pulang santri. */
    public function izin()
    {
        return $this->hasMany(Izin::class, 'santri_id');
    }

    /** Riwayat kenaikan kelas / pindah jenjang. */
    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'santri_id');
    }

    /** Dokumen final (ijazah, transkrip, sertifikat). */
    public function dokumen()
    {
        return $this->hasMany(DokumenSantri::class, 'santri_id');
    }

    /** Pengajuan perubahan data dari wali. */
    public function pengajuanPerubahan()
    {
        return $this->hasMany(PengajuanPerubahan::class, 'santri_id');
    }

    /** Data alumni yang dibuat saat santri ditandai lulus. */
    public function alumni()
    {
        return $this->hasOne(Alumni::class, 'santri_id');
    }

    /** Apakah santri sudah dinyatakan lulus. */
    public function getSudahLulusAttribute(): bool
    {
        return ($this->status_kelulusan ?? self::STATUS_AKTIF) === self::STATUS_LULUS;
    }

    /** Apakah santri sudah tidak aktif (lulus/keluar/arsip). */
    public function getNonAktifAttribute(): bool
    {
        return in_array($this->status_kelulusan, [self::STATUS_LULUS, self::STATUS_KELUAR, self::STATUS_ARSIP], true);
    }

    public function labelStatus(): string
    {
        return match ($this->status_kelulusan) {
            self::STATUS_LULUS => 'Lulus',
            self::STATUS_KELUAR => 'Keluar',
            self::STATUS_ARSIP => 'Diarsipkan',
            default => 'Aktif',
        };
    }

    /** Wali kelas saat ini (jika kelas resmi sudah diatur). */
    public function waliKelas(): ?Guru
    {
        return $this->kelasModel?->waliKelas;
    }
}
