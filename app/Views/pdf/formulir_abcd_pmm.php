<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Paket Formulir A-D Observasi Kinerja Guru (Perdirjen GTK No. 7607/2023)</title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.35;
            color: #111;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
        }
        .header h3 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
        }
        .header h4 {
            margin: 4px 0;
            font-size: 12pt;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 9pt;
            color: #444;
        }
        .doc-title {
            text-align: center;
            margin: 15px 0;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 12pt;
            text-decoration: underline;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 10pt;
            font-weight: bold;
        }
        table.meta {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10pt;
            border-collapse: collapse;
        }
        table.meta td {
            padding: 3px 4px;
            vertical-align: top;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        table.data th, table.data td {
            border: 1px solid #333;
            padding: 6px 8px;
            vertical-align: top;
        }
        table.data th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
        .section-box {
            background-color: #fbfbfb;
            border: 1px solid #ccc;
            padding: 8px 10px;
            margin-bottom: 12px;
            font-size: 10pt;
        }
        .section-box strong {
            display: block;
            margin-bottom: 4px;
        }
        .signature-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .signature-table td {
            padding: 4px;
            text-align: center;
            vertical-align: top;
        }
        .badge-kualitatif {
            display: inline-block;
            padding: 2px 6px;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #555;
            background-color: #eee;
        }
    </style>
</head>
<body>

    <!-- ============================================================== -->
    <!-- FORMULIR A: LEMBAR DISKUSI PERSIAPAN OBSERVASI PRAKTIK KINERJA -->
    <!-- ============================================================== -->
    <div class="header">
        <h3>PEMERINTAH DAERAH / SATUAN PENDIDIKAN</h3>
        <h4>SUPERVISI AKADEMIK & PENGELOLAAN KINERJA GURU</h4>
        <p>Berdasarkan Peraturan Direktur Jenderal Guru dan Tenaga Kependidikan Nomor 7607/B.B1/HK.03/2023</p>
    </div>

    <div class="doc-title">
        <h3>FORMULIR A: LEMBAR DISKUSI PERSIAPAN OBSERVASI KINERJA GURU</h3>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 20%;"><strong>Nama Guru</strong></td>
            <td style="width: 45%;">: <?= esc($schedule['nama_guru']) ?></td>
            <td style="width: 15%;"><strong>Hari / Tanggal</strong></td>
            <td style="width: 20%;">: <?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
        </tr>
        <tr>
            <td><strong>NIP Guru</strong></td>
            <td>: <?= esc($schedule['nip_guru'] ?? '-') ?></td>
            <td><strong>Waktu / Jam</strong></td>
            <td>: <?= esc($schedule['jam_ke'] ?? '-') ?></td>
        </tr>
        <tr>
            <td><strong>Nama Penilai</strong></td>
            <td>: <?= esc($schedule['nama_supervisor'] ?? 'Kepala Sekolah / Supervisor') ?></td>
            <td><strong>Kelas / Fase</strong></td>
            <td>: <?= esc($schedule['kelas'] ?? '-') ?></td>
        </tr>
        <tr>
            <td><strong>Mata Pelajaran</strong></td>
            <td>: <?= esc($schedule['mata_pelajaran']) ?></td>
            <td><strong>Pendekatan</strong></td>
            <td>: Coaching Alur TIRTA</td>
        </tr>
    </table>

    <div class="section-box">
        <strong>Fokus Indikator Kinerja PMM:</strong>
        <?= esc($indikatorNama ?? 'Praktik Kinerja Pembelajaran') ?>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 45%;">Fokus Perilaku yang Disepakati</th>
                <th style="width: 50%;">Upaya Mempelajari Target Perilaku</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($fokusList)): ?>
                <?php foreach ($fokusList as $i => $f): ?>
                    <tr>
                        <td style="text-align: center;"><?= $i + 1 ?></td>
                        <td>
                            <strong><?= esc($f['fokus_perilaku']) ?></strong>
                        </td>
                        <td>
                            <?= nl2br(esc($praObservasi['upaya_mempelajari'] ?? '-')) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #888;">Belum ada fokus perilaku yang dipilih</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="section-box">
        <strong>Sasaran Tujuan Pembelajaran (TP) Sesi Observasi:</strong>
        <p style="margin: 0;"><?= nl2br(esc($praObservasi['tujuan_pembelajaran_sesi'] ?? '-')) ?></p>
    </div>

    <div class="section-box">
        <strong>Catatan Kesepakatan Pra-Observasi (Supervisor):</strong>
        <p style="margin: 0;"><?= nl2br(esc($praObservasi['catatan_supervisor_pra'] ?? 'Disepakati fokus perilaku dan jadwal observasi kelas.')) ?></p>
    </div>

    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                Mengetahui / Menyetujui,<br>
                Penilai / Supervisor<br><br><br><br>
                <strong><u><?= esc($schedule['nama_supervisor'] ?? 'Kepala Sekolah') ?></u></strong><br>
                NIP. <?= esc($schedule['nip_supervisor'] ?? '-') ?>
            </td>
            <td style="width: 50%;">
                Guru yang Diobservasi,<br><br><br><br><br>
                <strong><u><?= esc($schedule['nama_guru']) ?></u></strong><br>
                NIP. <?= esc($schedule['nip_guru'] ?? '-') ?>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- ============================================================== -->
    <!-- FORMULIR B: PELAKSANAAN OBSERVASI PRAKTIK KINERJA GURU         -->
    <!-- ============================================================== -->
    <div class="doc-title" style="margin-top: 0;">
        <h3>FORMULIR B: LEMBAR PELAKSANAAN OBSERVASI PRAKTIK KINERJA GURU</h3>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 20%;"><strong>Nama Guru</strong></td>
            <td style="width: 45%;">: <?= esc($schedule['nama_guru']) ?></td>
            <td style="width: 15%;"><strong>Hari / Tanggal</strong></td>
            <td style="width: 20%;">: <?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
        </tr>
        <tr>
            <td><strong>Nama Penilai</strong></td>
            <td>: <?= esc($schedule['nama_supervisor'] ?? '-') ?></td>
            <td><strong>Mata Pelajaran</strong></td>
            <td>: <?= esc($schedule['mata_pelajaran']) ?></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 40%;">Fokus Perilaku</th>
                <th style="width: 25%;">Keterlaksanaan</th>
                <th style="width: 30%;">Catatan Fakta Objektif</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $noB = 1;
                $skalaPmmLabel = [
                    '1' => 'Belum Dilakukan',
                    '2' => 'Dilakukan tapi Belum Efektif',
                    '3' => 'Dilakukan dan Efektif',
                    '4' => 'Dilakukan dan Efektif'
                ];
            ?>
            <?php if (!empty($fokusList)): ?>
                <?php foreach ($fokusList as $aspek): ?>
                    <?php 
                        $det = $detailsByAspek[$aspek['id']] ?? null;
                        $skor = $det['skor'] ?? '-';
                        $label = $skalaPmmLabel[$skor] ?? 'Belum Dinilai';
                    ?>
                    <tr>
                        <td style="text-align: center;"><?= $noB++ ?></td>
                        <td>
                            <strong><?= esc($aspek['fokus_perilaku'] ?? $aspek['nama_aspek']) ?></strong>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-kualitatif"><?= $label ?></span>
                        </td>
                        <td>
                            <?= nl2br(esc($det['catatan'] ?? '-')) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">Data rincian observasi tidak ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="section-box">
        <strong>Rekomendasi Supervisor / Penilai:</strong>
        <p style="margin: 0;"><?= nl2br(esc($hasilUtama['rekomendasi'] ?? 'Guru disarankan melanjutkan ke tahap refleksi dan rencana tindak lanjut (Formulir C).')) ?></p>
    </div>

    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                Penilai / Supervisor<br><br><br><br>
                <strong><u><?= esc($schedule['nama_supervisor'] ?? 'Kepala Sekolah') ?></u></strong><br>
                NIP. <?= esc($schedule['nip_supervisor'] ?? '-') ?>
            </td>
            <td style="width: 50%;">
                Guru yang Diobservasi<br><br><br><br>
                <strong><u><?= esc($schedule['nama_guru']) ?></u></strong><br>
                NIP. <?= esc($schedule['nip_guru'] ?? '-') ?>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- ============================================================== -->
    <!-- FORMULIR C: TINDAK LANJUT OBSERVASI PRAKTIK KINERJA GURU       -->
    <!-- ============================================================== -->
    <div class="doc-title" style="margin-top: 0;">
        <h3>FORMULIR C: LEMBAR TINDAK LANJUT OBSERVASI PRAKTIK KINERJA GURU</h3>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 20%;"><strong>Nama Guru</strong></td>
            <td style="width: 45%;">: <?= esc($schedule['nama_guru']) ?></td>
            <td style="width: 15%;"><strong>Periode</strong></td>
            <td style="width: 20%;">: <?= esc($tindakLanjut['periode_tindak_lanjut'] ?? 'Semester Berjalan') ?></td>
        </tr>
    </table>

    <div class="section-box">
        <strong>Refleksi Guru Pasca Observasi:</strong>
        <p style="margin: 0;"><?= nl2br(esc($tindakLanjut['refleksi_guru'] ?? '-')) ?></p>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25%;">Kategori Belajar</th>
                <th style="width: 25%;">Tujuan Tindak Lanjut</th>
                <th style="width: 25%;">Upaya Tindak Lanjut</th>
                <th style="width: 25%;">Kebutuhan Dukungan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Ruang GTK / PMM:</strong><br>
                    <?= esc($tindakLanjut['pilihan_belajar_pmm'] ?? '-') ?>
                    <hr style="margin: 4px 0; border: none; border-top: 1px dashed #ccc;">
                    <strong>Komunitas Belajar (Kombel):</strong><br>
                    <?= nl2br(esc($tindakLanjut['pilihan_belajar_lainnya'] ?? '-')) ?>
                </td>
                <td>
                    <?= nl2br(esc($tindakLanjut['tujuan_tindak_lanjut'] ?? '-')) ?>
                </td>
                <td>
                    <?= nl2br(esc($tindakLanjut['upaya_tindak_lanjut'] ?? '-')) ?>
                </td>
                <td>
                    <strong><?= ucwords(str_replace('_', ' ', $tindakLanjut['kebutuhan_dukungan'] ?? '-')) ?></strong><br>
                    <?= nl2br(esc($tindakLanjut['detail_dukungan'] ?? '')) ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section-box">
        <strong>Penilaian Respon Refleksi Guru oleh Supervisor (Rubrik Form C):</strong>
        <p style="margin: 0; font-weight: bold; color: #004085;">
            <?= ucwords(str_replace('_', ' ', $tindakLanjut['respon_supervisor_c'] ?? 'Belum Dinilai')) ?>
        </p>
        <p style="margin: 4px 0 0 0;"><?= nl2br(esc($tindakLanjut['catatan_supervisor_c'] ?? '')) ?></p>
    </div>

    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                Penilai / Supervisor<br><br><br><br>
                <strong><u><?= esc($schedule['nama_supervisor'] ?? 'Kepala Sekolah') ?></u></strong><br>
                NIP. <?= esc($schedule['nip_supervisor'] ?? '-') ?>
            </td>
            <td style="width: 50%;">
                Guru yang Diobservasi<br><br><br><br>
                <strong><u><?= esc($schedule['nama_guru']) ?></u></strong><br>
                NIP. <?= esc($schedule['nip_guru'] ?? '-') ?>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- ============================================================== -->
    <!-- FORMULIR D: REFLEKSI TINDAK LANJUT PRAKTIK KINERJA GURU        -->
    <!-- ============================================================== -->
    <div class="doc-title" style="margin-top: 0;">
        <h3>FORMULIR D: LEMBAR REFLEKSI TINDAK LANJUT PRAKTIK KINERJA GURU</h3>
    </div>

    <table class="meta">
        <tr>
            <td style="width: 20%;"><strong>Nama Guru</strong></td>
            <td style="width: 45%;">: <?= esc($schedule['nama_guru']) ?></td>
            <td style="width: 15%;"><strong>Status Siklus</strong></td>
            <td style="width: 20%;">: <span class="badge-kualitatif">Selesai Paripurna</span></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 50%;">Pertanyaan Reflektif Guru</th>
                <th style="width: 50%;">Uraian Hasil Tindak Lanjut Guru</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>1. Inspirasi baru apa yang Anda dapatkan dari upaya tindak lanjut?</strong></td>
                <td><?= nl2br(esc($tindakLanjut['inspirasi_baru'] ?? '-')) ?></td>
            </tr>
            <tr>
                <td><strong>2. Perubahan praktik nyata apa yang telah Anda terapkan di kelas?</strong></td>
                <td><?= nl2br(esc($tindakLanjut['perubahan_praktik'] ?? '-')) ?></td>
            </tr>
            <tr>
                <td><strong>3. Apa kendala / tantangan tersulit yang dihadapi saat perubahan?</strong></td>
                <td><?= nl2br(esc($tindakLanjut['kendala_tindak_lanjut'] ?? '-')) ?></td>
            </tr>
            <tr>
                <td><strong>4. Rencana perbaikan berkelanjutan Anda ke depan:</strong></td>
                <td><?= nl2br(esc($tindakLanjut['rencana_perbaikan_berkelanjutan'] ?? '-')) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="section-box">
        <strong>Penilaian Capaian Perubahan oleh Supervisor (Rubrik Form D):</strong>
        <p style="margin: 0; font-weight: bold; color: #155724;">
            <?= ucwords(str_replace('_', ' ', $tindakLanjut['penilaian_supervisor_d'] ?? 'Belum Dinilai')) ?>
        </p>
        <p style="margin: 4px 0 0 0;"><?= nl2br(esc($tindakLanjut['catatan_supervisor_d'] ?? 'Praktik baik telah diterapkan secara berkelanjutan di kelas.')) ?></p>
    </div>

    <table class="signature-table">
        <tr>
            <td style="width: 50%;">
                Mengetahui,<br>
                Kepala Sekolah / Penilai<br><br><br><br>
                <strong><u><?= esc($schedule['nama_kepala'] ?? $schedule['nama_supervisor']) ?></u></strong><br>
                NIP. <?= esc($schedule['nip_kepala'] ?? $schedule['nip_supervisor'] ?? '-') ?>
            </td>
            <td style="width: 50%;">
                Guru yang Diobservasi<br><br><br><br><br>
                <strong><u><?= esc($schedule['nama_guru']) ?></u></strong><br>
                NIP. <?= esc($schedule['nip_guru'] ?? '-') ?>
            </td>
        </tr>
    </table>

</body>
</html>
