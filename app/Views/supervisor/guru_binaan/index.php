<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Guru Binaan & Pantauan Siklus Kurikulum Merdeka</h1>
            <p class="text-muted small mb-0">Pantau progres siklus supervisi (Formulir A, B, C, D) untuk seluruh guru yang dibina.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Guru Binaan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tahap Pra-Observasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['pra_observasi'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Observasi & Pasca</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['observasi'] + $stats['tindak_lanjut'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Siklus Tuntas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['selesai'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Matriks Guru Binaan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Matriks Status Siklus Supervisi (A–D)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light text-center">
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th>Guru Binaan</th>
                            <th>Mata Pelajaran / Kelas</th>
                            <th>Fokus Target PMM</th>
                            <th style="width: 10%;">Form A (Pra)</th>
                            <th style="width: 10%;">Form B (Obs)</th>
                            <th style="width: 10%;">Form C (TL)</th>
                            <th style="width: 10%;">Form D (Ref)</th>
                            <th style="width: 12%;">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($guruBinaan)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Belum ada guru binaan yang dijadwalkan untuk Anda.</td>
                        </tr>
                        <?php else: ?>
                        <?php $no = 1; foreach ($guruBinaan as $row): $j = $row['jadwal']; ?>
                        <tr>
                            <td class="text-center align-middle"><?= $no++ ?></td>
                            <td class="align-middle">
                                <strong><?= esc($j['nama_guru']) ?></strong><br>
                                <small class="text-muted">NIP: <?= esc($j['nip_guru'] ?? '-') ?></small>
                            </td>
                            <td class="align-middle">
                                <?= esc($j['mata_pelajaran'] ?? $j['guru_mapel'] ?? '-') ?><br>
                                <span class="badge badge-light border">Kelas <?= esc($j['nama_kelas'] ?? $j['kelas'] ?? '-') ?></span>
                            </td>
                            <td class="align-middle">
                                <?php if (!empty($j['nama_indikator'])): ?>
                                    <span class="badge badge-primary"><?= esc($j['nama_indikator']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">Supervisi Umum / Reguler</span>
                                <?php endif; ?>
                            </td>
                            <!-- Form A Status -->
                            <td class="text-center align-middle">
                                <?php if ($row['formA_status'] === 'disepakati'): ?>
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Disepakati</span>
                                <?php elseif ($row['formA_status'] === 'diajukan'): ?>
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Perlu Review</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Belum Diisi</span>
                                <?php endif; ?>
                            </td>
                            <!-- Form B Status -->
                            <td class="text-center align-middle">
                                <?php if ($row['formB_status'] === 'selesai'): ?>
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Selesai</span>
                                    <?php if (!empty($j['nilai_akhir'])): ?>
                                        <div class="small font-weight-bold text-primary mt-1"><?= round($j['nilai_akhir'], 1) ?>%</div>
                                    <?php endif; ?>
                                <?php elseif ($row['formB_status'] === 'proses'): ?>
                                    <span class="badge badge-info"><i class="fas fa-spinner"></i> Siap Amati</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Menunggu</span>
                                <?php endif; ?>
                            </td>
                            <!-- Form C Status -->
                            <td class="text-center align-middle">
                                <?php if ($row['formC_status'] === 'disepakati'): ?>
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> Disepakati</span>
                                <?php elseif ($row['formC_status'] === 'terisi'): ?>
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Butuh Feedback</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Belum</span>
                                <?php endif; ?>
                            </td>
                            <!-- Form D Status -->
                            <td class="text-center align-middle">
                                <?php if ($row['formD_status'] === 'selesai'): ?>
                                    <span class="badge badge-success"><i class="fas fa-star"></i> Dinilai</span>
                                <?php elseif ($row['formD_status'] === 'terisi'): ?>
                                    <span class="badge badge-warning"><i class="fas fa-clock"></i> Perlu Evaluasi</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Belum</span>
                                <?php endif; ?>
                            </td>
                            <!-- Aksi Cepat -->
                            <td class="text-center align-middle">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= base_url('supervisor/pra-observasi/' . $j['id']) ?>" class="btn btn-outline-info" title="Form A Pra-Observasi & TIRTA">
                                        <i class="fas fa-comments"></i> A
                                    </a>
                                    <a href="<?= base_url('supervisor/penilaian/form/' . $j['id']) ?>" class="btn btn-outline-primary" title="Form B Observasi">
                                        <i class="fas fa-clipboard-check"></i> B
                                    </a>
                                    <a href="<?= base_url('supervisor/tindak-lanjut/' . $j['id']) ?>" class="btn btn-outline-warning" title="Form C & D Tindak Lanjut">
                                        <i class="fas fa-tasks"></i> C/D
                                    </a>
                                </div>
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
<?= $this->endSection() ?>
