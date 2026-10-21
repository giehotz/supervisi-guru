<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-2 hasil-supervisi-page">

    <!-- Top Navigation & Action Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small">
                    <li class="breadcrumb-item"><a href="<?= base_url('guru') ?>" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('guru/hasil') ?>" class="text-decoration-none">Hasil Supervisi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail Laporan</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <span class="avatar bg-primary-lt text-primary rounded-2 p-2">
                    <i class="fas fa-clipboard-check fs-2"></i>
                </span>
                <div>
                    <h1 class="h2 fw-bold text-dark mb-0 tracking-tight">Detail Hasil Supervisi Akademik</h1>
                    <p class="text-muted small mb-0">Rincian lembar penilaian instrumen, catatan aspek observasi, dan rekomendasi supervisor.</p>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <?php if (!empty($schedule['indikator_pmm_id'])): ?>
                <a href="<?= base_url('guru/hasil/cetak-kurmer/' . $schedule['id']) ?>" target="_blank" class="btn btn-outline-success d-inline-flex align-items-center gap-2 shadow-sm" title="Cetak Formulir A-D PMM">
                    <i class="fas fa-file-signature"></i>
                    <span>Formulir A–D (PMM)</span>
                </a>
                <a href="<?= base_url('guru/hasil/cetak-telaah/' . $schedule['id']) ?>" target="_blank" class="btn btn-outline-info d-inline-flex align-items-center gap-2 shadow-sm" title="Cetak Telaah ATP / Modul Ajar">
                    <i class="fas fa-file-alt"></i>
                    <span>Cetak Telaah</span>
                </a>
                <a href="<?= base_url('guru/hasil/cetak-coaching/' . $schedule['id']) ?>" target="_blank" class="btn btn-outline-teal d-inline-flex align-items-center gap-2 shadow-sm" title="Cetak Berita Acara Coaching TIRTA">
                    <i class="fas fa-comments"></i>
                    <span>Berita Acara Coaching</span>
                </a>
            <?php endif; ?>
            <a href="<?= base_url('guru/hasil/cetak/' . $schedule['id']) ?>" target="_blank" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="fas fa-file-pdf"></i>
                <span>Cetak PDF</span>
            </a>
            <a href="<?= base_url('guru/hasil') ?>" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <?php
        // Hitung rata-rata nilai akhir dan predikat keseluruhan
        $totalNilaiSemua = 0;
        $countNilaiSemua = 0;
        foreach ($hasilList as $h) {
            if (!is_null($h['nilai_akhir'])) {
                $totalNilaiSemua += $h['nilai_akhir'];
                $countNilaiSemua++;
            }
        }
        $overallScore = $countNilaiSemua > 0 ? ($totalNilaiSemua / $countNilaiSemua) : 0;
        if ($overallScore >= 86) {
            $overallPred = 'Baik Sekali';
            $overallBadgeBg = 'bg-success-lt text-success';
            $overallCardBorder = 'border-success';
            $overallTextColor = 'text-success';
        } elseif ($overallScore >= 70) {
            $overallPred = 'Baik';
            $overallBadgeBg = 'bg-primary-lt text-primary';
            $overallCardBorder = 'border-primary';
            $overallTextColor = 'text-primary';
        } elseif ($overallScore >= 55) {
            $overallPred = 'Cukup';
            $overallBadgeBg = 'bg-warning-lt text-warning';
            $overallCardBorder = 'border-warning';
            $overallTextColor = 'text-warning';
        } else {
            $overallPred = 'Kurang';
            $overallBadgeBg = 'bg-danger-lt text-danger';
            $overallCardBorder = 'border-danger';
            $overallTextColor = 'text-danger';
        }
    ?>

    <!-- Banner Eksekutif: Nilai Akhir & Konteks Pelaksanaan -->
    <div class="card shadow-sm border-0 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">
                
                <!-- Nilai Utama & Predikat -->
                <div class="col-lg-4 col-md-5">
                    <div class="p-3 rounded-3 bg-light border text-center text-md-start">
                        <div class="text-uppercase text-muted fw-semibold small tracking-wide mb-1">
                            Akumulasi Nilai Akhir
                        </div>
                        <div class="d-flex align-items-baseline justify-content-center justify-content-md-start gap-2 mb-1">
                            <span class="score-display-value <?= $overallTextColor ?>">
                                <?= number_format($overallScore, 2) ?>
                            </span>
                            <span class="text-muted fw-semibold fs-4">%</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2">
                            <span class="badge <?= $overallBadgeBg ?> px-3 py-1 fs-6 fw-semibold rounded-pill">
                                <?= $overallPred ?>
                            </span>
                            <span class="badge bg-green-lt px-2 py-1 small rounded-pill">
                                <i class="fas fa-check-circle me-1"></i> Supervisi Selesai
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Identitas Guru & Supervisi -->
                <div class="col-lg-8 col-md-7">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="meta-item">
                                <span class="meta-label d-block">
                                    <i class="fas fa-user text-muted me-1"></i> Guru Pengajar
                                </span>
                                <span class="meta-value d-block">
                                    <?= esc($guru['nama'] ?? (session()->get('username') ?? '-')) ?>
                                </span>
                                <small class="text-muted tabular-nums">
                                    NIP: <?= esc($guru['nip'] ?? ($guru['nuptk'] ?? '-')) ?>
                                </small>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="meta-item">
                                <span class="meta-label d-block">
                                    <i class="fas fa-user-tie text-muted me-1"></i> Supervisor Penilai
                                </span>
                                <span class="meta-value d-block">
                                    <?= esc($schedule['supervisor_name'] ?? ($hasilList[0]['supervisor_name'] ?? '-')) ?>
                                </span>
                                <small class="text-muted">Supervisor Akademik</small>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="meta-item">
                                <span class="meta-label d-block">
                                    <i class="fas fa-book-open text-muted me-1"></i> Mata Pelajaran & Kelas
                                </span>
                                <span class="meta-value d-block">
                                    <?= esc($schedule['mata_pelajaran'] ?? '-') ?>
                                </span>
                                <span class="badge bg-azure-lt px-2 py-0 mt-1">
                                    Kelas <?= esc($schedule['nama_kelas'] ?? ($schedule['kelas'] ?? '-')) ?>
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="meta-item">
                                <span class="meta-label d-block">
                                    <i class="fas fa-calendar-day text-muted me-1"></i> Jadwal Observasi
                                </span>
                                <span class="meta-value d-block tabular-nums">
                                    <?= format_hari_indonesia($schedule['tanggal_supervisi']) ?>, <?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?>
                                </span>
                                <small class="text-muted">
                                    Jam Ke-<?= esc($schedule['jam_ke'] ?? '-') ?> &bull; TA <?= esc($schedule['tahun_ajar'] ?? '-') ?> (<?= esc($schedule['semester'] ?? '-') ?>)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Section 2: Lembar Penilaian Instrumen Aspek (Tabbed Navigation) -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <h2 class="h5 fw-bold text-dark mb-0">Rincian Lembar Penilaian Aspek</h2>
                    <p class="text-muted small mb-0">Evaluasi terperinci mencakup 4 pilar instrumen supervisi pembelajaran.</p>
                </div>
                <span class="badge bg-secondary-lt px-3 py-1 text-muted small">
                    <?= count($hasilList) ?> Komponen Instrumen
                </span>
            </div>
        </div>

        <div class="card-body p-4">
            <?php
                $komponenNames = [
                    1 => ['nama' => '1. Administrasi Perencanaan', 'icon' => 'fas fa-file-alt'],
                    2 => ['nama' => '2. Proses Pembelajaran', 'icon' => 'fas fa-chalkboard-teacher'],
                    3 => ['nama' => '3. Evaluasi Pembelajaran', 'icon' => 'fas fa-chart-bar'],
                    4 => ['nama' => '4. Pengembangan Diri', 'icon' => 'fas fa-user-graduate']
                ];
            ?>

            <!-- Tabs Segmented Button List -->
            <ul class="nav nav-pills gap-2 mb-4 p-1 bg-light rounded-3" id="komponenTabs" role="tablist">
                <?php foreach ($hasilList as $idx => $hasil): ?>
                    <?php 
                        $jId = $hasil['jenis_penilaian_id'];
                        $meta = $komponenNames[$jId] ?? ['nama' => 'Komponen ' . $jId, 'icon' => 'fas fa-check'];
                    ?>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 d-flex align-items-center justify-content-between gap-2 px-3 py-2 <?= $idx === 0 ? 'active' : '' ?>" 
                                id="tab-komponen-<?= $jId ?>-tab" 
                                data-bs-toggle="pill" 
                                data-bs-target="#tab-komponen-<?= $jId ?>" 
                                type="button" 
                                role="tab" 
                                aria-controls="tab-komponen-<?= $jId ?>" 
                                aria-selected="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <span class="text-truncate">
                                <i class="<?= $meta['icon'] ?> me-2"></i>
                                <?= $meta['nama'] ?>
                            </span>
                            <?php if (!is_null($hasil['nilai_akhir'])): ?>
                                <span class="badge bg-white text-dark shadow-sm tabular-nums fw-bold">
                                    <?= number_format($hasil['nilai_akhir'], 1) ?>%
                                </span>
                            <?php endif; ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Tab Content Panes -->
            <div class="tab-content" id="komponenTabsContent">
                <?php foreach ($hasilList as $idx => $hasil): ?>
                    <?php 
                        $jId = $hasil['jenis_penilaian_id'];
                        $meta = $komponenNames[$jId] ?? ['nama' => 'Komponen ' . $jId, 'icon' => 'fas fa-check'];
                        $aspekList = $detailResults[$jId] ?? [];
                    ?>
                    <div class="tab-pane fade <?= $idx === 0 ? 'show active' : '' ?>" 
                         id="tab-komponen-<?= $jId ?>" 
                         role="tabpanel" 
                         aria-labelledby="tab-komponen-<?= $jId ?>-tab">

                        <!-- Komponen Header Summary Box -->
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center p-3 mb-3 bg-light rounded-3 border gap-3">
                            <div>
                                <h3 class="h6 fw-bold text-dark mb-1">
                                    <i class="<?= $meta['icon'] ?> text-primary me-2"></i><?= $meta['nama'] ?>
                                </h3>
                                <p class="text-muted small mb-0">Total <strong><?= count($aspekList) ?> butir aspek</strong> diamati dan diverifikasi pada komponen ini.</p>
                            </div>
                            <div>
                                <?php if (!is_null($hasil['nilai_akhir'])): ?>
                                    <div class="text-sm-end">
                                        <span class="badge bg-primary-lt text-primary px-3 py-2 fs-6 fw-semibold rounded-pill">
                                            Capaian: <span class="tabular-nums"><?= number_format($hasil['nilai_akhir'], 2) ?>%</span> (<?= esc($hasil['ketercapaian'] ?? '-') ?>)
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-secondary-lt text-muted px-3 py-2 rounded-pill">Belum Dinilai</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Table Aspek Penilaian -->
                        <div class="table-responsive rounded-3 border mb-4">
                            <table class="table table-hover table-rubrik mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th style="width: 44%;">Deskripsi Aspek Observasi</th>
                                        <th class="text-center" style="width: 20%;">Skor & Predikat</th>
                                        <th style="width: 32%;">Catatan Temuan / Bukti Fisik</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($aspekList)): ?>
                                        <?php $no = 1; foreach ($aspekList as $aspek): ?>
                                            <?php
                                                $skor = $aspek['skor'] ?? 0;
                                                if ($skor == 4) { 
                                                    $badgeClass = 'bg-success text-white'; 
                                                    $skorText = '4 &bull; Sangat Baik'; 
                                                } elseif ($skor == 3) { 
                                                    $badgeClass = 'bg-primary text-white'; 
                                                    $skorText = '3 &bull; Baik'; 
                                                } elseif ($skor == 2) { 
                                                    $badgeClass = 'bg-warning text-dark'; 
                                                    $skorText = '2 &bull; Cukup'; 
                                                } elseif ($skor == 1) { 
                                                    $badgeClass = 'bg-danger text-white'; 
                                                    $skorText = '1 &bull; Kurang'; 
                                                } else { 
                                                    $badgeClass = 'bg-secondary text-white'; 
                                                    $skorText = '0 &bull; Belum'; 
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-center text-muted fw-bold tabular-nums"><?= $no++ ?></td>
                                                <td>
                                                    <span class="fw-medium text-dark d-block">
                                                        <?= esc($aspek['nama_aspek']) ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge <?= $badgeClass ?> px-3 py-1 rounded-pill tabular-nums fw-semibold">
                                                        <?= $skorText ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($aspek['catatan'])): ?>
                                                        <div class="text-secondary small measure-prose">
                                                            <i class="fas fa-quote-left text-muted me-1 fs-6"></i>
                                                            <?= nl2br(esc($aspek['catatan'])) ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted small fst-italic">Tidak ada catatan khusus.</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fs-2 d-block mb-2 text-muted"></i>
                                                Belum ada data butir aspek untuk komponen ini.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Rekomendasi & Catatan Supervisor -->
                        <div class="rekomendasi-card p-3 p-md-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="avatar bg-primary-lt text-primary rounded-circle" style="width: 32px; height: 32px;">
                                    <i class="fas fa-comment-dots"></i>
                                </span>
                                <h4 class="h6 fw-bold text-dark mb-0">Rekomendasi & Arahan Pembinaan Supervisor:</h4>
                            </div>
                            <div class="ps-md-4 ms-md-2">
                                <?php if (!empty($hasil['rekomendasi'])): ?>
                                    <div class="measure-prose text-dark lh-base">
                                        <?= nl2br(esc($hasil['rekomendasi'])) ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted small fst-italic mb-0">
                                        Belum ada catatan rekomendasi spesifik yang disematkan untuk komponen ini.
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Section 3: Rekapitulasi & Formula Perhitungan Nilai -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3">
            <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-calculator text-primary"></i>
                Rekapitulasi Capaian & Formula Penilaian
            </h2>
        </div>
        <div class="card-body p-4">
            <?php
                $komponen_rekap = [];
                $total_skor_all = 0;
                $total_maks_all = 0;

                foreach ($hasilList as $hasil) {
                    $jId = $hasil['jenis_penilaian_id'];
                    $nama = $komponenNames[$jId]['nama'] ?? 'Komponen ' . $jId;
                    
                    $skor_komp = 0;
                    $jml_aspek = 0;
                    if (isset($detailResults[$jId])) {
                        foreach ($detailResults[$jId] as $d) {
                            $skor_komp += (int)($d['skor'] ?? 0);
                            $jml_aspek++;
                        }
                    }
                    $maks_komp = $jml_aspek * 4;
                    $persen_komp = $maks_komp > 0 ? ($skor_komp / $maks_komp) * 100 : 0;

                    $komponen_rekap[] = [
                        'nama' => $nama,
                        'skor' => $skor_komp,
                        'maksimal' => $maks_komp,
                        'persentase' => $persen_komp
                    ];

                    $total_skor_all += $skor_komp;
                    $total_maks_all += $maks_komp;
                }

                $nilai_akhir_hitung = $total_maks_all > 0 ? ($total_skor_all / $total_maks_all) * 100 : 0;
            ?>

            <div class="table-responsive rounded-3 border mb-4">
                <table class="table table-hover table-rubrik text-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-start ps-3">Komponen Penilaian</th>
                            <th style="width: 22%;">Skor Diperoleh</th>
                            <th style="width: 22%;">Skor Maksimal</th>
                            <th style="width: 24%;">Persentase Capaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($komponen_rekap as $k): ?>
                            <tr>
                                <td class="text-start ps-3 fw-semibold text-dark"><?= esc($k['nama']) ?></td>
                                <td class="tabular-nums"><?= $k['skor'] ?></td>
                                <td class="tabular-nums text-muted"><?= $k['maksimal'] ?></td>
                                <td>
                                    <span class="badge bg-azure-lt px-3 py-1 rounded-pill tabular-nums fw-bold">
                                        <?= number_format($k['persentase'], 2) ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-light fw-bold">
                            <td class="text-start ps-3 text-uppercase tracking-wide text-dark">Total Akumulasi Skor</td>
                            <td class="tabular-nums fs-6 text-primary"><?= $total_skor_all ?></td>
                            <td class="tabular-nums fs-6 text-muted"><?= $total_maks_all ?></td>
                            <td class="tabular-nums fs-5 text-primary">
                                <?= number_format($nilai_akhir_hitung, 2) ?>%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row g-4 align-items-stretch">
                <!-- Rumus Perhitungan -->
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="meta-label mb-2 text-dark">
                            <i class="fas fa-square-root-alt text-primary me-1"></i> Rumus Kalkulasi Penilaian
                        </div>
                        <div class="formula-box p-3 bg-white rounded border">
                            <div class="text-muted small mb-1">Formula Perhitungan Indeks:</div>
                            <div class="fw-bold text-dark mb-2">
                                Nilai Akhir = (Total Skor Diperoleh / Total Skor Maksimal) &times; 100
                            </div>
                            <div class="text-muted small">Substitusi Nilai:</div>
                            <div class="tabular-nums text-dark">
                                = (<?= $total_skor_all ?> / <?= $total_maks_all ?>) &times; 100
                            </div>
                            <div class="d-flex align-items-baseline gap-2 mt-2 pt-2 border-top">
                                <span class="text-muted small">Indeks Capaian:</span>
                                <span class="fs-4 fw-bold text-primary tabular-nums">
                                    <?= number_format($nilai_akhir_hitung, 2) ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Standar Kualifikasi Nilai -->
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="meta-label mb-2 text-dark">
                            <i class="fas fa-bookmark text-primary me-1"></i> Kualifikasi Standar Predikat
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success text-white px-2 py-1 rounded-pill tabular-nums">86.00 – 100%</span>
                                    <span class="fw-semibold text-dark">Baik Sekali</span>
                                </div>
                                <span class="text-muted small">Sangat Memuaskan (A)</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary text-white px-2 py-1 rounded-pill tabular-nums">70.00 – 85.99%</span>
                                    <span class="fw-semibold text-dark">Baik</span>
                                </div>
                                <span class="text-muted small">Sesuai Standar (B)</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill tabular-nums">55.00 – 69.99%</span>
                                    <span class="fw-semibold text-dark">Cukup</span>
                                </div>
                                <span class="text-muted small">Perlu Pembinaan (C)</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 bg-white rounded border">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger text-white px-2 py-1 rounded-pill tabular-nums">&lt; 55.00%</span>
                                    <span class="fw-semibold text-dark">Kurang</span>
                                </div>
                                <span class="text-muted small">Bimbingan Khusus (D)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Section 4: Foto Bukti Supervisi (Jika Ada) -->
    <?php if (!empty($fotoBukti)): ?>
        <div class="card shadow-sm border-0 mb-4 bg-white">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="h5 fw-bold text-dark mb-0">Dokumentasi Foto Bukti Supervisi</h2>
                    <p class="text-muted small mb-0">Arsip foto kegiatan pelaksanaan supervisi akademik di kelas.</p>
                </div>
                <span class="badge bg-azure-lt px-3 py-1 rounded-pill">
                    <?= count($fotoBukti) ?> Foto Tersimpan
                </span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <?php foreach ($fotoBukti as $foto): ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="card h-100 border shadow-none bg-light overflow-hidden">
                                <img src="<?= base_url($foto['file_path']) ?>" 
                                     class="card-img-top img-thumbnail-preview" 
                                     alt="Foto Dokumentasi Supervisi" 
                                     style="height: 180px; object-fit: cover; cursor: pointer;"
                                     data-src="<?= base_url($foto['file_path']) ?>"
                                     data-keterangan="<?= esc($foto['keterangan'] ?? 'Tanpa keterangan') ?>">
                                <div class="card-body p-2 d-flex flex-column justify-content-between">
                                    <p class="small text-muted mb-2 text-truncate" title="<?= esc($foto['keterangan'] ?? '-') ?>">
                                        <?= esc($foto['keterangan'] ?: 'Dokumentasi supervisi') ?>
                                    </p>
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-modal-preview"
                                            data-src="<?= base_url($foto['file_path']) ?>"
                                            data-keterangan="<?= esc($foto['keterangan'] ?? '') ?>">
                                        <i class="fas fa-search-plus me-1"></i> Perbesar Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- Modal Preview Gambar (Bootstrap 5) -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="imagePreviewModalLabel">
                    <i class="fas fa-image text-primary me-2"></i>Dokumentasi Foto Bukti
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="modalImgPreview" 
                     src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1' height='1'%3E%3C/svg%3E" 
                     class="img-fluid rounded border shadow-sm" 
                     alt="Preview Dokumentasi Supervisi"
                     style="max-height: 72vh; object-fit: contain;">
                <p id="modalImgDesc" class="mt-3 text-dark fw-medium mb-0"></p>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Impeccable Typography System */
