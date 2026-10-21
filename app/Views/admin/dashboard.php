<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Ringkasan & Analitik Sistem</div>
            <h2 class="page-title text-dark fw-bold">Dashboard Administrator</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="d-flex align-items-center gap-2">
                <div class="text-end d-none d-md-block me-2">
                    <div class="fw-bold text-primary" id="waktuWIB"><?= format_waktu_indonesia(date('Y-m-d H:i:s')) ?></div>
                    <div class="small text-secondary"><?= format_tanggal_indonesia(date('Y-m-d')) ?></div>
                </div>
                <a href="<?= base_url('/admin/laporan') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-file-alt me-1"></i> Laporan
                </a>
                <a href="<?= base_url('/admin/jadwal/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Jadwal Baru
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Alert Banner -->
<div class="card card-sm mb-4 border-0 shadow-sm bg-primary-lt">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <span class="avatar bg-primary text-white rounded-circle">
                    <i class="fas fa-user-shield"></i>
                </span>
            </div>
            <div class="col">
                <h4 class="card-title text-primary mb-1 fw-bold">Selamat Datang di Sistem Supervisi Guru! 👋</h4>
                <div class="text-secondary small">
                    Pantau kinerja akademik, pelaksanaan jadwal supervisi, dan progres evaluasi pembelajaran secara real-time.
                </div>
            </div>
            <?php if (!empty($tahun_ajar_aktif)): ?>
            <div class="col-auto d-none d-lg-block">
                <span class="badge bg-green text-white px-3 py-2">
                    <i class="fas fa-calendar-check me-1"></i> TP Aktif: <?= esc($tahun_ajar_aktif['tahun_ajar']) ?> (<?= esc($tahun_ajar_aktif['semester']) ?>)
                </span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Row 1: Real-time Daily Stats -->
<div class="row row-cards mb-4">
    <!-- Supervisi Hari Ini -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-green-lt avatar avatar-md rounded">
                            <i class="fas fa-calendar-check fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-secondary text-uppercase fw-bold small">Supervisi Hari Ini</div>
                        <div class="h2 mb-0 fw-bold" id="todaySupervisions"><?= $today_stats['today_supervisions'] ?? 0 ?></div>
                        <div class="text-secondary small mt-1">Jadwal pelaksanaan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approval -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-warning-lt avatar avatar-md rounded">
                            <i class="fas fa-clock fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-secondary text-uppercase fw-bold small">Pending Approval</div>
                        <div class="h2 mb-0 fw-bold" id="pendingApprovals"><?= $today_stats['pending_approvals'] ?? 0 ?></div>
                        <div class="text-secondary small mt-1">Menunggu konfirmasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Users -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-azure-lt avatar avatar-md rounded">
                            <i class="fas fa-user-clock fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-secondary text-uppercase fw-bold small">Pengguna Aktif</div>
                        <div class="h2 mb-0 fw-bold" id="activeUsers"><?= $today_stats['active_users'] ?? 0 ?></div>
                        <div class="text-secondary small mt-1">Online saat ini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="col-sm-6 col-lg-3">
        <?php
        $health = $today_stats['system_health'] ?? 'good';
        $healthMeta = [
            'good' => ['label' => 'Normal', 'color' => 'bg-green-lt', 'icon' => 'fa-check-circle', 'text' => 'text-success'],
            'warning' => ['label' => 'Peringatan', 'color' => 'bg-warning-lt', 'icon' => 'fa-exclamation-circle', 'text' => 'text-warning'],
            'critical' => ['label' => 'Kritis', 'color' => 'bg-danger-lt', 'icon' => 'fa-times-circle', 'text' => 'text-danger']
        ];
        $meta = $healthMeta[$health] ?? $healthMeta['good'];
        ?>
        <div class="card card-sm shadow-sm h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="<?= $meta['color'] ?> avatar avatar-md rounded">
                            <i class="fas <?= $meta['icon'] ?> fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-secondary text-uppercase fw-bold small">Status Sistem</div>
                        <div class="h2 mb-0 fw-bold <?= $meta['text'] ?>"><?= $meta['label'] ?></div>
                        <div class="text-secondary small mt-1">Database & koneksi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Total Entitas Master -->
