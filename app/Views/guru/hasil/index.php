<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-2 guru-hasil-page">

    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small">
                    <li class="breadcrumb-item"><a href="<?= base_url('guru') ?>" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Hasil Supervisi</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <span class="avatar bg-primary-lt text-primary rounded-2 p-2">
                    <i class="fas fa-award fs-2"></i>
                </span>
                <div>
                    <h1 class="h2 fw-bold text-dark mb-0 tracking-tight">Rekapitulasi Hasil Supervisi Akademik</h1>
                    <p class="text-muted small mb-0">Riwayat capaian penilaian, predikat mutu pembelajaran, dan berkas evaluasi resmi.</p>
                </div>
            </div>
        </div>

        <div>
            <?php if (!empty($activeTahunAjar)): ?>
                <span class="badge bg-white text-dark border px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2">
                    <i class="fas fa-calendar-check text-primary"></i>
                    <span>Tahun Ajaran Aktif: <strong class="text-primary"><?= esc($activeTahunAjar['tahun_ajar']) ?> (<?= esc($activeTahunAjar['semester']) ?>)</strong></span>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <?php
        $rataRata = $stats['rata_rata_nilai'] ?? 0;
        $predikat = $stats['predikat_umum'] ?? '-';
        $dinilai = $stats['supervisi_dinilai'] ?? 0;
        $totalSupervisi = $stats['total_supervisi'] ?? 0;

        if ($rataRata >= 86) {
            $predColor = 'success';
            $predBadgeClass = 'bg-success text-white';
            $scoreTextClass = 'text-success';
            $predDesc = 'Sangat Memuaskan &bull; Memenuhi seluruh standar mutu';
        } elseif ($rataRata >= 70) {
            $predColor = 'primary';
            $predBadgeClass = 'bg-primary text-white';
            $scoreTextClass = 'text-primary';
            $predDesc = 'Baik &bull; Telah memenuhi standar kompetensi observasi';
        } elseif ($rataRata >= 55) {
            $predColor = 'warning';
            $predBadgeClass = 'bg-warning text-dark';
            $scoreTextClass = 'text-warning';
            $predDesc = 'Cukup &bull; Perlu peningkatan pada beberapa aspek fokus';
        } else {
            $predColor = 'danger';
            $predBadgeClass = $dinilai > 0 ? 'bg-danger text-white' : 'bg-secondary text-white';
            $scoreTextClass = $dinilai > 0 ? 'text-danger' : 'text-muted';
            $predDesc = $dinilai > 0 ? 'Kurang &bull; Membutuhkan bimbingan intensif' : 'Belum ada penilaian yang diverifikasi';
        }
    ?>

    <!-- Bolder & Asymmetric Performance Showcase (7 : 5 Grid) -->
    <div class="row g-3 mb-4">
        <!-- Hero Card: Nilai Rata-rata & Kualifikasi Utama -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100 bg-white overflow-hidden">
                <div class="card-body p-4">
                    <div class="row align-items-center g-3">
                        <div class="col-sm-5 text-center text-sm-start border-sm-end pe-sm-4">
                            <span class="meta-label d-block mb-1 text-muted">
                                Rata-rata Capaian Mutu
                            </span>
                            <div class="d-flex align-items-baseline justify-content-center justify-content-sm-start gap-1 my-1">
                                <span class="score-display-value <?= $scoreTextClass ?>">
                                    <?= $dinilai > 0 ? number_format($rataRata, 2) : '0.00' ?>
                                </span>
                                <span class="text-muted fs-4 fw-bold">%</span>
                            </div>
                            <span class="badge <?= $predBadgeClass ?> px-3 py-1 fs-6 fw-semibold rounded-pill">
                                <?= esc($predikat) ?>
                            </span>
                        </div>
                        <div class="col-sm-7 ps-sm-3">
                            <h3 class="h6 fw-bold text-dark mb-1">Status Kualifikasi Akademik</h3>
                            <p class="text-muted small mb-3">
                                <?= $predDesc ?>
                            </p>
                            <div class="p-2 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                                <span class="text-muted small">Supervisi Terverifikasi:</span>
                                <span class="badge bg-azure-lt px-2 py-1 tabular-nums fw-bold">
                                    <?= $dinilai ?> dari <?= $totalSupervisi ?> Observasi
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Secondary Metrics: Total Selesai & Periode -->
        <div class="col-lg-5">
            <div class="row g-3 h-100">
                <div class="col-sm-6 col-lg-12">
                    <div class="card shadow-sm border-0 bg-white h-100">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="meta-label d-block text-muted mb-1">Total Pelaksanaan</span>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="h2 fw-bold text-dark mb-0 tabular-nums">
                                        <?= $totalSupervisi ?>
                                    </span>
                                    <span class="text-muted small">Supervisi Selesai</span>
                                </div>
                            </div>
                            <span class="avatar bg-blue-lt text-blue rounded-3 p-2" style="width: 44px; height: 44px;">
                                <i class="fas fa-clipboard-check fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-12">
                    <div class="card shadow-sm border-0 bg-white h-100">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div>
                                <span class="meta-label d-block text-muted mb-1">Periode Evaluasi</span>
                                <div class="fw-bold text-dark mb-0">
                                    <?= !empty($activeTahunAjar) ? esc($activeTahunAjar['tahun_ajar']) : 'Semua Periode' ?>
                                </div>
                                <small class="text-muted"><?= !empty($activeTahunAjar) ? esc($activeTahunAjar['semester']) : 'Arsip Riwayat' ?></small>
                            </div>
                            <span class="avatar bg-teal-lt text-teal rounded-3 p-2" style="width: 44px; height: 44px;">
                                <i class="fas fa-calendar-alt fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card: Riwayat Hasil Supervisi -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <div>
                <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-list-alt text-primary"></i>
                    Daftar Riwayat Penilaian Supervisi
                </h2>
                <p class="text-muted small mb-0">Lembar hasil supervisi yang telah selesai dinilai dan divalidasi oleh supervisor.</p>
            </div>
            <span class="badge bg-secondary-lt px-3 py-1 rounded-pill tabular-nums">
                <?= count($hasil) ?> Catatan
            </span>
        </div>

        <div class="card-body p-0">
            <?php if (empty($hasil)): ?>
                <div class="p-5 text-center text-muted">
                    <div class="avatar bg-light text-muted rounded-circle mb-3 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-clipboard-list fs-1 text-muted"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Hasil Supervisi Tersedia</h5>
                    <p class="text-muted small mb-0 measure-prose mx-auto">
                        Hasil evaluasi supervisi yang telah diverifikasi dan diselesaikan oleh supervisor akademik akan secara otomatis ditampilkan pada tabel ini.
                    </p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter table-hasil mb-0">
                        <thead>
                            <tr>
                                <th class="text-center ps-3" style="width: 50px;">No</th>
                                <th style="width: 22%;">Jadwal Pelaksanaan</th>
                                <th style="width: 26%;">Mata Pelajaran & Kelas</th>
                                <th style="width: 20%;">Supervisor</th>
                                <th class="text-center" style="width: 14%;">Nilai Akhir</th>
                                <th class="text-center" style="width: 12%;">Kualifikasi</th>
                                <th class="text-end pe-3" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($hasil as $item): ?>
                                <?php
                                    $score = $item['nilai_akhir'];
                                    $ketercapaian = $item['ketercapaian'] ?? '-';
                                    if ($score >= 86) {
                                        $badgeBg = 'bg-success text-white';
                                        $scorePill = 'bg-success-lt text-success';
                                    } elseif ($score >= 70) {
                                        $badgeBg = 'bg-primary text-white';
                                        $scorePill = 'bg-primary-lt text-primary';
                                    } elseif ($score >= 55) {
                                        $badgeBg = 'bg-warning text-dark';
                                        $scorePill = 'bg-warning-lt text-warning';
                                    } else {
                                        $badgeBg = !is_null($score) ? 'bg-danger text-white' : 'bg-secondary text-white';
                                        $scorePill = !is_null($score) ? 'bg-danger-lt text-danger' : 'bg-secondary-lt text-muted';
                                    }
                                ?>
                                <tr>
                                    <td class="text-center ps-3 text-muted fw-bold tabular-nums">
                                        <?= $no++ ?>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block tabular-nums">
                                            <?= format_hari_indonesia($item['tanggal_supervisi']) ?>, <?= date('d M Y', strtotime($item['tanggal_supervisi'])) ?>
                                        </span>
                                        <small class="text-muted d-inline-flex align-items-center gap-1">
                                            <i class="far fa-clock"></i> Jam Ke-<?= esc($item['jam_ke'] ?? '-') ?> &bull; <?= esc($item['tahun_ajaran'] ?? '-') ?> (<?= esc($item['semester'] ?? '-') ?>)
                                        </small>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark d-block">
                                            <?= esc($item['mata_pelajaran'] ?? '-') ?>
                                        </span>
                                        <span class="badge bg-azure-lt px-2 py-0 mt-1 small">
                                            <i class="fas fa-chalkboard me-1"></i> Kelas <?= esc($item['nama_kelas'] ?? ($item['kelas'] ?? '-')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="avatar bg-light text-muted rounded-circle" style="width: 28px; height: 28px;">
                                                <i class="fas fa-user-tie"></i>
                                            </span>
                                            <span class="text-dark small fw-medium">
                                                <?= esc($item['supervisor_name'] ?? '-') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!is_null($score)): ?>
                                            <span class="badge <?= $scorePill ?> px-3 py-1 rounded-pill tabular-nums fw-bold fs-6">
                                                <?= number_format($score, 2) ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-lt text-muted px-2 py-1 rounded-pill small">Belum Dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!is_null($item['ketercapaian'])): ?>
                                            <span class="badge <?= $badgeBg ?> px-3 py-1 rounded-pill small fw-semibold">
                                                <?= esc($ketercapaian) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('guru/hasil/' . $item['id']) ?>" 
                                               class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm" 
                                               title="Buka Lembar Rincian Nilai">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-sm-inline">Detail</span>
                                            </a>
                                            <a href="<?= base_url('guru/hasil/cetak/' . $item['id']) ?>" 
                                               target="_blank" 
                                               class="btn btn-outline-danger shadow-sm" 
                                               title="Cetak Berkas PDF Resmi">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rubrik Standar Predikat Mutu Akademik -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-info-circle text-primary"></i>
                <span class="meta-label text-dark mb-0">Standar Kualifikasi Nilai Supervisi Akademik</span>
            </div>
            <div class="row g-2 small">
                <div class="col-md-3 col-sm-6">
                    <div class="p-2 rounded bg-light border d-flex align-items-center justify-content-between">
                        <span class="badge bg-success text-white px-2 py-1 rounded-pill tabular-nums">86.00 – 100%</span>
                        <span class="fw-semibold text-dark">Baik Sekali (A)</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-2 rounded bg-light border d-flex align-items-center justify-content-between">
                        <span class="badge bg-primary text-white px-2 py-1 rounded-pill tabular-nums">70.00 – 85.99%</span>
                        <span class="fw-semibold text-dark">Baik (B)</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-2 rounded bg-light border d-flex align-items-center justify-content-between">
                        <span class="badge bg-warning text-dark px-2 py-1 rounded-pill tabular-nums">55.00 – 69.99%</span>
                        <span class="fw-semibold text-dark">Cukup (C)</span>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-2 rounded bg-light border d-flex align-items-center justify-content-between">
                        <span class="badge bg-danger text-white px-2 py-1 rounded-pill tabular-nums">&lt; 55.00%</span>
                        <span class="fw-semibold text-dark">Kurang (D)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
/* Impeccable Refined Styles for Hasil Supervisi */
.guru-hasil-page {
    font-family: var(--tblr-font-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif);
    color: #1e293b;
}
.guru-hasil-page .tabular-nums {
    font-variant-numeric: tabular-nums;
}
.guru-hasil-page .tracking-tight {
    letter-spacing: -0.025em;
}
.guru-hasil-page .meta-label {
    font-size: 0.725rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.guru-hasil-page .score-display-value {
    font-size: 2.75rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.03em;
    font-variant-numeric: tabular-nums;
}
.guru-hasil-page .measure-prose {
    max-width: 65ch;
    line-height: 1.6;
}
.guru-hasil-page .table-hasil thead th {
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
.guru-hasil-page .table-hasil td {
    padding: 0.875rem 0.75rem;
    vertical-align: middle;
}
@media (min-width: 576px) {
    .border-sm-end {
        border-right: 1px solid #e2e8f0 !important;
    }
}
</style>
<?= $this->endSection() ?>