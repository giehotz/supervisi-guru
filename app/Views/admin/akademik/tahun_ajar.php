<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Manajemen Tahun Ajaran</h1>
            <p class="text-muted small mb-0">Kelola tahun ajaran, semester, dan penetapan tahun ajaran aktif untuk operasional madrasah.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold px-3 text-white" data-toggle="modal" data-target="#modalAddTahun">
                <i class="fas fa-plus-circle mr-1 text-white"></i> Tambah Tahun Ajaran
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

    <!-- Metric & Filter Cards -->
    <div class="row mb-4">
        <!-- Card Tahun Ajaran Aktif -->
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tahun Ajaran Aktif Saat Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php if (!empty($activeTahun)): ?>
                                    <?= esc($activeTahun['tahun_ajar']) ?> (<?= esc($activeTahun['semester']) ?>)
                                <?php else: ?>
                                    <span class="text-danger font-italic small">Belum ada TA aktif</span>
                                <?php endif; ?>
                            </div>
                            <small class="text-muted">Digunakan sebagai acuan default sistem</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Statistik -->
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Riwayat Tahun Ajaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalSemua ?> Periode</div>
                            <small class="text-muted"><?= $totalAktif ?> Aktif &bull; <?= $totalNonaktif ?> Nonaktif</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Filter Status -->
        <div class="col-xl-4 col-md-12 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <form method="get" action="<?= base_url('/admin/akademik/tahun-ajar') ?>">
                        <label class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <i class="fas fa-filter mr-1"></i> Filter Tampilan Data
                        </label>
                        <div class="input-group input-group-sm">
                            <select class="form-control" name="status" onchange="this.form.submit()">
                                <option value="Aktif" <?= $filterStatus === 'Aktif' ? 'selected' : '' ?>>Hanya Tahun Ajaran Aktif (Default)</option>
                                <option value="Semua" <?= $filterStatus === 'Semua' ? 'selected' : '' ?>>Semua Tahun Ajaran (Semua Status)</option>
                                <option value="Nonaktif" <?= $filterStatus === 'Nonaktif' ? 'selected' : '' ?>>Hanya Nonaktif</option>
                            </select>
                            <?php if ($filterStatus !== 'Aktif'): ?>
                                <div class="input-group-append">
                                    <a href="<?= base_url('/admin/akademik/tahun-ajar?status=Aktif') ?>" class="btn btn-outline-secondary" title="Reset ke Default (Hanya Aktif)">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            Mode saat ini: <strong><?= $filterStatus === 'Aktif' ? 'Hanya Tahun Ajaran Aktif' : ($filterStatus === 'Semua' ? 'Semua Riwayat' : 'Hanya Nonaktif') ?></strong>
                        </small>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTales Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-1"></i> Daftar Data Tahun Ajaran
            </h6>
            <span class="badge badge-secondary px-2 py-1">
                Menampilkan: <?= count($tahun_ajars) ?> Data
            </span>
        </div>
        <div class="card-body">
            <?php if (empty($tahun_ajars)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-calendar-times fa-3x mb-3 text-gray-300"></i>
                    <p class="mb-1 font-weight-bold">Tidak ada data tahun ajaran untuk filter "<?= esc($filterStatus) ?>".</p>
                    <a href="<?= base_url('/admin/akademik/tahun-ajar?status=Semua') ?>" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="fas fa-eye mr-1"></i> Tampilkan Semua Tahun Ajaran
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Tahun Ajaran</th>
                                <th width="20%">Semester</th>
                                <th width="15%" class="text-center">Status</th>
                                <th width="25%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($tahun_ajars as $tahun): ?>
                            <tr>
                                <td class="text-center align-middle font-weight-bold"><?= $no++ ?></td>
                                <td class="align-middle">
                                    <strong><?= esc($tahun['tahun_ajar']) ?></strong>
                                    <?php if ($tahun['status_aktif'] == 'Aktif'): ?>
                                        <span class="badge badge-success ml-2"><i class="fas fa-check-circle mr-1"></i> Sedang Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-light border text-dark px-2 py-1">
                                        Semester <?= esc($tahun['semester']) ?>
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if ($tahun['status_aktif'] == 'Aktif'): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <?php if ($tahun['status_aktif'] != 'Aktif'): ?>
                                            <a href="<?= base_url('/admin/akademik/tahun-ajar/' . $tahun['id'] . '/activate') ?>" 
                                               class="btn btn-success"
                                               onclick="return confirm('Aktifkan tahun ajaran <?= esc($tahun['tahun_ajar']) ?> (<?= esc($tahun['semester']) ?>)? Tahun ajaran aktif lainnya akan dinonaktifkan otomatis.')"
                                               title="Aktifkan Periode Ini">
                                                <i class="fas fa-power-off mr-1"></i> Aktifkan
                                            </a>
                                        <?php endif; ?>

                                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal<?= $tahun['id'] ?>" title="Edit Data">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <?php if ($tahun['status_aktif'] != 'Aktif'): ?>
                                            <a href="<?= base_url('/admin/akademik/tahun-ajar/' . $tahun['id'] . '/delete') ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus tahun ajaran <?= esc($tahun['tahun_ajar']) ?> (<?= esc($tahun['semester']) ?>)? Data yang berelasi dengan kelas atau pembagian mengajar tidak dapat dihapus.')"
                                               title="Hapus Data">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $tahun['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $tahun['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content text-left">
                                                <form action="<?= base_url('/admin/akademik/tahun-ajar/' . $tahun['id'] . '/update') ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-header">
                                                        <h5 class="modal-title font-weight-bold" id="editModalLabel<?= $tahun['id'] ?>">Edit Tahun Ajaran</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="tahun_ajar" value="<?= esc($tahun['tahun_ajar']) ?>" placeholder="Contoh: 2025/2026" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Semester <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="semester" required>
                                                                <option value="Ganjil" <?= $tahun['semester'] == 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
                                                                <option value="Genap" <?= $tahun['semester'] == 'Genap' ? 'selected' : '' ?>>Genap</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                                            <select class="form-control" name="status_aktif" required>
                                                                <option value="Aktif" <?= $tahun['status_aktif'] == 'Aktif' ? 'selected' : '' ?>>Aktif (Jadikan TA Aktif)</option>
                                                                <option value="Nonaktif" <?= $tahun['status_aktif'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                                            </select>
                                                            <small class="form-text text-muted">Jika memilih "Aktif", tahun ajaran aktif lainnya akan otomatis dinonaktifkan.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary font-weight-bold text-white"><i class="fas fa-save mr-1 text-white"></i> Simpan Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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

</div>

<!-- Modal Tambah Tahun Ajaran -->
<div class="modal fade" id="modalAddTahun" tabindex="-1" role="dialog" aria-labelledby="modalAddTahunLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('/admin/akademik/tahun-ajar/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalAddTahunLabel">
                        <i class="fas fa-plus-circle mr-1 text-primary"></i> Tambah Tahun Ajaran Baru
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tahun_ajar" placeholder="Contoh: 2025/2026" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Semester <span class="text-danger">*</span></label>
                        <select class="form-control" name="semester" required>
                            <option value="">-- Pilih Semester --</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Status Awal <span class="text-danger">*</span></label>
                        <select class="form-control" name="status_aktif" required>
                            <option value="Aktif">Aktif (Langsung jadikan TA Aktif)</option>
                            <option value="Nonaktif" selected>Nonaktif</option>
                        </select>
                        <small class="form-text text-muted">Hanya boleh ada 1 tahun ajaran berstatus Aktif di sistem.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold text-white"><i class="fas fa-check mr-1 text-white"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>