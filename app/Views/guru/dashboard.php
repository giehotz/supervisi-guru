<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-2 guru-dashboard-page">

    <!-- Greeting & Quick Action Header -->
    <div class="card shadow-sm border-0 mb-4 bg-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar bg-primary-lt text-primary rounded-circle p-2" style="width: 52px; height: 52px;">
                        <i class="fas fa-chalkboard-teacher fs-2"></i>
                    </span>
                    <div>
                        <div class="text-uppercase text-muted fw-semibold small tracking-wide mb-1">
                            Portal Supervisi Akademik Guru
                        </div>
                        <h1 class="h2 fw-bold text-dark mb-1 tracking-tight">
                            Selamat Datang, <?= esc($guru['nama'] ?? session()->get('username')) ?>!
                        </h1>
                        <div class="d-flex flex-wrap align-items-center gap-2 text-muted small">
                            <span><i class="fas fa-id-card me-1 text-muted"></i> NIP: <?= esc($guru['nip'] ?? ($guru['nuptk'] ?? '-')) ?></span>
                            <span>&bull;</span>
                            <span><i class="fas fa-calendar-check me-1 text-muted"></i> Tahun Ajaran Aktif</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="<?= base_url('guru/jadwal') ?>" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Jadwal Supervisi</span>
                    </a>
                    <a href="<?= base_url('guru/dokumen-ajar') ?>" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="fas fa-folder-open"></i>
                        <span>Dokumen Perangkat Ajar</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php
        // Calculate dynamic average score from completed evaluations
        $totalNilai = 0;
        $countNilai = 0;
        foreach ($recentHasil as $jadwal) {
            if (!empty($jadwal['hasil_items'])) {
                foreach ($jadwal['hasil_items'] as $hasil) {
                    if (isset($hasil['nilai_akhir']) && !is_null($hasil['nilai_akhir'])) {
                        $totalNilai += $hasil['nilai_akhir'];
                        $countNilai++;
                    }
                }
            }
        }
        $avgScore = $countNilai > 0 ? ($totalNilai / $countNilai) : 0;
        if ($avgScore >= 86) {
            $avgPred = 'Baik Sekali';
            $avgBadgeClass = 'bg-success-lt text-success';
        } elseif ($avgScore >= 70) {
            $avgPred = 'Baik';
            $avgBadgeClass = 'bg-primary-lt text-primary';
        } elseif ($avgScore >= 55) {
            $avgPred = 'Cukup';
            $avgBadgeClass = 'bg-warning-lt text-warning';
        } else {
            $avgPred = $countNilai > 0 ? 'Kurang' : 'Belum Ada Nilai';
            $avgBadgeClass = $countNilai > 0 ? 'bg-danger-lt text-danger' : 'bg-secondary-lt text-muted';
        }
    ?>

    <!-- Three KPI Metric Cards (Tabler Clean Style) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Supervisi Mendatang -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted fw-semibold small tracking-wide">
                            Supervisi Mendatang
                        </span>
                        <span class="avatar bg-blue-lt text-blue rounded-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <span class="h1 fw-bold text-dark mb-0 tabular-nums">
                            <?= count($upcomingSchedules) ?>
                        </span>
                        <span class="text-muted small">Jadwal Aktif</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="text-muted small">Tahap Pelaksanaan</span>
                        <a href="<?= base_url('guru/jadwal') ?>" class="text-primary small text-decoration-none fw-semibold">
                            Lihat Rincian &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Supervisi Selesai -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted fw-semibold small tracking-wide">
                            Supervisi Selesai
                        </span>
                        <span class="avatar bg-green-lt text-green rounded-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-clipboard-check"></i>
                        </span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <span class="h1 fw-bold text-dark mb-0 tabular-nums">
                            <?= count($completedSchedules) ?>
                        </span>
                        <span class="text-muted small">Telah Dinilai</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="text-muted small">Riwayat Pelaksanaan</span>
                        <a href="<?= base_url('guru/hasil') ?>" class="text-success small text-decoration-none fw-semibold">
                            Lihat Riwayat &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Rata-rata Nilai Capaian -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted fw-semibold small tracking-wide">
                            Rata-rata Capaian Nilai
                        </span>
                        <span class="avatar bg-teal-lt text-teal rounded-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-award"></i>
                        </span>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <span class="h1 fw-bold text-dark mb-0 tabular-nums">
                            <?= number_format($avgScore, 2) ?>
                        </span>
                        <span class="text-muted small">%</span>
                        <span class="badge <?= $avgBadgeClass ?> px-2 py-1 rounded-pill small fw-semibold ms-auto">
                            <?= $avgPred ?>
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <span class="text-muted small">Kualifikasi Keseluruhan</span>
                        <a href="<?= base_url('guru/hasil') ?>" class="text-teal small text-decoration-none fw-semibold">
                            Rincian Komponen &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid: Upcoming Schedules & Recent Results -->
    <div class="row g-4 mb-4">
        <!-- Kolom Kiri: Jadwal Supervisi Mendatang -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            Jadwal Supervisi Mendatang
                        </h2>
                        <p class="text-muted small mb-0">Agenda observasi kelas terdekat yang perlu dipersiapkan.</p>
                    </div>
                    <span class="badge bg-primary-lt px-2 py-1 rounded-pill tabular-nums">
                        <?= count($upcomingSchedules) ?> Jadwal
                    </span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($upcomingSchedules)): ?>
                        <div class="p-4 text-center text-muted">
                            <div class="avatar bg-light text-muted rounded-circle mb-3 mx-auto" style="width: 56px; height: 56px;">
                                <i class="fas fa-calendar-check fs-2"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Tidak Ada Jadwal Mendatang</h6>
                            <p class="small text-muted mb-0">Semua agenda supervisi telah terlaksana atau belum dijadwalkan oleh supervisor.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-vcenter mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 text-uppercase text-muted fw-semibold small tracking-wide">Tanggal & Waktu</th>
                                        <th class="text-uppercase text-muted fw-semibold small tracking-wide">Mata Pelajaran & Kelas</th>
                                        <th class="text-uppercase text-muted fw-semibold small tracking-wide">Supervisor</th>
                                        <th class="text-end pe-3 text-uppercase text-muted fw-semibold small tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingSchedules as $schedule): ?>
                                        <tr>
                                            <td class="ps-3">
                                                <span class="fw-bold text-dark d-block tabular-nums">
                                                    <?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?>
                                                </span>
                                                <small class="text-muted">
                                                    Jam Ke-<?= esc($schedule['jam_ke'] ?? '-') ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="fw-semibold text-dark d-block">
                                                    <?= esc($schedule['mata_pelajaran']) ?>
                                                </span>
                                                <span class="badge bg-azure-lt px-2 py-0 small">
                                                    Kelas <?= esc($schedule['nama_kelas'] ?? ($schedule['kelas'] ?? '-')) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-secondary small d-flex align-items-center gap-1">
                                                    <i class="fas fa-user-tie text-muted"></i>
                                                    <?= esc($schedule['supervisor_name'] ?? 'Supervisor') ?>
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('guru/dokumen-ajar/manage/' . $schedule['id']) ?>" 
                                                       class="btn btn-outline-primary btn-pill" 
                                                       title="Kelola Dokumen Ajar">
                                                        <i class="fas fa-file-upload"></i>
                                                    </a>
                                                    <a href="<?= base_url('guru/pra-observasi/' . $schedule['id']) ?>" 
                                                       class="btn btn-outline-teal btn-pill" 
                                                       title="Formulir Pra-Observasi">
                                                        <i class="fas fa-clipboard-list"></i>
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
                <div class="card-footer bg-white border-top py-2 px-3 text-end">
                    <a href="<?= base_url('guru/jadwal') ?>" class="text-primary small text-decoration-none fw-semibold">
                        Buka Semua Jadwal &rarr;
                    </a>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Hasil Supervisi Terbaru -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fas fa-chart-line text-success"></i>
                            Hasil Supervisi Terbaru
                        </h2>
                        <p class="text-muted small mb-0">Capaian lembar penilaian pada observasi yang telah tuntas.</p>
                    </div>
                    <span class="badge bg-green-lt px-2 py-1 rounded-pill tabular-nums">
                        <?= count($recentHasil) ?> Data
                    </span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentHasil)): ?>
                        <div class="p-4 text-center text-muted">
                            <div class="avatar bg-light text-muted rounded-circle mb-3 mx-auto" style="width: 56px; height: 56px;">
                                <i class="fas fa-inbox fs-2"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Belum Ada Hasil Supervisi</h6>
                            <p class="small text-muted mb-0">Hasil penilaian observasi akan tampil di sini setelah diverifikasi oleh supervisor.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-vcenter mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 text-uppercase text-muted fw-semibold small tracking-wide">Tanggal & Mapel</th>
                                        <th class="text-center text-uppercase text-muted fw-semibold small tracking-wide">Capaian Komponen</th>
                                        <th class="text-center text-uppercase text-muted fw-semibold small tracking-wide">Rata-rata</th>
                                        <th class="text-end pe-3 text-uppercase text-muted fw-semibold small tracking-wide">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentHasil as $jadwal): ?>
                                        <?php
                                            $nilaiMap = [1 => null, 2 => null, 3 => null, 4 => null];
                                            $totalJadwal = 0;
                                            $countJadwal = 0;
                                            if (!empty($jadwal['hasil_items'])) {
                                                foreach ($jadwal['hasil_items'] as $hasil) {
                                                    if (isset($hasil['nilai_akhir']) && isset($hasil['jenis_penilaian_id'])) {
                                                        $nilaiMap[$hasil['jenis_penilaian_id']] = $hasil['nilai_akhir'];
                                                        $totalJadwal += $hasil['nilai_akhir'];
                                                        $countJadwal++;
                                                    }
                                                }
                                            }
                                            $avgJadwal = $countJadwal > 0 ? ($totalJadwal / $countJadwal) : 0;
                                        ?>
                                        <tr>
                                            <td class="ps-3">
                                                <span class="fw-bold text-dark d-block tabular-nums">
                                                    <?= format_tanggal_indonesia($jadwal['tanggal_supervisi']) ?>
                                                </span>
                                                <small class="text-muted">
                                                    <?= esc($jadwal['mata_pelajaran'] ?? '-') ?> &bull; <?= esc($jadwal['nama_kelas'] ?? ($jadwal['kelas'] ?? '-')) ?>
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1" title="Komponen: 1.Adm, 2.Proses, 3.Eval, 4.Pengembangan">
                                                    <?php for ($i = 1; $i <= 4; $i++): ?>
                                                        <?php if (!is_null($nilaiMap[$i])): ?>
                                                            <span class="badge bg-light text-dark border px-1 py-0 small tabular-nums" title="Komponen <?= $i ?>: <?= number_format($nilaiMap[$i], 0) ?>%">
                                                                K<?= $i ?>: <?= number_format($nilaiMap[$i], 0) ?>%
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-light text-muted border px-1 py-0 small" title="Komponen <?= $i ?>: Belum dinilai">-</span>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary-lt text-primary px-2 py-1 rounded-pill tabular-nums fw-bold">
                                                    <?= number_format($avgJadwal, 1) ?>%
                                                </span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="<?= base_url('guru/hasil/' . $jadwal['id']) ?>" 
                                                   class="btn btn-pill btn-outline-primary"
                                                   title="Lihat Detail Hasil Supervisi">
                                                    <i class="fas fa-eye me-1"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-top py-2 px-3 text-end">
                    <a href="<?= base_url('guru/hasil') ?>" class="text-success small text-decoration-none fw-semibold">
                        Buka Semua Hasil &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Panduan Alur Siklus Supervisi Akademik (Kurikulum Merdeka) -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3">
            <h2 class="h5 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-route text-primary"></i>
                Alur Siklus Supervisi Pembelajaran (Kurikulum Merdeka)
            </h2>
            <p class="text-muted small mb-0">Tahapan terintegrasi mulai dari perencanaan dokumen hingga refleksi tindak lanjut.</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary text-white rounded-circle p-2" style="width: 28px; height: 28px; line-height: 12px; text-align: center;">1</span>
                            <span class="fw-bold text-dark small text-uppercase">Perangkat Ajar</span>
                        </div>
                        <p class="small text-muted mb-0">
                            Unggah Modul Ajar / RPP, Bahan Ajar, Asesmen, dan tautkan bukti karya PMM ke sistem.
                        </p>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-teal text-white rounded-circle p-2" style="width: 28px; height: 28px; line-height: 12px; text-align: center;">2</span>
                            <span class="fw-bold text-dark small text-uppercase">Pra-Observasi</span>
                        </div>
                        <p class="small text-muted mb-0">
                            Isi Formulir A bersama supervisor untuk menyepakati fokus indikator observasi dan strategi pembelajaran.
                        </p>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-azure text-white rounded-circle p-2" style="width: 28px; height: 28px; line-height: 12px; text-align: center;">3</span>
                            <span class="fw-bold text-dark small text-uppercase">Observasi Kelas</span>
                        </div>
                        <p class="small text-muted mb-0">
                            Pelaksanaan pembelajaran di kelas dinilai oleh supervisor sesuai rubrik observasi Form B.
                        </p>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-green text-white rounded-circle p-2" style="width: 28px; height: 28px; line-height: 12px; text-align: center;">4</span>
                            <span class="fw-bold text-dark small text-uppercase">Tindak Lanjut & Refleksi</span>
                        </div>
                        <p class="small text-muted mb-0">
                            Diskusikan umpan balik supervisor, susun rencana pengembangan diri (Form C), dan evaluasi refleksi (Form D).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
/* Dashboard Polish Styles */
.guru-dashboard-page {
    font-family: var(--tblr-font-sans-serif, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif);
    color: #1e293b;
}
.guru-dashboard-page .tabular-nums {
    font-variant-numeric: tabular-nums;
}
.guru-dashboard-page .tracking-tight {
    letter-spacing: -0.02em;
}
.guru-dashboard-page .tracking-wide {
    letter-spacing: 0.04em;
}
.guru-dashboard-page .table-vcenter td, 
.guru-dashboard-page .table-vcenter th {
    vertical-align: middle;
}
</style>
<?= $this->endSection() ?>