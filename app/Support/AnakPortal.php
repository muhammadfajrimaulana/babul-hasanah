<?php

namespace App\Support;

use App\Models\Santri;
use App\Models\Student;

/**
 * Satu anak yang tampil di Portal Wali Santri.
 *
 * Anak dapat berasal dari skema baru (`students`, terhubung lewat
 * `parent_email`/`user_id`) atau dari data lama (`santri`, terhubung lewat
 * `santri.user_id`). Kelas ini menyatukan keduanya agar header, dashboard,
 * dan tab portal memakai objek yang sama.
 */
class AnakPortal
{
    public function __construct(
        public string $key,
        public ?Student $student,
        public ?Santri $santri,
        public string $nama,
        public string $nis,
        public ?string $kelas,
        public ?string $jenjang,
        public ?string $alamat = null,
        public ?string $namaWali = null,
    ) {}

    public static function dariStudent(Student $student): self
    {
        $student->loadMissing(['kelas', 'santri']);

        return new self(
            key: 's-'.$student->id,
            student: $student,
            santri: $student->santri,
            nama: (string) $student->name,
            nis: (string) $student->nisn,
            kelas: $student->kelas?->name ?: null,
            jenjang: $student->kelas?->jenjang ?: $student->jenjang,
            alamat: $student->santri?->alamat,
            namaWali: $student->santri?->nama_wali,
        );
    }

    public static function dariSantri(Santri $santri): self
    {
        return new self(
            key: 'l-'.$santri->id,
            student: null,
            santri: $santri,
            nama: (string) $santri->nama_lengkap,
            nis: (string) $santri->nomor_induk,
            kelas: $santri->kelas ?: null,
            jenjang: $santri->jenjang_pendidikan ?: null,
            alamat: $santri->alamat,
            namaWali: $santri->nama_wali,
        );
    }

    public function studentId(): ?int
    {
        return $this->student?->id;
    }

    /** ID pada tabel lama `santri` (dipakai absensi dan keuangan lama). */
    public function santriId(): ?int
    {
        return $this->student?->santri_id ?: $this->santri?->id;
    }

    public function inisial(): string
    {
        $kata = preg_split('/\s+/', trim($this->nama)) ?: [];
        $inisial = '';

        foreach (array_slice($kata, 0, 2) as $bagian) {
            $inisial .= mb_strtoupper(mb_substr($bagian, 0, 1));
        }

        return $inisial ?: '?';
    }

    public function label(): string
    {
        $kelas = $this->kelas ? 'Kelas '.$this->kelas : 'Kelas belum diatur';

        return $this->nama.' — '.$kelas;
    }
}
