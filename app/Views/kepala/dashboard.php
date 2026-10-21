<?= $this->extend('layouts/kepala') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Kepala Sekolah</h1>
    </div>

    <!-- Overview Cards -->
    <div class="row">
        <!-- Total Guru Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Guru</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $total_guru ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Supervisor Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Supervisor</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $total_supervisor ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Supervisions Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Supervisi Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count($today_schedules) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed This Month Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Penilaian Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $completed_count ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SIKLUS SUPERVISI KURIKULUM MERDEKA & KEBUTUHAN KOMBEL -->
    <div class="card shadow mb-4 border-top-primary">
        <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-sync-alt mr-1"></i> Progres Siklus Supervisi Kurikulum Merdeka (Ruang GTK / PMM)
            </h6>
            <span class="badge badge-light border text-muted">Perdirjen GTK No. 7607/2023</span>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-3 mb-md-0 border-right">
                    <span class="text-muted small text-uppercase font-weight-bold d-block">1. Pra-Observasi (Form A)</span>
                    <span class="h4 font-weight-bold text-warning d-block mt-2"><?= $kurmer_stages['pra_observasi'] ?? 0 ?></span>
                    <small class="text-muted">Target Perilaku Disepakati</small>
                </div>
                <div class="col-md-3 col-6 mb-3 mb-md-0 border-right">
                    <span class="text-muted small text-uppercase font-weight-bold d-block">2. Observasi Kelas (Form B)</span>
                    <span class="h4 font-weight-bold text-primary d-block mt-2"><?= $kurmer_stages['observasi'] ?? 0 ?></span>
                    <small class="text-muted">Rubrik Perilaku 1-3 PMM</small>
                </div>
                <div class="col-md-3 col-6 mb-3 mb-md-0 border-right">
                    <span class="text-muted small text-uppercase font-weight-bold d-block">3. Tindak Lanjut (Form C & D)</span>
                    <span class="h4 font-weight-bold text-info d-block mt-2"><?= $kurmer_stages['tindak_lanjut'] ?? 0 ?></span>
                    <small class="text-muted">Belajar Mandiri di Kombel/PMM</small>
                </div>
                <div class="col-md-3 col-6">
                    <span class="text-muted small text-uppercase font-weight-bold d-block">4. Selesai Paripurna</span>
                    <span class="h4 font-weight-bold text-success d-block mt-2"><?= $kurmer_stages['selesai'] ?? 0 ?></span>
                    <small class="text-muted">Refleksi Siklus Tuntas</small>
                </div>
            </div>

            <?php if (!empty($rekap_dukungan_kombel)): ?>
                <hr class="my-4">
                <h6 class="font-weight-bold text-dark mb-2">
                    <i class="fas fa-hands-helping text-primary mr-1"></i> Rekapitulasi Kebutuhan Dukungan Guru (Bahan Program Kombel & RKAS):
                </h6>
                <div class="row">
                    <?php foreach ($rekap_dukungan_kombel as $dukungan): ?>
                        <div class="col-md-4 mb-2">
                            <div class="p-2 border rounded bg-light d-flex justify-content-between align-items-center">
                                <span class="small font-weight-bold text-dark text-capitalize">
                                    <?= str_replace('_', ' ', $dukungan['kebutuhan_dukungan']) ?>
                                </span>
                                <span class="badge badge-primary badge-pill font-weight-bold"><?= $dukungan['total'] ?> Guru</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Radar Chart 8 Indikator PMM -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-chart-pie mr-1"></i> Rapor Capaian 8 Indikator Praktik Pembelajaran (PMM / Kurikulum Merdeka)
            </h6>
            <span class="badge badge-info">Skala 0 - 100%</span>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 360px; width: 100%;">
                <canvas id="chart8IndikatorPMM"></canvas>
            </div>
            <div class="mt-3 text-center small text-muted">
                Grafik jaring laba-laba menggambarkan rata-rata capaian mutu 8 indikator kinerja pembelajaran guru satuan pendidikan.
            </div>
        </div>
    </div>

    <div class="row">

        <!-- Today's Supervisions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Supervisi Hari Ini</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($today_schedules)): ?>
                        <p class="text-center text-muted">Tidak ada supervisi hari ini.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Guru</th>
                                        <th>Supervisor</th>
                                        <th>Kelas</th>
                                        <th>Jam</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($today_schedules as $schedule): ?>
                                        <tr>
                                            <td><?= $schedule['nama_guru'] ?></td>
                                            <td><?= $schedule['nama_supervisor'] ?></td>
                                            <td><?= $schedule['kelas'] ?></td>
                                            <td><?= $schedule['jam_ke'] ?></td>
                                            <td>
                                                <?php if ($schedule['status'] == 'Terjadwal'): ?>
                                                    <span class="badge badge-info"><?= $schedule['status'] ?></span>
                                                <?php elseif ($schedule['status'] == 'Selesai'): ?>
                                                    <span class="badge badge-success"><?= $schedule['status'] ?></span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary"><?= $schedule['status'] ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Upcoming Supervisions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Jadwal Mendatang</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($upcoming_schedules)): ?>
                        <p class="text-center text-muted">Tidak ada supervisi mendatang.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Guru</th>
                                        <th>Supervisor</th>
                                        <th>Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcoming_schedules as $schedule): ?>
                                        <tr>
                                            <td><?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                                            <td><?= $schedule['nama_guru'] ?></td>
                                            <td><?= $schedule['nama_supervisor'] ?></td>
                                            <td><?= $schedule['kelas'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Results -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Hasil Supervisi Terbaru</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($recent_results)): ?>
                        <p class="text-center text-muted">Belum ada hasil supervisi.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Guru</th>
                                        <th>Supervisor</th>
                                        <th>Jenis</th>
                                        <th>Nilai</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_results as $result): ?>
                                        <tr>
                                            <td><?= $result['nama_guru'] ?></td>
                                            <td><?= $result['nama_supervisor'] ?></td>
                                            <td><?= $result['jenis_penilaian'] ?></td>
                                            <td><?= number_format($result['nilai_akhir'], 2) ?></td>
                                            <td><?= format_tanggal_indonesia($result['tanggal_supervisi']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/vendor/chart.js/Chart.min.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctxRadar = document.getElementById('chart8IndikatorPMM');
    if (ctxRadar) {
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: <?= json_encode($pmm_radar_labels ?? []) ?>,
                datasets: [{
                    label: 'Rata-Rata Capaian (%)',
                    backgroundColor: 'rgba(32, 107, 196, 0.25)',
                    borderColor: '#206bc4',
                    pointBackgroundColor: '#206bc4',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#206bc4',
                    data: <?= json_encode($pmm_radar_scores ?? []) ?>
                }]
            },
            options: {
                scale: {
                    ticks: {
                        beginAtZero: true,
                        max: 100,
                        stepSize: 20
                    }
                },
                maintainAspectRatio: false
            }
        });
    }
});
</script>
<?= $this->endSection() ?>