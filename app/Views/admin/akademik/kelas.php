<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Manajemen Kelas & Rombel</h1>
            <p class="text-muted small mb-0">Kelola rombongan belajar (rombel), tahun ajaran, dan penetapan wali kelas.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalAddKelas">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Kelas Baru
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

    <!-- Metric Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Rombel / Kelas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalKelas ?> Kelas</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kelas Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalAktif ?> Kelas</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <form method="get" action="<?= base_url('admin/akademik/kelas') ?>">
                        <label class="text-xs font-weight-bold text-info text-uppercase mb-1">Filter Tahun Ajaran</label>
                        <div class="input-group input-group-sm">
                            <select class="form-control" name="tahun_ajar_id" onchange="this.form.submit()">
                                <option value="">Semua Tahun Ajaran</option>
                                <?php foreach ($tahun_ajars as $tahun): ?>
                                    <option value="<?= $tahun['id'] ?>" <?= $selectedTahunId == $tahun['id'] ? 'selected' : '' ?>>
                                        <?= esc($tahun['tahun_ajar']) ?> (<?= esc($tahun['semester']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($selectedTahunId): ?>
                                <div class="input-group-append">
                                    <a href="<?= base_url('admin/akademik/kelas') ?>" class="btn btn-outline-secondary" title="Reset Filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTales Card -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-school mr-1"></i> Daftar Rombongan Belajar (Kelas)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="20%">Nama Kelas</th>
                            <th width="25%">Tahun Ajaran & Semester</th>
                            <th width="25%">Wali Kelas</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($kelases as $kelas): ?>
                            <tr>
                                <td class="text-center align-middle font-weight-bold"><?= $no++ ?></td>
                                <td class="align-middle font-weight-bold text-gray-800">
                                    <i class="fas fa-chalkboard text-primary mr-1"></i> <?= esc($kelas['nama_kelas']) ?>
                                </td>
                                <td class="align-middle">
                                    <span class="badge badge-light border text-dark px-2 py-1">
                                        <?= esc($kelas['tahun_ajar'] ?? '-') ?> &bull; <?= esc($kelas['semester'] ?? '-') ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <?php if (!empty($kelas['nama_wali'])): ?>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-tie text-muted mr-2"></i>
                                            <span><?= esc($kelas['nama_wali']) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted font-italic small">Belum Ditentukan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if ($kelas['status'] == 'Aktif'): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-light border text-muted px-2 py-1">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('/admin/akademik/kelas/' . $kelas['id'] . '/edit') ?>" 
                                           class="btn btn-outline-primary"
                                           title="Edit Kelas">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('/admin/akademik/kelas/' . $kelas['id'] . '/delete') ?>" 
                                           class="btn btn-outline-danger"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus kelas <?= esc(addslashes($kelas['nama_kelas'])) ?>?')"
                                           title="Hapus Kelas">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalAddKelas" tabindex="-1" role="dialog" aria-labelledby="modalAddKelasTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('/admin/akademik/kelas/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalAddKelasTitle">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Kelas Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-gray-700">Nama Kelas / Rombel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kelas" placeholder="Contoh: VII A, X IPA 1, XII Keagamaan" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-gray-700">Tahun Ajaran & Semester <span class="text-danger">*</span></label>
                        <select class="form-control" name="tahun_ajar_id" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php foreach ($tahun_ajars as $tahun): ?>
                                <option value="<?= $tahun['id'] ?>" <?= ($tahun['status_aktif'] ?? '') === 'Aktif' ? 'selected' : '' ?>>
                                    <?= esc($tahun['tahun_ajar']) ?> - <?= esc($tahun['semester']) ?> <?= ($tahun['status_aktif'] ?? '') === 'Aktif' ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-gray-700">Wali Kelas <small class="text-muted">(Daftar Guru)</small></label>
                        <select class="form-control" name="wali_kelas">
                            <option value="">-- Belum Ditentukan --</option>
                            <?php foreach ($gurus as $guru): ?>
                                <option value="<?= $guru['id'] ?>">
                                    <?= esc($guru['nama']) ?> <?= !empty($guru['nip']) ? '(' . esc($guru['nip']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-gray-700">Status Operasional <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>