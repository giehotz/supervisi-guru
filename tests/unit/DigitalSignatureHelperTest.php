<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\DokumenSignatureModel;

/**
 * @internal
 */
final class DigitalSignatureHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('signature');

        require_once APPPATH . 'Database/Migrations/2026-10-27-195500_CreateDokumenSignaturesTable.php';
        $migration = new \App\Database\Migrations\CreateDokumenSignaturesTable();
        try {
            $migration->up();
        } catch (\Throwable $e) {
            // Table might already exist in memory
        }
    }

    public function testGenerateQrDataUriProducesValidBase64(): void
    {
        $testUrl = 'https://example.com/verifikasi/dokumen/testtoken123';
        $dataUri = generate_qr_data_uri($testUrl);

        $this->assertIsString($dataUri);
        $this->assertNotEmpty($dataUri);
        $this->assertStringStartsWith('data:image/', $dataUri);
        $this->assertStringContainsString('base64,', $dataUri);
    }

    public function testGetVerificationUrl(): void
    {
        $token = 'abc123xyz456';
        $url = get_verification_url($token);

        $this->assertIsString($url);
        $this->assertStringContainsString('verifikasi/dokumen/' . $token, $url);
    }

    public function testRenderSignatureBlockDompdfPolos(): void
    {
        $html = render_signature_block_dompdf(
            'Guru yang Disupervisi',
            'Ahmad Fauzi, S.Pd.',
            '198501012010011001',
            null,
            true
        );

        $this->assertStringContainsString('Guru yang Disupervisi', $html);
        $this->assertStringContainsString('Ahmad Fauzi, S.Pd.', $html);
        $this->assertStringContainsString('198501012010011001', $html);
        $this->assertStringNotContainsString('data:image/', $html);
        $this->assertStringNotContainsString('[ Belum Ditandatangani ]', $html);
    }

    public function testRenderSignatureBlockDompdfUnsigned(): void
    {
        $html = render_signature_block_dompdf(
            'Supervisor',
            'Drs. H. Mulyadi',
            '197001011995031002',
            null,
            false
        );

        $this->assertStringContainsString('Supervisor', $html);
        $this->assertStringContainsString('[ Belum Ditandatangani ]', $html);
        $this->assertStringNotContainsString('Ditandatangani Elektronik', $html);
    }

    public function testRenderSignatureBlockDompdfSigned(): void
    {
        $sigRecord = [
            'token_verifikasi' => 'dummytoken64characterslongabcdef1234567890abcdef1234567890abcdef12',
            'tanggal_ttd'      => '2026-10-28',
            'signed_at'        => '2026-10-28 10:00:00',
        ];

        $html = render_signature_block_dompdf(
            'Kepala Madrasah',
            'Dr. H. Sulaiman, M.Pd.',
            '196805121994031001',
            $sigRecord,
            false
        );

        $this->assertStringContainsString('Kepala Madrasah', $html);
        $this->assertStringContainsString('Ditandatangani Elektronik', $html);
        $this->assertStringContainsString('28/10/2026', $html);
        $this->assertStringContainsString('data:image/', $html);
        $this->assertStringContainsString('Dr. H. Sulaiman, M.Pd.', $html);
    }

    public function testRenderPdfDraftWatermark(): void
    {
        $watermarkDraft = render_pdf_draft_watermark(true);
        $this->assertNotEmpty($watermarkDraft);
        $this->assertStringContainsString('DRAFT / BELUM DISAHKAN', $watermarkDraft);

        $watermarkFinal = render_pdf_draft_watermark(false);
        $this->assertEmpty($watermarkFinal);
    }

    public function testSequentialCanSignRules(): void
    {
        $model = new DokumenSignatureModel();

        // 1. Guru can always sign first (if not signed)
        $canGuru = $model->canSign(999999, 'guru');
        $this->assertTrue($canGuru['allowed']);

        // 2. Supervisor cannot sign if guru has not signed
        $canSupervisor = $model->canSign(999999, 'supervisor');
        $this->assertFalse($canSupervisor['allowed']);
        $this->assertStringContainsString('Guru yang bersangkutan belum menandatangani', $canSupervisor['message']);

        // 3. Kepala cannot sign if guru or supervisor has not signed
        $canKepala = $model->canSign(999999, 'kepala');
        $this->assertFalse($canKepala['allowed']);

        // 4. Test hasAnySignature before and after inserting
        $this->assertFalse($model->hasAnySignature(999999));

        $model->insert([
            'jadwal_id'        => 999999,
            'dokumen_type'     => 'supervisi',
            'signer_role'      => 'guru',
            'user_id'          => 1,
            'signer_nama'      => 'Guru Test',
            'signer_nip'       => '123456',
            'token_verifikasi' => hash('sha256', 'testtoken'),
            'tanggal_ttd'      => date('Y-m-d'),
            'signed_at'        => date('Y-m-d H:i:s'),
        ]);

        $this->assertTrue($model->hasAnySignature(999999));

        // After guru signs, supervisor can now sign
        $canSupervisorNow = $model->canSign(999999, 'supervisor');
        $this->assertTrue($canSupervisorNow['allowed']);

        // But guru cannot sign again
        $canGuruAgain = $model->canSign(999999, 'guru');
        $this->assertFalse($canGuruAgain['allowed']);
        $this->assertStringContainsString('Anda sudah menandatangani', $canGuruAgain['message']);
    }

    public function testAdminBatchSignWorkflow(): void
    {
        $model = new DokumenSignatureModel();
        $testJadwalId = 888888;

        $this->assertFalse($model->isFullySigned($testJadwalId));

        // Simulate admin batch signing all three roles
        $roles = [
            'guru'       => ['nama' => 'Ahmad Guru, S.Pd.', 'nip' => '198001012005011001'],
            'supervisor' => ['nama' => 'Siti Supervisor, M.Pd.', 'nip' => '197502022000032001'],
            'kepala'     => ['nama' => 'Dr. H. Kepala, M.Ag.', 'nip' => '197003031995031001'],
        ];

        foreach ($roles as $roleKey => $data) {
            $model->insert([
                'jadwal_id'        => $testJadwalId,
                'dokumen_type'     => 'supervisi',
                'signer_role'      => $roleKey,
                'user_id'          => 1, // Admin as executor
                'signer_nama'      => $data['nama'],
                'signer_nip'       => $data['nip'],
                'token_verifikasi' => hash('sha256', $testJadwalId . $roleKey),
                'tanggal_ttd'      => '2026-10-28',
                'signed_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        // Verify all 3 signatures exist
        $sigs = $model->getSignaturesByJadwal($testJadwalId);
        $this->assertNotNull($sigs['guru']);
        $this->assertNotNull($sigs['supervisor']);
        $this->assertNotNull($sigs['kepala']);
        $this->assertEquals('Ahmad Guru, S.Pd.', $sigs['guru']['signer_nama']);
        $this->assertEquals('Siti Supervisor, M.Pd.', $sigs['supervisor']['signer_nama']);
        $this->assertEquals('Dr. H. Kepala, M.Ag.', $sigs['kepala']['signer_nama']);

        // Verify document is now fully signed
        $this->assertTrue($model->isFullySigned($testJadwalId));
    }

    public function testRenderSignatureBlockDompdfAllowsHtmlBrAndHandlesComma(): void
    {
        $html = render_signature_block_dompdf(
            'Mengetahui,<br>Kepala Madrasah / Penilai',
            'Dr. H. Sulaiman, M.Pd.',
            '196805121994031001',
            null,
            true,
            'Gisting,<br>28 Oktober 2026'
        );

        // Must preserve <br> tag for HTML/Dompdf layout instead of escaping to &lt;br&gt;
        $this->assertStringContainsString('Mengetahui,<br>Kepala Madrasah / Penilai,', $html);
        $this->assertStringNotContainsString('&lt;br&gt;', $html);
        $this->assertStringContainsString('Gisting,<br>28 Oktober 2026', $html);
    }
}
