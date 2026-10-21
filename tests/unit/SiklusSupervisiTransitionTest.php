<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\JadwalSupervisiModel;
use App\Models\CoachingTirtaModel;

/**
 * @internal
 */
final class SiklusSupervisiTransitionTest extends CIUnitTestCase
{
    private JadwalSupervisiModel $jadwalModel;
    private CoachingTirtaModel $coachingModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->jadwalModel = new JadwalSupervisiModel();
        $this->coachingModel = new CoachingTirtaModel();
    }

    /**
     * Menguji transisi siklus supervisi Kurikulum Merdeka yang sah secara berurutan:
     * Terjadwal -> pra_observasi -> observasi -> pasca_observasi -> Selesai
     */
    public function testValidSequentialTransitionsKurmer(): void
    {
        // 1. Guru mengajukan Formulir A
        $this->assertTrue(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_TERJADWAL,
                JadwalSupervisiModel::STATUS_PRA_OBSERVASI,
                true
            ),
            'Terjadwal harus dapat berpindah ke pra_observasi saat guru mengisi Form A'
        );

        // 2. Supervisor menyepakati Formulir A & Coaching TIRTA Pra
        $this->assertTrue(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_PRA_OBSERVASI,
                JadwalSupervisiModel::STATUS_OBSERVASI,
                true
            ),
            'pra_observasi harus dapat berpindah ke observasi saat supervisor menyepakati Form A'
        );

        // 3. Supervisor menyelesaikan penilaian observasi kelas
        $this->assertTrue(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_OBSERVASI,
                JadwalSupervisiModel::STATUS_PASCA_OBSERVASI,
                true
            ),
            'observasi harus dapat berpindah ke pasca_observasi saat penilaian Form B tuntas'
        );

        // 4. Guru & Supervisor menuntaskan Form C/D & evaluasi refleksi
        $this->assertTrue(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_PASCA_OBSERVASI,
                JadwalSupervisiModel::STATUS_SELESAI,
                true
            ),
            'pasca_observasi harus dapat berpindah ke Selesai saat evaluasi Form D tuntas'
        );
    }

    /**
     * Menguji transisi status ke status yang sama (idempotent update)
     */
    public function testIdempotentSameStatusTransitions(): void
    {
        $this->assertTrue(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_TERJADWAL,
                JadwalSupervisiModel::STATUS_TERJADWAL,
                true
            )
        );

        $this->assertTrue(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_OBSERVASI,
                JadwalSupervisiModel::STATUS_OBSERVASI,
                true
            )
        );
    }

    /**
     * Menguji bahwa lompatan tahap secara tidak sengaja (invalid skip) DITOLAK oleh State Machine
     */
    public function testInvalidSkipTransitionsBlocked(): void
    {
        // Tidak boleh langsung ke observasi tanpa melewati pra_observasi
        $this->assertFalse(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_TERJADWAL,
                JadwalSupervisiModel::STATUS_OBSERVASI,
                true
            ),
            'Jadwal Kurmer tidak boleh melompati tahap pra_observasi langsung ke observasi'
        );

        // Tidak boleh langsung ke pasca_observasi dari Terjadwal
        $this->assertFalse(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_TERJADWAL,
                JadwalSupervisiModel::STATUS_PASCA_OBSERVASI,
                true
            )
        );

        // Tidak boleh langsung Selesai dari Terjadwal pada jadwal Kurmer
        $this->assertFalse(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_TERJADWAL,
                JadwalSupervisiModel::STATUS_SELESAI,
                true
            )
        );

        // Tidak boleh melompati observasi dari pra_observasi langsung ke pasca_observasi
        $this->assertFalse(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_PRA_OBSERVASI,
                JadwalSupervisiModel::STATUS_PASCA_OBSERVASI,
                true
            )
        );

        // Tidak boleh langsung Selesai dari observasi tanpa tahap pasca_observasi
        $this->assertFalse(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_OBSERVASI,
                JadwalSupervisiModel::STATUS_SELESAI,
                true
            ),
            'Jadwal Kurmer harus melewati pasca_observasi (Form C & D) sebelum selesai'
        );

        // Status Selesai adalah terminal state, tidak boleh di-rollback
        $this->assertFalse(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_SELESAI,
                JadwalSupervisiModel::STATUS_PRA_OBSERVASI,
                true
            )
        );
    }

    /**
     * Menguji alur non-Kurmer (reguler/tradisional) tetap berjalan fleksibel
     */
    public function testNonKurmerTransitions(): void
    {
        $this->assertTrue(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_TERJADWAL,
                JadwalSupervisiModel::STATUS_SELESAI,
                false
            ),
            'Alur non-Kurmer diperbolehkan langsung selesai setelah penilaian reguler'
        );

        $this->assertTrue(
            $this->jadwalModel->isValidTransition(
                JadwalSupervisiModel::STATUS_TERJADWAL,
                JadwalSupervisiModel::STATUS_OBSERVASI,
                false
            )
        );
    }

    /**
     * Menguji helper canAccessObservasi
     */
    public function testCanAccessObservasiHelper(): void
    {
        // Kurmer: hanya boleh jika status sudah observasi, pasca_observasi, atau Selesai
        $this->assertFalse($this->jadwalModel->canAccessObservasi(JadwalSupervisiModel::STATUS_TERJADWAL, true));
        $this->assertFalse($this->jadwalModel->canAccessObservasi(JadwalSupervisiModel::STATUS_PRA_OBSERVASI, true));
        $this->assertTrue($this->jadwalModel->canAccessObservasi(JadwalSupervisiModel::STATUS_OBSERVASI, true));
        $this->assertTrue($this->jadwalModel->canAccessObservasi(JadwalSupervisiModel::STATUS_PASCA_OBSERVASI, true));
        $this->assertTrue($this->jadwalModel->canAccessObservasi(JadwalSupervisiModel::STATUS_SELESAI, true));

        // Non-Kurmer: selalu diizinkan
        $this->assertTrue($this->jadwalModel->canAccessObservasi(JadwalSupervisiModel::STATUS_TERJADWAL, false));
    }

    /**
     * Menguji helper canAccessPascaObservasi
     */
    public function testCanAccessPascaObservasiHelper(): void
    {
        $this->assertFalse($this->jadwalModel->canAccessPascaObservasi(JadwalSupervisiModel::STATUS_TERJADWAL, true));
        $this->assertFalse($this->jadwalModel->canAccessPascaObservasi(JadwalSupervisiModel::STATUS_PRA_OBSERVASI, true));
        $this->assertFalse($this->jadwalModel->canAccessPascaObservasi(JadwalSupervisiModel::STATUS_OBSERVASI, true));
        $this->assertTrue($this->jadwalModel->canAccessPascaObservasi(JadwalSupervisiModel::STATUS_PASCA_OBSERVASI, true));
        $this->assertTrue($this->jadwalModel->canAccessPascaObservasi(JadwalSupervisiModel::STATUS_SELESAI, true));

        // Non-Kurmer: selalu diizinkan
        $this->assertTrue($this->jadwalModel->canAccessPascaObservasi(JadwalSupervisiModel::STATUS_TERJADWAL, false));
    }

    /**
     * Menguji integritas CoachingTirtaModel
     */
    public function testCoachingTirtaModelConfiguration(): void
    {
        $allowedFields = [
            'jadwal_id',
            'tahap',
            'tujuan_coaching',
            'identifikasi_kondisi',
            'rencana_aksi',
            'tanggung_jawab'
        ];

        // Pastikan model mengizinkan kolom-kolom TIRTA
        $this->assertSame('coaching_tirta_log', $this->coachingModel->table);
        $this->assertSame('id', $this->coachingModel->primaryKey);
        foreach ($allowedFields as $field) {
            $this->assertContains($field, $this->coachingModel->allowedFields);
        }
    }
}
