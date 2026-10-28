<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isValid ? 'Verifikasi Dokumen Sah - ' . esc($jadwal['nama_guru'] ?? '') : 'Verifikasi Gagal - Dokumen Tidak Valid' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --success: #10b981;
            --danger: #ef4444;
            --slate-800: #1e293b;
            --slate-600: #475569;
            --slate-100: #f1f5f9;
            --border: #e2e8f0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%);
            min-height: 100vh;
            color: var(--slate-800);
            padding: 24px 16px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .container {
            width: 100%;
            max-width: 680px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            border: 1px solid var(--border);
        }
        .header {
            background: #ffffff;
            padding: 24px 24px 20px 24px;
            text-align: center;
            border-bottom: 2px solid var(--border);
        }
        .header h1 {
            font-size: 15pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 9.5pt;
            color: var(--slate-600);
            line-height: 1.4;
        }
        .status-badge-container {
            padding: 24px 20px 16px 20px;
            text-align: center;
        }
        .status-card {
            border-radius: 16px;
            padding: 20px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
        }
        .status-card.valid {
            background: #ecfdf5;
            border: 1.5px solid #a7f3d0;
            color: #065f46;
        }
        .status-card.invalid {
            background: #fef2f2;
            border: 1.5px solid #fecaca;
            color: #991b1b;
        }
        .status-card i {
            font-size: 32px;
        }
        .status-title {
            font-size: 13pt;
            font-weight: 700;
            text-align: left;
        }
        .status-desc {
            font-size: 8.5pt;
            opacity: 0.9;
            text-align: left;
        }
        .body-content {
            padding: 0 24px 28px 24px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: 700;
            color: var(--slate-800);
            margin: 20px 0 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title i {
            color: var(--primary);
        }
        .meta-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            font-size: 9.5pt;
        }
        .meta-table tr:not(:last-child) td {
            border-bottom: 1px solid var(--border);
        }
        .meta-table td {
            padding: 10px 14px;
        }
        .meta-table td.label {
            width: 38%;
            background: #f8fafc;
            color: var(--slate-600);
            font-weight: 600;
        }
        .meta-table td.value {
            color: #0f172a;
            font-weight: 500;
        }
        /* Signatures timeline */
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 10px;
        }
        .timeline-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .timeline-item.signed {
            border-color: #86efac;
            background: #f0fdf4;
        }
        .timeline-item.pending {
            border-style: dashed;
            background: #f8fafc;
            opacity: 0.75;
        }
        .timeline-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }
        .timeline-item.signed .timeline-icon {
            background: #22c55e;
            color: #ffffff;
        }
        .timeline-item.pending .timeline-icon {
            background: #cbd5e1;
            color: #64748b;
        }
        .timeline-info {
            flex-grow: 1;
        }
        .role-tag {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 2px;
        }
        .timeline-item.signed .role-tag { color: #15803d; }
        .timeline-item.pending .role-tag { color: #64748b; }
        .signer-name {
            font-size: 10pt;
            font-weight: 700;
            color: #0f172a;
        }
        .signer-nip {
            font-size: 8.5pt;
            color: var(--slate-600);
        }
        .sign-date {
            font-size: 8pt;
            color: #166534;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .footer-note {
            margin-top: 24px;
            padding: 14px 16px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid var(--border);
            text-align: center;
            font-size: 8pt;
            color: var(--slate-600);
            line-height: 1.5;
        }
        .footer-note code {
            display: inline-block;
            word-break: break-all;
            background: #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            color: #334155;
            font-size: 7.5pt;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header Instansi -->
    <div class="header">
        <h1><?= esc($kop['baris_3'] ?? 'SUPERVISI GURU MADRASAH') ?></h1>
        <p><?= esc($kop['baris_4'] ?? 'Sistem Verifikasi Keabsahan Dokumen Digital') ?></p>
    </div>

    <!-- Status Badge -->
    <div class="status-badge-container">
        <?php if ($isValid): ?>
            <div class="status-card valid">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <div class="status-title">DOKUMEN TERVERIFIKASI & ASLI</div>
                    <div class="status-desc">
                        <?= $isFullySigned ? 'Telah disahkan lengkap oleh Kepala Madrasah secara digital.' : 'Dokumen sah terdaftar (Sedang dalam proses alur persetujuan).' ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="status-card invalid">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <div class="status-title">DOKUMEN TIDAK VALID / TIDAK DITEMUKAN</div>
                    <div class="status-desc">Token verifikasi tidak terdaftar pada sistem supervisi resmi.</div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="body-content">
        <?php if ($isValid && $jadwal): ?>
            <!-- Metadata Dokumen -->
            <div class="section-title">
                <i class="fa-solid fa-file-lines"></i> Informasi Dokumen
            </div>
            <table class="meta-table">
                <tr>
                    <td class="label">Jenis Dokumen</td>
                    <td class="value">Laporan Hasil Supervisi Akademik & Pembelajaran</td>
                </tr>
                <tr>
                    <td class="label">Guru yang Disupervisi</td>
                    <td class="value"><strong><?= esc($jadwal['nama_guru']) ?></strong> (NIP: <?= esc($jadwal['nip_guru'] ?: '-') ?>)</td>
                </tr>
                <tr>
                    <td class="label">Mata Pelajaran & Kelas</td>
                    <td class="value"><?= esc($jadwal['guru_mapel'] ?: ($jadwal['materi_supervisi'] ?: '-')) ?> / <?= esc($jadwal['nama_kelas'] ?: '-') ?></td>
                </tr>
                <tr>
                    <td class="label">Tahun Ajaran / Semester</td>
                    <td class="value"><?= esc($jadwal['tahun_ajar'] ?? '-') ?> (Semester <?= esc($jadwal['semester'] ?? '-') ?>)</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Pelaksanaan</td>
                    <td class="value"><?= date('d F Y', strtotime($jadwal['tanggal_supervisi'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Status Pengesahan</td>
                    <td class="value">
                        <?php if ($isFullySigned): ?>
                            <span style="color: #166534; font-weight: 700;"><i class="fa-solid fa-lock"></i> Final / Disahkan Lengkap</span>
                        <?php else: ?>
                            <span style="color: #b45309; font-weight: 700;"><i class="fa-solid fa-clock"></i> Belum Lengkap (Menunggu Pengesahan Akhir)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <!-- Riwayat Penandatanganan Digital (Sequential Timeline) -->
            <div class="section-title">
                <i class="fa-solid fa-signature"></i> Riwayat Penandatanganan Digital
            </div>
            <div class="timeline">
                <!-- 1. Guru -->
                <?php $guruSig = $allSignatures['guru'] ?? null; ?>
                <div class="timeline-item <?= $guruSig ? 'signed' : 'pending' ?>">
                    <div class="timeline-icon">
                        <i class="fa-solid <?= $guruSig ? 'fa-check' : 'fa-hourglass' ?>"></i>
                    </div>
                    <div class="timeline-info">
                        <div class="role-tag">1. Guru yang Disupervisi</div>
                        <div class="signer-name"><?= esc($jadwal['nama_guru']) ?></div>
                        <div class="signer-nip">NIP: <?= esc($jadwal['nip_guru'] ?: '-') ?></div>
                        <?php if ($guruSig): ?>
                            <div class="sign-date">
                                <i class="fa-solid fa-circle-check"></i>
                                Ditandatangani pada: <?= date('d/m/Y', strtotime($guruSig['tanggal_ttd'])) ?> (Waktu sistem: <?= date('d/m/Y H:i', strtotime($guruSig['signed_at'])) ?> WIB)
                            </div>
                        <?php else: ?>
                            <div style="font-size: 8pt; color: #94a3b8; margin-top: 4px;">Menunggu tanda tangan digital dari Guru</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. Supervisor -->
                <?php $spvSig = $allSignatures['supervisor'] ?? null; ?>
                <div class="timeline-item <?= $spvSig ? 'signed' : 'pending' ?>">
                    <div class="timeline-icon">
                        <i class="fa-solid <?= $spvSig ? 'fa-check' : 'fa-hourglass' ?>"></i>
                    </div>
                    <div class="timeline-info">
                        <div class="role-tag">2. Supervisor Penilai</div>
                        <div class="signer-name"><?= esc($jadwal['nama_supervisor'] ?: 'Supervisor') ?></div>
                        <div class="signer-nip">NIP: <?= esc($jadwal['nip_supervisor'] ?: '-') ?></div>
                        <?php if ($spvSig): ?>
                            <div class="sign-date">
                                <i class="fa-solid fa-circle-check"></i>
                                Ditandatangani pada: <?= date('d/m/Y', strtotime($spvSig['tanggal_ttd'])) ?> (Waktu sistem: <?= date('d/m/Y H:i', strtotime($spvSig['signed_at'])) ?> WIB)
                            </div>
                        <?php else: ?>
                            <div style="font-size: 8pt; color: #94a3b8; margin-top: 4px;">Menunggu tanda tangan digital dari Supervisor</div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 3. Kepala Madrasah -->
                <?php $kepalaSig = $allSignatures['kepala'] ?? null; ?>
                <div class="timeline-item <?= $kepalaSig ? 'signed' : 'pending' ?>">
                    <div class="timeline-icon">
                        <i class="fa-solid <?= $kepalaSig ? 'fa-check-double' : 'fa-hourglass' ?>"></i>
                    </div>
                    <div class="timeline-info">
                        <div class="role-tag">3. Pengesahan Kepala Madrasah</div>
                        <div class="signer-name"><?= esc($kepalaSig['signer_nama'] ?? get_nama_kepala()) ?></div>
                        <div class="signer-nip">NIP: <?= esc($kepalaSig['signer_nip'] ?? get_pengaturan('nip_kepala', '-')) ?></div>
                        <?php if ($kepalaSig): ?>
                            <div class="sign-date">
                                <i class="fa-solid fa-circle-check"></i>
                                Disahkan pada: <?= date('d/m/Y', strtotime($kepalaSig['tanggal_ttd'])) ?> (Waktu sistem: <?= date('d/m/Y H:i', strtotime($kepalaSig['signed_at'])) ?> WIB)
                            </div>
                        <?php else: ?>
                            <div style="font-size: 8pt; color: #94a3b8; margin-top: 4px;">Menunggu pengesahan akhir dari Kepala Madrasah</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="footer-note">
                <i class="fa-solid fa-shield-halved" style="color: #059669; font-size: 14px;"></i><br>
                Dokumen ini ditandatangani secara elektronik yang terverifikasi resmi oleh sistem informasi supervisi.<br>
                Kode Token: <code><?= esc($token) ?></code>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #64748b; font-size: 9.5pt; padding: 20px 0;">
                Token yang Anda pindai tidak cocok dengan arsip tanda tangan digital yang sah.<br>
                Mohon pastikan Anda memindai kode QR dari dokumen resmi yang dikeluarkan oleh sistem.
            </p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
