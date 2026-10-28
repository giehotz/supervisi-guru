<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara & Lembar Percakapan Coaching TIRTA - <?= esc($schedule['nama_guru']) ?></title>
    <style>
        @page {
            margin: 1.5cm 1.5cm 1.5cm 1.5cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #111;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
        }
        .header h3 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h4 {
            margin: 3px 0;
            font-size: 11pt;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 8.5pt;
            color: #333;
        }
        .doc-title {
            text-align: center;
            margin: 14px 0 10px 0;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 12pt;
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
            margin-bottom: 14px;
            font-size: 9pt;
        }
        table.data th, table.data td {
            border: 1px solid #333;
            padding: 6px 8px;
            vertical-align: top;
        }
        table.data th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .tirta-badge {
            display: inline-block;
            font-weight: bold;
            color: #000;
        }
        .section-header {
            background-color: #e9ecef;
            font-weight: bold;
            padding: 5px 8px;
            border: 1px solid #333;
            border-bottom: none;
            font-size: 9.5pt;
            margin-top: 10px;
        }
        .box-catatan {
            border: 1px solid #333;
            padding: 8px 10px;
            margin-bottom: 12px;
            background-color: #fdfdfd;
            font-size: 9pt;
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
        .lead-text {
            font-size: 9.5pt;
            margin-bottom: 8px;
            text-align: justify;
        }
    </style>
</head>
<body>

    <!-- Kop Surat Resmi Satuan Pendidikan -->
    <div class="header">
        <h3>PEMERINTAH DAERAH / SATUAN PENDIDIKAN</h3>
        <h4>SUPERVISI AKADEMIK DENGAN PENDEKATAN COACHING</h4>
        <p>Pedoman Pembinaan Guru Berkelanjutan &bull; Standar Penilaian Kinerja Guru (PKKS)</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h3>BERITA ACARA & LEMBAR PERCAKAPAN COACHING ALUR TIRTA</h3>
        <p>Tahun Ajaran <?= esc($schedule['tahun_ajar'] ?? date('Y')) ?> / <?= esc($schedule['semester'] ?? 'Ganjil') ?></p>
    </div>

    <p class="lead-text">
        Pada hari ini telah dilaksanakan sesi percakapan pembinaan akademik (<em>Academic Coaching</em>) menggunakan alur <strong>TIRTA</strong> (Tujuan, Identifikasi, Rencana Aksi, Tanggung Jawab) antara Supervisor (Coach) dengan Guru (Coachee) dengan identitas pelaksanaan sebagai berikut:
    </p>

    <!-- Identitas Pelaksanaan -->
    <table class="meta">
        <tr>
            <td style="width: 20%;"><strong>Nama Guru (Coachee)</strong></td>
            <td style="width: 40%;">: <?= esc($schedule['nama_guru']) ?></td>
            <td style="width: 18%;"><strong>Hari / Tanggal</strong></td>
            <td style="width: 22%;">: <?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
        </tr>
        <tr>
            <td><strong>NIP Guru</strong></td>
            <td>: <?= esc($schedule['nip_guru'] ?? '-') ?></td>
            <td><strong>Kelas / Fase</strong></td>
            <td>: <?= esc($schedule['nama_kelas'] ?? ($schedule['kelas'] ?? '-')) ?> (Fase <?= esc($schedule['kelas_fase'] ?? 'E/F') ?>)</td>
        </tr>
        <tr>
            <td><strong>Mata Pelajaran</strong></td>
            <td>: <?= esc($schedule['mata_pelajaran'] ?? '-') ?></td>
            <td><strong>Jam Observasi</strong></td>
            <td>: Jam Ke-<?= esc($schedule['jam_ke'] ?? '-') ?></td>
        </tr>
        <tr>
            <td><strong>Nama Supervisor (Coach)</strong></td>
            <td>: <?= esc($schedule['nama_supervisor'] ?? '-') ?></td>
            <td><strong>NIP Supervisor</strong></td>
            <td>: <?= esc($schedule['nip_supervisor'] ?? '-') ?></td>
        </tr>
    </table>

    <?php 
        // Pisahkan log coaching pra dan pasca jika ada
        $coachingPra = null;
        $coachingPasca = null;
        if (!empty($coachingList)) {
            foreach ($coachingList as $c) {
                if ($c['tahap'] === 'pra_observasi') {
                    $coachingPra = $c;
                } elseif ($c['tahap'] === 'pasca_observasi') {
                    $coachingPasca = $c;
                }
            }
            if (!$coachingPra && isset($coachingList[0])) {
                $coachingPra = $coachingList[0];
            }
        }
    ?>

    <!-- BAGIAN I: PERCAKAPAN COACHING PRA-OBSERVASI -->
    <div class="section-header">
        BAGIAN I. PERCAKAPAN COACHING PRA-OBSERVASI (PERENCANAAN PEMBELAJARAN)
    </div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 22%;">Tahapan Alur TIRTA</th>
                <th style="width: 78%;">Catatan Refleksi & Hasil Kesepakatan Percakapan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span class="tirta-badge">T - TUJUAN</span><br>
                    <small style="color: #555;">(Menyepakati sasaran dan fokus percakapan)</small>
                </td>
                <td>
                    <?= !empty($coachingPra['tujuan_coaching']) ? nl2br(esc($coachingPra['tujuan_coaching'])) : '<em>Menyepakati fokus target perilaku observasi kelas dan strategi pencapaian tujuan pembelajaran.</em>' ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="tirta-badge">I - IDENTIFIKASI</span><br>
                    <small style="color: #555;">(Memetakan situasi riil & tantangan murid)</small>
                </td>
                <td>
                    <?= !empty($coachingPra['identifikasi_kondisi']) ? nl2br(esc($coachingPra['identifikasi_kondisi'])) : '<em>Guru mengidentifikasi kesiapan belajar siswa dan potensi hambatan dalam pengelolaan kelas.</em>' ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="tirta-badge">R - RENCANA AKSI</span><br>
                    <small style="color: #555;">(Strategi konkret & inovasi ajar)</small>
                </td>
                <td>
                    <?= !empty($coachingPra['rencana_aksi']) ? nl2br(esc($coachingPra['rencana_aksi'])) : '<em>Penerapan pembelajaran berdiferensiasi dan media interaktif sesuai kebutuhan murid.</em>' ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="tirta-badge">TA - TANGGUNG JAWAB</span><br>
                    <small style="color: #555;">(Komitmen & dukungan yang diperlukan)</small>
                </td>
                <td>
                    <?= !empty($coachingPra['tanggung_jawab']) ? nl2br(esc($coachingPra['tanggung_jawab'])) : '<em>Komitmen pelaksanaan pada jadwal observasi dengan dukungan rekan sejawat dalam Kombel.</em>' ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- BAGIAN II: PERCAKAPAN COACHING PASCA-OBSERVASI (JIKA SUDAH TEREALISASI) -->
    <?php if ($coachingPasca || !empty($tindakLanjut['respon_supervisor_c'])): ?>
    <div class="section-header">
        BAGIAN II. PERCAKAPAN COACHING PASCA-OBSERVASI & REFLEKSI TINDAK LANJUT
    </div>
    <table class="data">
        <thead>
            <tr>
                <th style="width: 22%;">Tahapan Alur TIRTA</th>
                <th style="width: 78%;">Catatan Refleksi & Hasil Kesepakatan Percakapan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span class="tirta-badge">T - TUJUAN</span><br>
                    <small style="color: #555;">(Evaluasi diri & umpan balik)</small>
                </td>
                <td>
                    <?= !empty($coachingPasca['tujuan_coaching']) ? nl2br(esc($coachingPasca['tujuan_coaching'])) : '<em>Mengevaluasi keterlaksanaan pembelajaran berdasarkan hasil observasi dan umpan balik supervisor.</em>' ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="tirta-badge">I - IDENTIFIKASI</span><br>
                    <small style="color: #555;">(Kekuatan & area peningkatan)</small>
                </td>
                <td>
                    <?= !empty($coachingPasca['identifikasi_kondisi']) ? nl2br(esc($coachingPasca['identifikasi_kondisi'])) : (!empty($tindakLanjut['catatan_supervisor_c']) ? nl2br(esc($tindakLanjut['catatan_supervisor_c'])) : '<em>Mengidentifikasi aspek yang sudah berjalan optimal dan praktik pembelajaran yang memerlukan perbaikan.</em>') ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="tirta-badge">R - RENCANA AKSI</span><br>
                    <small style="color: #555;">(Aktivitas tindak lanjut di PMM/Kombel)</small>
                </td>
                <td>
                    <?= !empty($coachingPasca['rencana_aksi']) ? nl2br(esc($coachingPasca['rencana_aksi'])) : (!empty($tindakLanjut['tindakan_perbaikan']) ? nl2br(esc($tindakLanjut['tindakan_perbaikan'])) : '<em>Mengikuti pelatihan mandiri di PMM dan berbagi praktik baik di Komunitas Belajar (Kombel).</em>') ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="tirta-badge">TA - TANGGUNG JAWAB</span><br>
                    <small style="color: #555;">(Jadwal realisasi & dukungan)</small>
                </td>
                <td>
                    <?= !empty($coachingPasca['tanggung_jawab']) ? nl2br(esc($coachingPasca['tanggung_jawab'])) : (!empty($tindakLanjut['kebutuhan_dukungan']) ? 'Dukungan: ' . esc($tindakLanjut['kebutuhan_dukungan']) : '<em>Realisasi rencana tindak lanjut dipantau berkala oleh Kepala Satuan Pendidikan.</em>') ?>
                </td>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Catatan Komitmen Bersama -->
    <div class="box-catatan">
        <strong>Pernyataan Komitmen Bersama:</strong><br>
        Percakapan coaching ini dilaksanakan secara kolaboratif, memberdayakan, dan berpusat pada pengembangan potensi guru untuk mewujudkan kepemimpinan murid (<em>student agency</em>). Kedua belah pihak menyepakati rencana tindak lanjut yang telah dirumuskan untuk diimplementasikan secara konsisten.
    </div>

    <!-- Tanda Tangan Ganda -->
    <?php
    helper('signature');
    $isPolos = !empty($is_polos);
    $signatures = $signatures ?? [];
    ?>
    <table class="signature-table" style="width: 100%; border: 0px none transparent !important; margin-top: 20px;">
        <tr>
            <td style="width: 48%; vertical-align: top; border: 0px none transparent !important;">
                <?= render_signature_block_dompdf(
                    'Guru yang Disupervisi (Coachee)',
                    $schedule['nama_guru'] ?? '',
                    $schedule['nip_guru'] ?? '',
                    $signatures['guru'] ?? null,
                    $isPolos
                ) ?>
            </td>
            <td style="width: 4%; border: 0px none transparent !important;"></td>
            <td style="width: 48%; vertical-align: top; border: 0px none transparent !important;">
                <?= render_signature_block_dompdf(
                    'Supervisor / Penilai (Coach)',
                    $schedule['nama_supervisor'] ?? 'Supervisor',
                    $schedule['nip_supervisor'] ?? '',
                    $signatures['supervisor'] ?? null,
                    $isPolos,
                    date('d F Y', strtotime($schedule['tanggal_supervisi']))
                ) ?>
            </td>
        </tr>
    </table>

</body>
</html>