<div class="row row-cards mb-4">
    <!-- Total Users -->
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-bold small text-uppercase">Total Pengguna</span>
                    <span class="avatar avatar-sm bg-primary text-white rounded">
                        <i class="fas fa-users"></i>
                    </span>
                </div>
                <div class="h1 mb-2 fw-bold"><?= $total_users ?? 0 ?></div>
                <div class="text-success small fw-bold">
                    <i class="fas fa-check-circle me-1"></i> Terdaftar di sistem
                </div>
            </div>
        </div>
    </div>

    <!-- Total Guru -->
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-bold small text-uppercase">Total Guru</span>
                    <span class="avatar avatar-sm bg-success text-white rounded">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </span>
                </div>
                <div class="h1 mb-2 fw-bold"><?= $total_guru ?? 0 ?></div>
                <div class="text-secondary small">
                    <i class="fas fa-user-check me-1"></i> Tenaga pendidik
                </div>
            </div>
        </div>
    </div>

    <!-- Total Supervisor -->
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-bold small text-uppercase">Supervisor Aktif</span>
                    <span class="avatar avatar-sm bg-info text-white rounded">
                        <i class="fas fa-user-tie"></i>
                    </span>
                </div>
                <div class="h1 mb-2 fw-bold"><?= $total_supervisor ?? 0 ?></div>
                <div class="text-secondary small">
                    <i class="fas fa-clipboard-check me-1"></i> Penilai supervisi
                </div>
            </div>
        </div>
    </div>

    <!-- Total Kelas -->
    <div class="col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-bold small text-uppercase">Total Kelas</span>
                    <span class="avatar avatar-sm bg-warning text-white rounded">
                        <i class="fas fa-school"></i>
                    </span>
                </div>
                <div class="h1 mb-2 fw-bold"><?= $total_kelas ?? 0 ?></div>
                <div class="text-secondary small">
                    <i class="fas fa-door-open me-1"></i> Rombongan belajar
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Analitik Charts -->
<div class="row row-cards mb-4">
    <!-- Completion Rate Line Chart -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-chart-line text-success me-2"></i> Completion Rate (6 Bulan)
                </h3>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="completionRateChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Supervisor Workload Bar Chart -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-chart-bar text-primary me-2"></i> Beban Kerja Supervisor
                </h3>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="supervisorWorkloadChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Activity Chart -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-chart-area text-info me-2"></i> Tren Aktivitas Bulanan Pengguna
                </h3>
            </div>
            <div class="card-body">
                <div style="height: 250px;">
                    <canvas id="monthlyActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Detail Stats & Quick Access -->
