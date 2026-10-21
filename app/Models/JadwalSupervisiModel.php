<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalSupervisiModel extends Model
{
    protected $table = 'jadwal_supervisi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tahun_ajar_id',
        'guru_id',
        'supervisor_id',
        'mata_pelajaran',
        'indikator_pmm_id',
        'kelas',
        'kelas_id',
        'jam_ke',
        'hari',
        'tanggal_supervisi',
        'tanggal_pra_observasi',
        'tanggal_pasca_observasi',
        'waktu_dari',
        'waktu_sampai',
        'materi_supervisi',
        'status'
    ];
    protected $useTimestamps = false;

    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    // Status siklus supervisi
    public const STATUS_TERJADWAL       = 'Terjadwal';
    public const STATUS_PRA_OBSERVASI   = 'pra_observasi';
    public const STATUS_OBSERVASI       = 'observasi';
    public const STATUS_PASCA_OBSERVASI = 'pasca_observasi';
    public const STATUS_SELESAI         = 'Selesai';

    /**
     * Memvalidasi apakah transisi dari status saat ini ke status berikutnya sah (State Machine)
     */
    public function isValidTransition(string $currentStatus, string $nextStatus, bool $isKurmer = true): bool
    {
        // Transisi ke status yang sama selalu diperbolehkan (idempotent)
        if ($currentStatus === $nextStatus) {
            return true;
        }

        if ($isKurmer) {
            $validTransitions = [
                self::STATUS_TERJADWAL       => [self::STATUS_PRA_OBSERVASI],
                self::STATUS_PRA_OBSERVASI   => [self::STATUS_OBSERVASI],
                self::STATUS_OBSERVASI       => [self::STATUS_PASCA_OBSERVASI],
                self::STATUS_PASCA_OBSERVASI => [self::STATUS_SELESAI],
                self::STATUS_SELESAI         => [] // Status terminal, tidak boleh mundur
            ];

            return isset($validTransitions[$currentStatus]) && in_array($nextStatus, $validTransitions[$currentStatus], true);
        }

        // Alur Reguler / Non-Kurmer
        $validTransitionsNonKurmer = [
            self::STATUS_TERJADWAL => [self::STATUS_OBSERVASI, self::STATUS_SELESAI],
            self::STATUS_OBSERVASI => [self::STATUS_SELESAI],
            self::STATUS_SELESAI   => []
        ];

        return isset($validTransitionsNonKurmer[$currentStatus]) && in_array($nextStatus, $validTransitionsNonKurmer[$currentStatus], true);
    }

    /**
     * Mengecek apakah jadwal sudah boleh dinilai di kelas (observasi)
     */
    public function canAccessObservasi(string $status, bool $isKurmer = true): bool
    {
        if (!$isKurmer) {
            return true;
        }

        return in_array($status, [self::STATUS_OBSERVASI, self::STATUS_PASCA_OBSERVASI, self::STATUS_SELESAI], true);
    }

    /**
     * Mengecek apakah jadwal sudah boleh memasuki tahap refleksi tindak lanjut (pasca-observasi)
     */
    public function canAccessPascaObservasi(string $status, bool $isKurmer = true): bool
    {
        if (!$isKurmer) {
            return true;
        }

        return in_array($status, [self::STATUS_PASCA_OBSERVASI, self::STATUS_SELESAI], true);
    }
}