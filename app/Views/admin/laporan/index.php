<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading & Actions Bar -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold tracking-tight">
                <i class="fas fa-chart-line text-primary mr-2"></i>Laporan & Analitik Hasil Supervisi
            </h1>
            <p class="text-muted small mb-0">Dashboard rekapitulasi capaian akademik, evaluasi pembelajaran, dan cetak laporan resmi.</p>
        </div>
        <div class="mt-3 mt-sm-0 d-flex flex-wrap" style="gap: 8px;">
            <a href="<?= base_url('admin/laporan/cetak-rekap-detail?tahun_ajar_id=' . ($tahun_ajar_id ?? '')) ?>" target="_blank" class="btn btn-sm btn-pill btn-danger shadow-sm font-weight-bold px-3">
                <i class="fas fa-file-pdf mr-1"></i> Cetak Rekap Detail (PDF)
            </a>
            <a href="<?= base_url('admin/laporan/hasil-supervisi?tahun_ajar_id=' . ($tahun_ajar_id ?? '')) ?>" class="btn btn-sm btn-pill btn-primary shadow-sm font-weight-bold px-3">
                <i class="fas fa-clipboard-list mr-1"></i> Kelola Hasil Supervisi
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-times-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filter Tahun Ajaran Card -->
    <div class="card shadow-sm mb-4 border-0" style="border-left: 4px solid #4e73df !important;">
        <div class="card-body py-3">
            <form method="get" action="<?= base_url('admin/laporan') ?>" class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center flex-wrap mb-2 mb-md-0" style="gap: 10px;">
                    <label class="font-weight-bold text-gray-800 small mb-0">
                        <i class="fas fa-calendar-alt text-primary mr-1"></i> Periode Tahun Ajaran:
                    </label>
                    <select name="tahun_ajar_id" class="form-control form-control font-weight-bold" style="min-width: 240px;" onchange="this.form.submit()">
                        <?php foreach ($tahun_ajars as $ta): ?>
                            <option value="<?= $ta['id'] ?>" <?= ($ta['id'] == $tahun_ajar_id) ? 'selected' : '' ?>>
                                <?= esc($ta['tahun_ajar']) ?> - <?= esc($ta['semester']) ?> <?= (!empty($ta['is_active']) || ($ta['status_aktif'] ?? '') === 'Aktif') ? '(Aktif)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary font-weight-bold px-3 shadow-sm">
                        <i class="fas fa-filter mr-1"></i> Terapkan
                    </button>
                </div>
                <div>
                    <span class="badge badge-light border px-3 py-2 text-gray-800 font-weight-normal shadow-sm">
                        Tahun Ajaran Aktif: <strong class="text-primary"><?= esc($selectedTahunAjar['tahun_ajar'] ?? '-') ?> (<?= esc($selectedTahunAjar['semester'] ?? '-') ?>)</strong>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="row">
        <!-- Total Guru Disupervisi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100 py-2 border-0" style="border-left: 4px solid #4e73df !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 tracking-wider">
                                Guru Disupervisi
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-900" style="font-variant-numeric: tabular-nums;">
                                <?= $metrics['total_selesai'] ?> <span class="text-muted small font-weight-normal" style="font-size: 0.85rem;">/ <?= $metrics['total_jadwal'] ?> Jadwal</span>
                            </div>
                            <div class="small text-muted mt-2 d-flex align-items-center">
                                <span class="badge badge-pill badge-primary font-weight-bold px-2 py-0 mr-1">
                                    <?= ($metrics['total_jadwal'] > 0) ? round(($metrics['total_selesai'] / $metrics['total_jadwal']) * 100) : 0 ?>%
                                </span>
                                terlaksana dari total target
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="p-3 bg-light rounded-circle text-primary shadow-xs">
                                <i class="fas fa-user-check fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nilai Rata-Rata Supervisi -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100 py-2 border-0" style="border-left: 4px solid #1cc88a !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1 tracking-wider">
                                Rata-Rata Nilai Akhir
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-900" style="font-variant-numeric: tabular-nums;">
                                <?= number_format($metrics['rata_rata_nilai'], 2, ',', '.') ?>
                            </div>
                            <div class="small mt-2 font-weight-bold">
                                <?php
                                    $avg = $metrics['rata_rata_nilai'];
                                    if ($avg >= 86) echo '<span class="text-success"><i class="fas fa-star mr-1"></i>Predikat: Baik Sekali</span>';
                                    elseif ($avg >= 70) echo '<span class="text-primary"><i class="fas fa-thumbs-up mr-1"></i>Predikat: Baik</span>';
                                    elseif ($avg >= 55) echo '<span class="text-warning"><i class="fas fa-minus mr-1"></i>Predikat: Cukup</span>';
                                    elseif ($avg > 0) echo '<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i>Predikat: Kurang</span>';
                                    else echo '<span class="text-muted font-weight-normal">Belum Ada Penilaian</span>';
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="p-3 bg-light rounded-circle text-success shadow-xs">
                                <i class="fas fa-chart-line fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tingkat Ketercapaian Kualitas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100 py-2 border-0" style="border-left: 4px solid #36b9cc !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 tracking-wider">
                                Capaian Mutu (Baik / BS)
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <div class="h4 mb-0 mr-3 font-weight-bold text-gray-900" style="font-variant-numeric: tabular-nums;">
                                    <?= $metrics['persen_kualitas'] ?>%
                                </div>
                                <div class="progress flex-grow-1" style="height: 6px; border-radius: 999px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: <?= $metrics['persen_kualitas'] ?>%"
                                        aria-valuenow="<?= $metrics['persen_kualitas'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="small text-muted mt-2">
                                <strong><?= ($metrics['predikat_counts']['Baik Sekali'] + $metrics['predikat_counts']['Baik']) ?></strong> dari <?= $metrics['total_selesai'] ?> guru tuntas
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="p-3 bg-light rounded-circle text-info shadow-xs">
                                <i class="fas fa-award fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Guru Perlu Pembinaan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100 py-2 border-0" style="border-left: 4px solid #f6c23e !important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 tracking-wider">
                                Perlu Pembinaan
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-900" style="font-variant-numeric: tabular-nums;">
                                <?= ($metrics['predikat_counts']['Cukup'] + $metrics['predikat_counts']['Kurang']) ?> <span class="text-muted small font-weight-normal" style="font-size: 0.85rem;">Guru</span>
                            </div>
                            <div class="small text-muted mt-2">
                                <span class="badge badge-light border text-warning font-weight-bold mr-1">Cukup: <?= $metrics['predikat_counts']['Cukup'] ?></span>
                                <span class="badge badge-light border text-danger font-weight-bold">Kurang: <?= $metrics['predikat_counts']['Kurang'] ?></span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="p-3 bg-light rounded-circle text-warning shadow-xs">
                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Bar Chart: Nilai Capaian per Jenis Penilaian -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar mr-1"></i> Rata-Rata Capaian Instrumen Supervisi
                    </h6>
                    <span class="badge badge-pill badge-primary px-3 py-1 font-weight-bold small">Skala 100%</span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="chart-bar" style="position: relative; height: 260px;">
                        <canvas id="jenisPenilaianBarChart"></canvas>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <div class="row">
                            <?php foreach ($jenisPenilaians as $jp): ?>
                                <div class="col-sm-6 mb-2">
                                    <div class="p-2 bg-light rounded border d-flex justify-content-between align-items-center text-left">
                                        <span class="text-gray-800 small font-weight-bold text-left mr-2"><?= esc($jp['nama']) ?>:</span>
                                        <strong class="text-primary text-nowrap ml-auto" style="font-variant-numeric: tabular-nums;"><?= number_format($metrics['avg_per_jenis'][$jp['id']] ?? 0, 1) ?>%</strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donut Chart: Distribusi Predikat Kualifikasi -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow-sm h-100 border-0">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-1"></i> Distribusi Kualifikasi
                    </h6>
                    <span class="badge badge-light border text-gray-700 font-weight-normal px-2 py-1 small">
                        Total: <?= $metrics['total_selesai'] ?> Guru
                    </span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="chart-pie pt-2 pb-2" style="position: relative; height: 220px;">
                        <canvas id="predikatDonutChart"></canvas>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <div class="row no-gutters small">
                            <div class="col-6 mb-2">
                                <span class="badge badge-pill badge-success mr-1 px-2 py-1 font-weight-bold shadow-xs">
                                    <i class="fas fa-star mr-1"></i> Baik Sekali
                                </span>
                                <strong class="text-gray-900 ml-1"><?= $metrics['predikat_counts']['Baik Sekali'] ?></strong>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="badge badge-pill badge-primary mr-1 px-2 py-1 font-weight-bold shadow-xs">
                                    <i class="fas fa-thumbs-up mr-1"></i> Baik
                                </span>
                                <strong class="text-gray-900 ml-1"><?= $metrics['predikat_counts']['Baik'] ?></strong>
                            </div>
                            <div class="col-6">
                                <span class="badge badge-pill badge-warning text-dark mr-1 px-2 py-1 font-weight-bold shadow-xs">
                                    <i class="fas fa-minus mr-1"></i> Cukup
                                </span>
                                <strong class="text-gray-900 ml-1"><?= $metrics['predikat_counts']['Cukup'] ?></strong>
                            </div>
                            <div class="col-6">
                                <span class="badge badge-pill badge-danger mr-1 px-2 py-1 font-weight-bold shadow-xs">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Kurang
                                </span>
                                <strong class="text-gray-900 ml-1"><?= $metrics['predikat_counts']['Kurang'] ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Supervision Table Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between bg-white border-bottom">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <h6 class="m-0 font-weight-bold text-primary mr-2">
                    <i class="fas fa-table mr-1"></i> Rekapitulasi Rincian Supervisi Guru
                </h6>
                <span class="badge badge-light border text-gray-700 px-2 py-1 small">
                    Tahun Ajaran <?= esc($selectedTahunAjar['tahun_ajar'] ?? '-') ?> (<?= esc($selectedTahunAjar['semester'] ?? '-') ?>)
                </span>
            </div>
            <div>
                <a href="<?= base_url('admin/laporan/cetak-rekap-detail?tahun_ajar_id=' . ($tahun_ajar_id ?? '')) ?>" target="_blank" class="btn btn-sm btn-pill btn-outline-danger shadow-sm font-weight-bold">
                    <i class="fas fa-print mr-1"></i> Cetak Rekap Landscape
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="dataTableLaporan" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 40px;" class="text-center align-middle">No</th>
                            <th class="align-middle">Nama Guru & NIP</th>
                            <th class="align-middle">Mata Pelajaran</th>
                            <th class="text-center align-middle" style="width: 80px;">Kelas</th>
                            <th class="text-center align-middle" style="width: 100px;">Tanggal</th>
                            <?php foreach ($jenisPenilaians as $jp): ?>
                                <th class="text-center align-middle" style="min-width: 110px;"><?= esc($jp['nama']) ?></th>
                            <?php endforeach; ?>
                            <th class="text-center align-middle" style="width: 95px;">Nilai Akhir</th>
                            <th class="text-center align-middle" style="width: 115px;">Kualifikasi</th>
                            <th class="text-center align-middle" style="width: 115px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jadwals)): ?>
                            <tr>
                                <td colspan="<?= 7 + count($jenisPenilaians) ?>" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3 d-block"></i>
                                        <h6 class="font-weight-bold text-gray-700 mb-1">Belum Ada Data Supervisi</h6>
                                        <p class="small text-muted mb-0">Tidak ditemukan jadwal supervisi untuk tahun ajaran yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($jadwals as $jadwal): ?>
                            <tr>
                                <td class="text-center align-middle font-weight-bold text-gray-700"><?= $no++ ?></td>
                                <td class="align-middle">
                                    <div class="font-weight-bold text-gray-900"><?= esc($jadwal['nama_guru']) ?></div>
                                    <?php if (!empty($jadwal['nip_guru']) && $jadwal['nip_guru'] !== '-'): ?>
                                        <small class="text-muted d-block">NIP: <?= esc($jadwal['nip_guru']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-light border text-gray-800 font-weight-normal px-2 py-1">
                                        <?= esc($jadwal['mata_pelajaran'] ?? '-') ?>
                                    </span>
                                </td>
                                <td class="align-middle text-center font-weight-bold text-gray-800">
                                    <?= esc($jadwal['nama_kelas'] ?? $jadwal['kelas'] ?? '-') ?>
                                </td>
                                <td class="align-middle text-center text-nowrap">
                                    <div class="font-weight-bold text-gray-800">
                                        <?= !empty($jadwal['tanggal_supervisi']) ? date('d/m/Y', strtotime($jadwal['tanggal_supervisi'])) : '-' ?>
                                    </div>
                                    <?php if (!empty($jadwal['hari'])): ?>
                                        <small class="text-muted"><?= esc($jadwal['hari']) ?></small>
                                    <?php endif; ?>
                                </td>

                                <!-- Kolom Rincian Nilai per Jenis Penilaian -->
                                <?php foreach ($jenisPenilaians as $jp): ?>
                                    <?php 
                                        $item = $jadwal['nilai_per_jenis'][$jp['id']] ?? null;
                                    ?>
                                    <td class="text-center align-middle">
                                        <?php if ($item !== null): ?>
                                            <span class="badge badge-light border font-weight-bold px-2 py-1 text-gray-800" style="font-size: 0.82rem; font-variant-numeric: tabular-nums;">
                                                <?= number_format($item['nilai'], 1, ',', '.') ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>

                                <!-- Nilai Akhir -->
                                <td class="text-center align-middle">
                                    <?php if ($jadwal['nilai_akhir'] !== null): ?>
                                        <span class="badge badge-pill badge-primary font-weight-bold px-3 py-1 shadow-sm" style="font-size: 0.9rem; font-variant-numeric: tabular-nums;">
                                            <?= number_format($jadwal['nilai_akhir'], 2, ',', '.') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-pill badge-light border text-muted px-2 py-1 small">
                                            Belum Dinilai
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Ketercapaian / Predikat -->
                                <td class="text-center align-middle">
                                    <?php if ($jadwal['ketercapaian'] !== null): ?>
                                        <?php
                                            $ket = $jadwal['ketercapaian'];
                                            $badgeClass = 'badge-secondary';
                                            $ketIcon = 'fa-check';
                                            if ($ket == 'Baik Sekali') {
                                                $badgeClass = 'badge-success';
                                                $ketIcon = 'fa-star text-warning';
                                            } elseif ($ket == 'Baik') {
                                                $badgeClass = 'badge-info';
                                                $ketIcon = 'fa-thumbs-up';
                                            } elseif ($ket == 'Cukup') {
                                                $badgeClass = 'badge-warning text-dark';
                                                $ketIcon = 'fa-minus';
                                            } elseif ($ket == 'Kurang') {
                                                $badgeClass = 'badge-danger';
                                                $ketIcon = 'fa-exclamation-triangle';
                                            }
                                        ?>
                                        <span class="badge badge-pill <?= $badgeClass ?> font-weight-bold px-2 py-1 shadow-sm">
                                            <i class="fas <?= $ketIcon ?> mr-1"></i> <?= esc($ket) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-pill badge-light border text-muted px-2 py-1 small">
                                            <?= esc($jadwal['status']) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center align-middle">
                                    <?php if ($jadwal['status'] === 'Selesai'): ?>
                                        <div class="btn-group btn-group-sm shadow-sm" role="group">
                                            <a href="<?= base_url('admin/laporan/hasil-supervisi/detail/' . $jadwal['id']) ?>" class="btn btn-primary" title="Lihat Rekap Penilaian">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('admin/penilaian/form/' . $jadwal['id']) ?>" class="btn btn-warning text-dark font-weight-bold" title="Edit Hasil Penilaian">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('admin/laporan/hasil-supervisi/cetak-detail/' . $jadwal['id']) ?>" target="_blank" class="btn btn-danger" title="Cetak Dokumen PDF">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge badge-pill badge-light border text-muted px-2 py-1 small" title="Supervisi belum selesai dinilai">
                                            <i class="fas fa-hourglass-half mr-1 text-warning"></i> Menunggu
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- Chart.js Core Library -->
<script src="<?= base_url('assets/vendor/chart.js/Chart.min.js') ?>"></script>
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    if ($('#dataTableLaporan').length) {
        $('#dataTableLaporan').DataTable({
            "language": {
                "search": "Cari Guru / Mapel:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ guru",
                "infoEmpty": "Tidak ada data yang tersedia",
                "zeroRecords": "Tidak ditemukan data yang sesuai",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            },
            "pageLength": 25,
            "order": [[ 0, "asc" ]]
        });
    }

    // Chart.js - Bar Chart Rata-rata Jenis Penilaian
    var ctxBar = document.getElementById("jenisPenilaianBarChart");
    if (ctxBar && typeof Chart !== 'undefined') {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_jenis['labels'] ?? []) ?>,
                datasets: [{
                    label: "Rata-Rata Capaian (%)",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    borderWidth: 1,
                    data: <?= json_encode($chart_jenis['data'] ?? []) ?>,
                    maxBarThickness: 45
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                layout: {
                    padding: { left: 10, right: 15, top: 15, bottom: 5 }
                },
                scales: {
                    xAxes: [{
                        gridLines: { display: false, drawBorder: false },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 20,
                            fontSize: 10,
                            callback: function(value) {
                                if (typeof value === 'string' && value.length > 18) {
                                    return value.substring(0, 16) + '...';
                                }
                                return value;
                            }
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: 100,
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) { return value + '%'; }
                        },
                        gridLines: {
                            color: "rgba(0, 0, 0, 0.05)",
                            zeroLineColor: "rgba(0, 0, 0, 0.1)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }]
                },
                legend: { display: false },
                tooltips: {
                    titleMarginBottom: 8,
                    titleFontColor: '#2d3748',
                    titleFontSize: 12,
                    backgroundColor: "rgba(255, 255, 255, 0.96)",
                    bodyFontColor: "#4a5568",
                    bodyFontSize: 12,
                    borderColor: '#cbd5e0',
                    borderWidth: 1,
                    xPadding: 12,
                    yPadding: 10,
                    displayColors: false,
                    caretPadding: 8,
                    callbacks: {
                        title: function(tooltipItems, data) {
                            var idx = tooltipItems[0].index;
                            return data.labels[idx] || '';
                        },
                        label: function(tooltipItem, chart) {
                            return 'Rata-Rata Capaian: ' + tooltipItem.yLabel + '%';
                        }
                    }
                }
            }
        });
    }

    // Chart.js - Donut Chart Predikat Kualifikasi
    var ctxDonut = document.getElementById("predikatDonutChart");
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chart_predikat['labels']) ?>,
                datasets: [{
                    data: <?= json_encode($chart_predikat['data']) ?>,
                    backgroundColor: ['#1cc88a', '#4e73df', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#2e59d9', '#dda20a', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)"
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10
                },
                legend: { display: false },
                cutoutPercentage: 70
            }
        });
    }
});
</script>
<?= $this->endSection(); ?>