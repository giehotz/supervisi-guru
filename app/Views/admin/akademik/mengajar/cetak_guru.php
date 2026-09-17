<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #fff;
            padding: 20px;
        }
        .header-title {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-title h4, .header-title h5 {
            margin: 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .table-jadwal th, .table-jadwal td {
            border: 1px solid #000 !important;
            vertical-align: middle !important;
            padding: 6px;
        }
        .table-jadwal th {
            background-color: #f2f2f2 !important;
            text-align: center;
            font-weight: bold;
        }
        .cell-isi {
            background-color: #f9fdf9;
        }
        .cell-kelas {
            font-weight: bold;
            font-size: 0.95rem;
        }
        .cell-mapel {
            font-size: 0.82rem;
            color: #333;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="no-print mb-4 d-flex justify-content-between align-items-center">
        <a href="javascript:window.close()" class="btn btn-secondary btn-sm">
            &larr; Kembali / Tutup
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm font-weight-bold">
            Cetak / Simpan PDF
        </button>
    </div>

    <!-- Header Dokumen -->
    <div class="header-title">
        <h4>JADWAL MENGAJAR GURU</h4>
        <h5>TAHUN PELAJARAN <?= esc($tahunAjar['tahun_ajar'] ?? '-') ?> (SEMESTER <?= strtoupper(esc($tahunAjar['semester'] ?? '-')) ?>)</h5>
    </div>

    <!-- Informasi Guru -->
    <table class="table table-borderless table-sm mb-3" style="width: 100%; font-size: 0.95rem;">
        <tr>
            <td style="width: 15%;"><strong>Nama Guru</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%; font-weight: bold;"><?= esc($guru['nama']) ?></td>
            <td style="width: 15%;"><strong>Total Beban (JTM)</strong></td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;"><strong><?= $totalJtm ?> Jam Pelajaran</strong> / Pekan</td>
        </tr>
        <tr>
            <td><strong>NIP</strong></td>
            <td>:</td>
            <td><?= !empty($guru['nip']) ? esc($guru['nip']) : '-' ?></td>
            <td><strong>Mata Pelajaran Pokok</strong></td>
            <td>:</td>
            <td><?= esc($guru['mata_pelajaran'] ?? '-') ?></td>
        </tr>
    </table>

    <!-- Tabel Matriks Jadwal -->
    <table class="table table-bordered table-jadwal text-center mb-4">
        <thead>
            <tr>
                <th style="width: 120px;">Jam Ke / Waktu</th>
                <?php 
                $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                foreach ($hariList as $h): ?>
                    <th style="width: 14%;"><?= $h ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sesiJam as $jamKe => $sesi): ?>
                <tr>
                    <td class="font-weight-bold text-left" style="background-color: #fcfcfc;">
                        <div>Jam Ke-<?= $jamKe ?></div>
                        <div class="text-muted" style="font-size: 0.8rem;"><?= $sesi['mulai'] ?> - <?= $sesi['selesai'] ?></div>
                    </td>
                    <?php foreach ($hariList as $h): 
                        $cell = $matriksGuru[$h][$jamKe] ?? null;
                    ?>
                        <?php if ($cell !== null): ?>
                            <?php if (!empty($cell['is_start'])): ?>
                                <td rowspan="<?= $cell['durasi'] ?>" class="cell-isi">
                                    <div class="cell-kelas">Kelas <?= esc($cell['item']['nama_kelas'] ?? '-') ?></div>
                                    <div class="cell-mapel"><?= esc($cell['item']['nama_mapel'] ?? '-') ?></div>
                                </td>
                            <?php endif; ?>
                        <?php else: ?>
                            <td class="text-muted" style="font-size: 0.8rem;">-</td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Bagian Tanda Tangan -->
    <div class="row mt-5" style="page-break-inside: avoid;">
        <div class="col-6 text-center">
            <p class="mb-5">Mengetahui,<br>Kepala Madrasah</p>
            <br><br>
            <p class="font-weight-bold mb-0"><u>( .................................................. )</u><br>NIP. </p>
        </div>
        <div class="col-6 text-center">
            <p class="mb-5">Guru Yang Bersangkutan,</p>
            <br><br>
            <p class="font-weight-bold mb-0"><u><?= esc($guru['nama']) ?></u><br><?= !empty($guru['nip']) ? 'NIP. ' . esc($guru['nip']) : 'NIP. -' ?></p>
        </div>
    </div>

</body>
</html>