<div class="row row-cards">
    <!-- Left Column: Jadwal, Distribusi, Aktivitas Login -->
    <div class="col-xl-8 col-lg-7">
        <!-- Jadwal Overview Cards -->
        <div class="row row-cards mb-3">
            <div class="col-md-4">
                <div class="card card-sm shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-blue-lt text-primary rounded me-3">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <div>
                                <div class="text-secondary small fw-bold">Total Jadwal</div>
                                <div class="h3 mb-0 fw-bold"><?= $total_jadwal ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-yellow-lt text-warning rounded me-3">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <div>
                                <div class="text-secondary small fw-bold">Terjadwal</div>
                                <div class="h3 mb-0 fw-bold"><?= $jadwal_terjadwal ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <span class="avatar bg-green-lt text-success rounded me-3">
                                <i class="fas fa-check-double"></i>
                            </span>
                            <div>
                                <div class="text-secondary small fw-bold">Selesai</div>
                                <div class="h3 mb-0 fw-bold"><?= $jadwal_selesai ?? 0 ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Role Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-chart-pie text-primary me-2"></i> Distribusi Peran Pengguna
                </h3>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div style="height: 220px;">
                            <canvas id="myPieChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span><i class="fas fa-chalkboard-teacher text-success me-1"></i> Guru</span>
                                <span><?= $user_stats['guru'] ?? 0 ?> Orang</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-success" style="width: <?= ($total_users > 0) ? ($user_stats['guru'] / $total_users * 100) : 0 ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span><i class="fas fa-user-tie text-info me-1"></i> Supervisor</span>
                                <span><?= $user_stats['supervisor'] ?? 0 ?> Orang</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-info" style="width: <?= ($total_users > 0) ? ($user_stats['supervisor'] / $total_users * 100) : 0 ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span><i class="fas fa-user-shield text-primary me-1"></i> Administrator</span>
                                <span><?= $user_stats['admin'] ?? 0 ?> Orang</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-primary" style="width: <?= ($total_users > 0) ? ($user_stats['admin'] / $total_users * 100) : 0 ?>%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span><i class="fas fa-graduation-cap text-warning me-1"></i> Kepala Madrasah</span>
                                <span><?= $user_stats['kepala'] ?? 0 ?> Orang</span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar bg-warning" style="width: <?= ($total_users > 0) ? ($user_stats['kepala'] / $total_users * 100) : 0 ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Radar Chart 8 Indikator PMM (Rapor Mutu Pembelajaran) -->
        <div class="card shadow-sm mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-chart-pie text-primary me-2"></i> Rapor Capaian 8 Indikator Praktik Pembelajaran (PMM)
                </h3>
                <span class="badge bg-blue-lt">Skala 0 - 100%</span>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 320px;">
                    <canvas id="chart8IndikatorPMMAdmin"></canvas>
                </div>
                <div class="mt-2 text-center text-secondary small">
                    Grafik jaring laba-laba agregat 8 indikator praktik kinerja guru Kurikulum Merdeka (Perdirjen GTK 7607/2023).
                </div>
            </div>
        </div>

        <!-- Rekapitulasi Kebutuhan Dukungan Kombel (RKAS) -->
        <?php if (!empty($rekap_dukungan_kombel)): ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-hands-helping text-success me-2"></i> Rekapitulasi Kebutuhan Dukungan Guru (Kombel & Evaluasi RKAS)
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($rekap_dukungan_kombel as $dukungan): ?>
                        <div class="col-md-6 mb-2">
                            <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                                <span class="small fw-bold text-dark text-capitalize">
                                    <?= str_replace('_', ' ', esc($dukungan['kebutuhan_dukungan'])) ?>
                                </span>
                                <span class="badge bg-primary text-white font-weight-bold"><?= $dukungan['total'] ?> Guru</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Aktivitas Login Terbaru Table -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-history text-secondary me-2"></i> Aktivitas Login Terkini
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table table-striped">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Role</th>
                            <th>Waktu Login</th>
                            <th class="text-end">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_activities)): ?>
                            <?php foreach ($recent_activities as $activity): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-primary-lt me-2 rounded-circle">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <div>
                                                <div class="fw-bold text-dark"><?= esc($activity['username']) ?></div>
                                                <div class="text-secondary small"><?= esc($activity['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-blue-lt text-capitalize"><?= esc($activity['role']) ?></span>
                                    </td>
                                    <td class="text-secondary small">
                                        <i class="far fa-clock me-1"></i> <?= date('d M Y, H:i', strtotime($activity['last_login'])) ?>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success-lt">Aktif</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">
                                    <i class="fas fa-info-circle me-1"></i> Belum ada rekaman aktivitas terbaru
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: TP Aktif, Aksi Cepat, Riwayat -->
    <div class="col-xl-4 col-lg-5">
        <!-- Card Tahun Ajaran Aktif -->
        <div class="card shadow-sm mb-3">
            <div class="card-status-top bg-success"></div>
            <div class="card-body text-center py-4">
                <span class="avatar avatar-md bg-green-lt text-success rounded-circle mb-3">
                    <i class="fas fa-graduation-cap fs-2"></i>
                </span>
                <div class="text-secondary text-uppercase fw-bold small mb-1">Tahun Ajaran Aktif</div>
                <?php if ($tahun_ajar_aktif): ?>
                    <h2 class="fw-bold text-dark mb-1"><?= esc($tahun_ajar_aktif['tahun_ajar']) ?></h2>
                    <div class="badge bg-green text-white px-3 py-1 mb-2">Semester <?= esc($tahun_ajar_aktif['semester']) ?></div>
                    <div class="text-secondary small">Periode supervisi operasional sedang berjalan</div>
                <?php else: ?>
                    <div class="text-muted py-2">Belum ada tahun ajaran aktif</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card Aksi Cepat -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-bolt text-warning me-2"></i> Pintasan & Aksi Cepat
                </h3>
            </div>
            <div class="card-body p-3">
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <a href="<?= base_url('/admin/pengguna/create') ?>" class="card card-link card-link-pop text-center p-3 h-100 border text-decoration-none">
                            <span class="avatar bg-blue-lt text-primary mx-auto mb-2 rounded">
                                <i class="fas fa-user-plus"></i>
                            </span>
                            <div class="fw-bold text-dark small">Tambah User</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('/admin/akademik/tahun-ajar') ?>" class="card card-link card-link-pop text-center p-3 h-100 border text-decoration-none">
                            <span class="avatar bg-green-lt text-success mx-auto mb-2 rounded">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <div class="fw-bold text-dark small">Tahun Ajaran</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('/admin/jadwal/create') ?>" class="card card-link card-link-pop text-center p-3 h-100 border text-decoration-none">
                            <span class="avatar bg-azure-lt text-info mx-auto mb-2 rounded">
                                <i class="fas fa-calendar-plus"></i>
                            </span>
                            <div class="fw-bold text-dark small">Jadwal Baru</div>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('/admin/laporan') ?>" class="card card-link card-link-pop text-center p-3 h-100 border text-decoration-none">
                            <span class="avatar bg-yellow-lt text-warning mx-auto mb-2 rounded">
                                <i class="fas fa-chart-bar"></i>
                            </span>
                            <div class="fw-bold text-dark small">Rekap Nilai</div>
                        </a>
                    </div>
                </div>
                <a href="<?= base_url('/admin/laporan/hasil-supervisi') ?>" class="btn btn-primary w-100 py-2">
                    <i class="fas fa-clipboard-check me-2"></i> Laporan Hasil Supervisi
                </a>
            </div>
        </div>

        <!-- Riwayat Tahun Ajaran -->
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-history text-secondary me-2"></i> Riwayat Tahun Ajaran
                </h3>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($tahun_ajar_stats)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($tahun_ajar_stats as $tahun => $stats): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <div>
                                    <div class="fw-bold text-dark"><?= esc($tahun) ?></div>
                                    <div class="text-secondary small"><?= esc($stats['kelas_count']) ?> Rombel Kelas</div>
                                </div>
                                <?php if ($stats['status'] === 'Aktif'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-lt">Nonaktif</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-3 text-center text-secondary small">Belum ada data riwayat</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/chart.js/Chart.min.js') ?>"></script>
<script>
    // Real-time WIB Clock updater
    function updateWaktuWIB() {
        const options = {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };
        const now = new Date();
        const waktuWIB = now.toLocaleTimeString('id-ID', options);
        const el = document.getElementById('waktuWIB');
        if (el) el.textContent = waktuWIB + ' WIB';
    }
    setInterval(updateWaktuWIB, 1000);
    updateWaktuWIB();

    // Tabler Chart.js Theme Colors
    const tablerColors = {
        primary: '#206bc4',
        success: '#2fb344',
        info: '#4299e1',
        warning: '#f59f00',
        danger: '#d63939',
        gray: '#64748b'
    };

    // 1. Role Distribution Donut Chart
    var ctx = document.getElementById("myPieChart");
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Admin", "Guru", "Supervisor", "Kepala"],
                datasets: [{
                    data: [
                        <?= $user_stats['admin'] ?? 0 ?>,
                        <?= $user_stats['guru'] ?? 0 ?>,
                        <?= $user_stats['supervisor'] ?? 0 ?>,
                        <?= $user_stats['kepala'] ?? 0 ?>
                    ],
                    backgroundColor: [tablerColors.primary, tablerColors.success, tablerColors.info, tablerColors.warning],
                    hoverBackgroundColor: ['#1d5baf', '#279a3a', '#3182ce', '#dd8c00'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { family: 'inherit', size: 11 } }
                    }
                },
                cutout: '70%'
            }
        });
    }

    // 2. Completion Rate Line Chart
    var completionCtx = document.getElementById("completionRateChart");
    if (completionCtx) {
        new Chart(completionCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($completion_rate['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Tingkat Ketercapaian (%)',
                    data: <?= json_encode($completion_rate['data'] ?? []) ?>,
                    backgroundColor: 'rgba(47, 179, 68, 0.1)',
                    borderColor: tablerColors.success,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: tablerColors.success,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(val) { return val + '%'; }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 3. Supervisor Workload Bar Chart
    var workloadCtx = document.getElementById("supervisorWorkloadChart");
    if (workloadCtx) {
        new Chart(workloadCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($supervisor_workload['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Total Supervisi',
                    data: <?= json_encode($supervisor_workload['data'] ?? []) ?>,
                    backgroundColor: tablerColors.primary,
                    borderRadius: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 4. Monthly Activity Line Chart
    var activityCtx = document.getElementById("monthlyActivityChart");
    if (activityCtx) {
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($monthly_activity['labels'] ?? []) ?>,
                datasets: [{
                    label: 'Pengguna Aktif',
                    data: <?= json_encode($monthly_activity['data'] ?? []) ?>,
                    backgroundColor: 'rgba(66, 153, 225, 0.1)',
                    borderColor: tablerColors.info,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: tablerColors.info,
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // 5. Radar Chart 8 Indikator PMM
    var radarCtx = document.getElementById("chart8IndikatorPMMAdmin");
    if (radarCtx) {
        new Chart(radarCtx, {
            type: 'radar',
            data: {
                labels: <?= json_encode($pmm_radar_labels ?? []) ?>,
                datasets: [{
                    label: 'Capaian Mutu (%)',
                    backgroundColor: 'rgba(32, 107, 196, 0.25)',
                    borderColor: tablerColors.primary,
                    pointBackgroundColor: tablerColors.primary,
                    pointBorderColor: '#ffffff',
                    pointHoverBackgroundColor: '#ffffff',
                    pointHoverBorderColor: tablerColors.primary,
                    data: <?= json_encode($pmm_radar_scores ?? []) ?>
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scale: {
                    ticks: {
                        beginAtZero: true,
                        max: 100,
                        stepSize: 20
                    }
                }
            }
        });
    }
</script>
<?= $this->endSection(); ?>