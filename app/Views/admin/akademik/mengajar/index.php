<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Pembagian Mengajar Guru (Jadwal KBM)</h1>
            <p class="text-muted small mb-0">Atur pemetaan guru, mata pelajaran, kelas, serta hari dan sesi jam mengajar rutin madrasah.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalAddMengajar">
                <i class="fas fa-calendar-plus mr-1"></i> Tambah Plotting Mengajar
            </button>
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
            <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filter & Info Card -->
    <div class="card shadow border-0 mb-4">
        <div class="card-body p-3 bg-light rounded">
            <form method="get" action="<?= base_url('admin/akademik/mengajar') ?>" class="row align-items-center">
                <div class="col-md-5 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-gray-700 mb-1">
                        <i class="fas fa-calendar-alt mr-1 text-primary"></i> Tahun Ajaran & Semester:
                    </label>
                    <select class="form-control form-control-sm" name="tahun_ajar_id" onchange="this.form.submit()">
                        <option value="">-- Semua Tahun Ajaran --</option>
                        <?php foreach ($tahun_ajars as $tahun): ?>
                            <option value="<?= $tahun['id'] ?>" <?= $selectedTahunId == $tahun['id'] ? 'selected' : '' ?>>
                                <?= esc($tahun['tahun_ajar']) ?> - <?= esc($tahun['semester']) ?> <?= ($tahun['status_aktif'] ?? '') === 'Aktif' ? '(Aktif)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-5 mb-2 mb-md-0">
                    <label class="small font-weight-bold text-gray-700 mb-1">
                        <i class="fas fa-graduation-cap mr-1 text-primary"></i> Filter Rombel / Kelas:
                    </label>
                    <select class="form-control form-control-sm" name="kelas_id" onchange="this.form.submit()">
                        <option value="">-- Semua Kelas / Rombel --</option>
                        <?php foreach ($kelases as $kls): ?>
                            <option value="<?= $kls['id'] ?>" <?= $selectedKelasId == $kls['id'] ? 'selected' : '' ?>>
                                Kelas <?= esc($kls['nama_kelas']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 mt-md-4 text-right">
                    <?php if ($selectedTahunId || $selectedKelasId): ?>
                        <a href="<?= base_url('admin/akademik/mengajar') ?>" class="btn btn-outline-secondary btn-sm btn-block" title="Reset Filter">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-1"></i> Distribusi Jadwal Mengajar Guru
            </h6>
            <span class="badge badge-primary px-3 py-2 font-weight-bold">
                Total: <?= $totalPlotting ?> Jadwal Terdaftar
            </span>
        </div>
        <div class="card-body">
            <?php if (empty($jadwals)): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-calendar-times fa-3x mb-3 text-gray-300"></i>
                    <p class="mb-1 font-weight-bold">Belum ada data jadwal mengajar untuk filter terpilih.</p>
                    <small>Klik tombol <strong>"Tambah Plotting Mengajar"</strong> di atas untuk menambahkan jadwal guru.</small>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="15%">Hari & Waktu</th>
                                <th width="20%">Kelas & Rombel</th>
                                <th width="25%">Mata Pelajaran</th>
                                <th width="25%">Guru Pengampu</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($jadwals as $j): ?>
                                <tr>
                                    <td class="text-center align-middle font-weight-bold"><?= $no++ ?></td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-gray-800">
                                            <i class="far fa-calendar-check text-primary mr-1"></i> <?= esc($j['hari']) ?>
                                        </div>
                                        <span class="badge badge-light border text-primary small">
                                            Jam Ke-<?= $j['jam_mulai_ke'] ?> s/d Ke-<?= $j['jam_selesai_ke'] ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-primary px-2 py-1 font-weight-bold">
                                            Kelas <?= esc($j['nama_kelas'] ?? '-') ?>
                                        </span>
                                        <div class="small text-muted mt-1">
                                            <?= esc($j['tahun_ajar'] ?? '') ?> (<?= esc($j['semester'] ?? '') ?>)
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <strong class="text-gray-800"><?= esc($j['nama_mapel'] ?? '-') ?></strong>
                                        <?php if (!empty($j['kode_mapel'])): ?>
                                            <small class="text-muted">(<?= esc($j['kode_mapel']) ?>)</small>
                                        <?php endif; ?>
                                        <div>
                                            <span class="badge badge-light border text-muted small mt-1">
                                                <?= esc($j['kelompok'] ?? 'Umum') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-gray-800">
                                            <?= esc($j['nama_guru'] ?? '-') ?>
                                        </div>
                                        <small class="text-muted">
                                            NIP: <?= esc($j['nip'] ?: '-') ?>
                                        </small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <a href="<?= base_url('admin/akademik/mengajar/' . $j['id'] . '/delete') ?>"
                                           class="btn btn-outline-danger btn-sm"
                                           onclick="return confirm('Hapus jadwal mengajar guru <?= esc(addslashes($j['nama_guru'] ?? '')) ?> di kelas <?= esc(addslashes($j['nama_kelas'] ?? '')) ?>?')"
                                           title="Hapus Jadwal">
                                            <i class="fas fa-trash"></i>
                                        </a>
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

<!-- Modal Tambah Plotting Mengajar -->
<div class="modal fade" id="modalAddMengajar" tabindex="-1" role="dialog" aria-labelledby="modalAddMengajarTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('admin/akademik/mengajar/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalAddMengajarTitle">
                        <i class="fas fa-calendar-plus mr-2"></i> Tambah Pembagian Mengajar Guru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="small font-weight-bold text-gray-700">Tahun Ajaran & Semester <span class="text-danger">*</span></label>
                            <select class="form-control" name="tahun_ajar_id" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach ($tahun_ajars as $tahun): ?>
                                    <option value="<?= $tahun['id'] ?>" <?= $selectedTahunId == $tahun['id'] ? 'selected' : '' ?>>
                                        <?= esc($tahun['tahun_ajar']) ?> - <?= esc($tahun['semester']) ?> <?= ($tahun['status_aktif'] ?? '') === 'Aktif' ? '(Aktif)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label class="small font-weight-bold text-gray-700">Kelas / Rombel <span class="text-danger">*</span></label>
                            <select class="form-control" name="kelas_id" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelases as $kls): ?>
                                    <option value="<?= $kls['id'] ?>" <?= $selectedKelasId == $kls['id'] ? 'selected' : '' ?>>
                                        Kelas <?= esc($kls['nama_kelas']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="small font-weight-bold text-gray-700">Guru Pengampu <span class="text-danger">*</span></label>
                            <select class="form-control" name="guru_id" required>
                                <option value="">-- Pilih Guru --</option>
                                <?php foreach ($gurus as $guru): ?>
                                    <option value="<?= $guru['id'] ?>">
                                        <?= esc($guru['nama']) ?> <?= !empty($guru['nip']) ? '(' . esc($guru['nip']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6 form-group mb-3">
                            <label class="small font-weight-bold text-gray-700">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-control" name="mapel_id" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                <?php foreach ($mapels as $m): ?>
                                    <option value="<?= $m['id'] ?>">
                                        <?= esc($m['nama_mapel']) ?> (<?= esc($m['kelompok']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="small font-weight-bold text-gray-700">Hari Mengajar <span class="text-danger">*</span></label>
                            <select class="form-control" name="hari" required>
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                            </select>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label class="small font-weight-bold text-gray-700">Mulai Jam Ke- <span class="text-danger">*</span></label>
                            <select class="form-control" name="jam_mulai_ke" required>
                                <option value="1">Jam Ke-1 (07.30 - 08.05)</option>
                                <option value="2">Jam Ke-2 (08.05 - 08.40)</option>
                                <option value="3">Jam Ke-3 (08.40 - 09.15)</option>
                                <option value="4">Jam Ke-4 (09.15 - 09.50)</option>
                                <option value="6">Jam Ke-6 (10.10 - 10.45)</option>
                                <option value="7">Jam Ke-7 (10.45 - 11.20)</option>
                            </select>
                        </div>

                        <div class="col-md-4 form-group mb-3">
                            <label class="small font-weight-bold text-gray-700">Selesai Jam Ke- <span class="text-danger">*</span></label>
                            <select class="form-control" name="jam_selesai_ke" required>
                                <option value="1">Jam Ke-1 (07.30 - 08.05)</option>
                                <option value="2" selected>Jam Ke-2 (08.05 - 08.40)</option>
                                <option value="3">Jam Ke-3 (08.40 - 09.15)</option>
                                <option value="4">Jam Ke-4 (09.15 - 09.50)</option>
                                <option value="6">Jam Ke-6 (10.10 - 10.45)</option>
                                <option value="7">Jam Ke-7 (10.45 - 11.20)</option>
                            </select>
                        </div>
                    </div>

                    <div class="alert alert-light border small text-muted mb-0">
                        <i class="fas fa-info-circle text-primary mr-1"></i>
                        Sistem memiliki validasi otomatis untuk mencegah bentrok jadwal guru (mengajar di dua tempat bersamaan) maupun bentrok kelas (dua mapel di sesi jam yang sama).
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Plotting Mengajar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