.hasil-supervisi-page {
    font-family: var(--tblr-font-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif);
    color: #1e293b;
}
.hasil-supervisi-page .tabular-nums {
    font-variant-numeric: tabular-nums;
}
.hasil-supervisi-page .meta-label {
    font-size: 0.725rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 0.2rem;
}
.hasil-supervisi-page .meta-value {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.4;
}
.hasil-supervisi-page .score-display-value {
    font-size: 2.75rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.03em;
    font-variant-numeric: tabular-nums;
}
.hasil-supervisi-page .measure-prose {
    max-width: 72ch;
    line-height: 1.6;
}
.hasil-supervisi-page .table-rubrik thead th {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #475569;
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
    background-color: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.hasil-supervisi-page .table-rubrik td {
    padding: 0.875rem 0.75rem;
    vertical-align: middle;
}
.hasil-supervisi-page .rekomendasi-card {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
}
.hasil-supervisi-page .formula-box {
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    font-family: var(--tblr-font-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace);
    font-size: 0.85rem;
    line-height: 1.55;
}
.hasil-supervisi-page .nav-pills .nav-link {
    color: #475569;
    font-weight: 500;
    border-radius: 0.5rem;
    transition: all 0.15s ease-in-out;
}
.hasil-supervisi-page .nav-pills .nav-link.active {
    background-color: #206bc4;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
.hasil-supervisi-page .nav-pills .nav-link.active .badge {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Lightbox modal preview foto dokumentasi via Bootstrap 5 API
    var modalEl = document.getElementById('imagePreviewModal');
    var modalInstance = null;
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        modalInstance = new bootstrap.Modal(modalEl);
    }
    
    var imgPreview = document.getElementById('modalImgPreview');
    var imgDesc = document.getElementById('modalImgDesc');

    document.querySelectorAll('.btn-modal-preview, .img-thumbnail-preview').forEach(function(trigger) {
        trigger.addEventListener('click', function() {
            var src = this.getAttribute('data-src');
            var ket = this.getAttribute('data-keterangan') || 'Dokumentasi kegiatan supervisi';
            if (imgPreview) imgPreview.setAttribute('src', src);
            if (imgDesc) imgDesc.textContent = ket;
            if (modalInstance) {
                modalInstance.show();
            } else if (typeof $ !== 'undefined') {
                $('#imagePreviewModal').modal('show');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>