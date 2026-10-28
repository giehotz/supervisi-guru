<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($jenisPenilaian['nama'] ?? 'Instrumen Telaah Perangkat Ajar Kurikulum Merdeka') ?></title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #111;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
        }
        .header h3 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
        }
        .header h4 {
            margin: 3px 0;
            font-size: 11pt;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            color: #444;
        }
        .doc-title {
            text-align: center;
            margin: 12px 0;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 11.5pt;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 9.5pt;
            font-weight: bold;
            color: #333;
        }
        table.meta {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9.5pt;
            border-collapse: collapse;
        }
        table.meta td {
            padding: 3px 4px;
            vertical-align: top;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9pt;
        }
        table.data th, table.data td {
            border: 1px solid #333;
            padding: 5px 6px;
            vertical-align: top;
        }
        table.data th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
        .rubrik-guide {
            background-color: #fafafa;
            border: 1px solid #ddd;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        .rubrik-guide strong {
            display: block;
            margin-bottom: 2px;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .signature-table td {
            padding: 4px;
            text-align: center;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>PEMERINTAH DAERAH / SATUAN PENDIDIKAN</h3>
        <h4>SUPERVISI PERANGKAT AJAR KURIKULUM MERDEKA</h4>
        <p>Berdasarkan Peraturan Direktur Jenderal GTK Kemendikbudristek No. 7607/B.B1/HK.03/2023</p>
    </div>

    <div class="doc-title">
        <h3><?= esc($jenisPenilaian['nama'] ?? 'INSTRUMEN TELAAH PERANGKAT AJAR') ?></h3>
        <p>Tahun Ajaran <?= esc($schedule['tahun_ajar'] ?? date('Y')) ?> / <?= esc($schedule['semester'] ?? 'Ganjil') ?></p>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 18%;"><strong>Nama Guru</strong></td>
            <td style="width: 42%;">: <?= esc($schedule['nama_guru']) ?></td>
            <td style="width: 18%;"><strong>Hari / Tanggal</strong></td>
            <td style="width: 22%;">: <?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
        </tr>
        <tr>
            <td><strong>NIP Guru</strong></td>
            <td>: <?= esc($schedule['nip_guru'] ?? '-') ?></td>
            <td><strong>Kelas / Fase</strong></td>
            <td>: <?= esc($schedule['nama_kelas'] ?? $schedule['kelas'] ?? '-') ?> (Fase <?= esc($schedule['kelas_fase'] ?? 'E/F') ?>)</td>
        </tr>
        <tr>
            <td><strong>Mata Pelajaran</strong></td>
            <td>: <?= esc($schedule['mata_pelajaran'] ?? $schedule['guru_mata_pelajaran'] ?? '-') ?></td>
            <td><strong>Supervisor / Penilai</strong></td>
            <td>: <?= esc($schedule['nama_supervisor'] ?? 'Supervisor') ?></td>
        </tr>
    </table>

    <div class="rubrik-guide">
        <strong>Pedoman Skala Penilaian Rubrik Telaah (1–4):</strong>
        <span>1 = Kurang / Tidak Sesuai &nbsp;|&nbsp; 2 = Cukup / Sebagian Sesuai &nbsp;|&nbsp; 3 = Baik / Sesuai &nbsp;|&nbsp; 4 = Sangat Baik / Melebihi Standar</span>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Komponen / Aspek yang Ditelaah</th>
                <th style="width: 40%;">Fokus / Indikator Kesesuaian</th>
                <th style="width: 10%;">Skor (1–4)</th>
                <th style="width: 20%;">Catatan / Saran Perbaikan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            $totalSkor = 0; 
            $skorMaksimal = count($aspekList) * 4;
            foreach ($aspekList as $aspek): 
                $skor = isset($detailResults[$aspek['id']]) ? (int)$detailResults[$aspek['id']]['skor'] : 0;
                $catatan = isset($detailResults[$aspek['id']]) ? ($detailResults[$aspek['id']]['catatan'] ?? '-') : '-';
                $totalSkor += $skor;
            ?>
            <tr>
                <td class="center"><?= $no++ ?></td>
                <td><strong><?= esc($aspek['nama_aspek']) ?></strong></td>
                <td>
                    <?php if (!empty($aspek['fokus_perilaku'])): ?>
                        <div><?= esc($aspek['fokus_perilaku']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($aspek['perilaku_dianjurkan'])): ?>
                        <div style="font-size: 8pt; color: #006600; margin-top: 2px;">
                            <strong>Kriteria Baik:</strong> <?= esc($aspek['perilaku_dianjurkan']) ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="center" style="font-size: 11pt; font-weight: bold;">
                    <?= $skor > 0 ? $skor : '-' ?>
                </td>
                <td><?= esc($catatan) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <?php 
                $nilaiPersen = $skorMaksimal > 0 ? round(($totalSkor / $skorMaksimal) * 100, 2) : 0;
                if ($nilaiPersen >= 86) {
                    $predikat = 'Baik Sekali (A)';
                } elseif ($nilaiPersen >= 70) {
                    $predikat = 'Baik (B)';
                } elseif ($nilaiPersen >= 55) {
                    $predikat = 'Cukup (C)';
                } else {
                    $predikat = 'Kurang (D)';
                }
            ?>
            <tr style="background-color: #f7f7f7; font-weight: bold;">
                <td colspan="3" style="text-align: right;">Total Skor Diperoleh / Total Maksimal:</td>
                <td class="center"><?= $totalSkor ?> / <?= $skorMaksimal ?></td>
                <td>Nilai: <?= $nilaiPersen ?>%</td>
            </tr>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="3" style="text-align: right;">Predikat Ketercapaian:</td>
                <td colspan="2" class="center"><?= $predikat ?></td>
            </tr>
        </tfoot>
    </table>

    <?php if (!empty($hasil['catatan_umum'])): ?>
    <div style="margin-top: 10px; border: 1px solid #333; padding: 6px 10px;">
        <strong>Catatan Rekomendasi Umum Supervisor:</strong>
        <p style="margin: 3px 0 0 0;"><?= nl2br(esc($hasil['catatan_umum'])) ?></p>
    </div>
    <?php endif; ?>

    <?php
    helper('signature');
    $isPolos = !empty($is_polos);
    $signatures = $signatures ?? [];
    ?>
    <table class="signature-table" style="width: 100%; border: 0px none transparent !important; margin-top: 25px;">
        <tr>
            <td style="width: 48%; vertical-align: top; border: 0px none transparent !important;">
                <?= render_signature_block_dompdf(
                    'Guru Mata Pelajaran',
                    $schedule['nama_guru'] ?? '',
                    $schedule['nip_guru'] ?? '',
                    $signatures['guru'] ?? null,
                    $isPolos
                ) ?>
            </td>
            <td style="width: 4%; border: 0px none transparent !important;"></td>
            <td style="width: 48%; vertical-align: top; border: 0px none transparent !important;">
                <?= render_signature_block_dompdf(
                    'Supervisor / Penilai',
                    $schedule['nama_supervisor'] ?? 'Supervisor',
                    $schedule['nip_supervisor'] ?? '',
                    $signatures['supervisor'] ?? null,
                    $isPolos,
                    'Ditetapkan di Tempat Supervisi'
                ) ?>
            </td>
        </tr>
    </table>

</body>
</html>
