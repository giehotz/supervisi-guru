<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Master Mata Pelajaran</h1>
            <p class="text-muted small mb-0">Kelola kurikulum dan daftar mata pelajaran madrasah yang diajarkan.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold px-3 text-white" data-toggle="modal" data-target="#modalAddMapel">
                <i class="fas fa-plus-circle mr-1 text-white"></i> Tambah Mata Pelajaran
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error'); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Metric Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Mata Pelajaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalMapel ?> Mapel</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book-open fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Mapel Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalAktif ?> Mapel</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kelompok Keagamaan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalKeagamaan ?> Mapel</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mosque fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kelompok Umum</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalUmum ?> Mapel</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-globe-asia fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list-ul mr-1"></i> Daftar Mata Pelajaran
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">Kode Mapel</th>
                            <th width="35%">Nama Mata Pelajaran</th>
                            <th width="20%">Kelompok</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($mapels as $mapel): ?>
                            <tr>
                                <td class="text-center align-middle font-weight-bold"><?= $no++ ?></td>
                                <td class="align-middle">
                                    <span class="badge badge-light border text-primary font-weight-bold px-2 py-1">
                                        <?= esc($mapel['kode_mapel'] ?: '-') ?>
                                    </span>
                                </td>
                                <td class="align-middle font-weight-bold text-gray-800">
                                    <?= esc($mapel['nama_mapel']) ?>
                                </td>
                                <td class="align-middle">
                                    <?php if ($mapel['kelompok'] === 'Keagamaan'): ?>
                                        <span class="badge badge-info px-2 py-1"><i class="fas fa-mosque mr-1"></i> Keagamaan</span>
                                    <?php elseif ($mapel['kelompok'] === 'Muatan Lokal'): ?>
                                        <span class="badge badge-secondary px-2 py-1"><i class="fas fa-map-pin mr-1"></i> Muatan Lokal</span>
                                    <?php elseif ($mapel['kelompok'] === 'Peminatan'): ?>
                                        <span class="badge badge-purple px-2 py-1" style="background:#6f42c1; color:#fff;"><i class="fas fa-star mr-1"></i> Peminatan</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-book mr-1"></i> Umum</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if ($mapel['status'] === 'Aktif'): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-light border text-muted px-2 py-1">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-edit-mapel"
                                                data-id="<?= $mapel['id'] ?>"
                                                data-kode="<?= esc($mapel['kode_mapel']) ?>"
                                                data-nama="<?= esc($mapel['nama_mapel']) ?>"
                                                data-kelompok="<?= esc($mapel['kelompok']) ?>"
                                                data-status="<?= esc($mapel['status']) ?>"
                                                title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('admin/akademik/mapel/' . $mapel['id'] . '/delete') ?>"
                                           class="btn btn-outline-danger"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran <?= esc(addslashes($mapel['nama_mapel'])) ?>?')"
                                           title="Hapus Mapel">
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

<!-- Modal Tambah Mapel -->
<div class="modal fade" id="modalAddMapel" tabindex="-1" role="dialog" aria-labelledby="modalAddMapelTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form action="<?= base_url('admin/akademik/mapel/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalAddMapelTitle">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Mata Pelajaran Baru
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-gray-700">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_mapel" placeholder="Contoh: Matematika Wajib" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-gray-700">Kode Mapel <small class="text-muted">(Opsional, otomatis digenerate jika kosong)</small></label>
                        <input type="text" class="form-control text-uppercase" name="kode_mapel" placeholder="Contoh: MATW" maxlength="10">
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-gray-700">Kelompok Kurikulum <span class="text-danger">*</span></label>
                        <select class="form-control" name="kelompok" required>
                            <option value="Umum">Umum</option>
                            <option value="Keagamaan">Keagamaan (Kemenag)</option>
                            <option value="Peminatan">Peminatan</option>
                            <option value="Muatan Lokal">Muatan Lokal</option>
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
                    <button type="submit" class="btn btn-primary font-weight-bold text-white">
                        <i class="fas fa-save mr-1 text-white"></i> Simpan Mata Pelajaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Mapel -->
<div class="modal fade" id="modalEditMapel" tabindex="-1" role="dialog" aria-labelledby="modalEditMapelTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <form id="formEditMapel" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalEditMapelTitle">
                        <i class="fas fa-edit mr-2"></i> Edit Mata Pelajaran
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-gray-700">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_mapel" name="nama_mapel" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-gray-700">Kode Mapel</label>
                        <input type="text" class="form-control text-uppercase" id="edit_kode_mapel" name="kode_mapel" maxlength="10">
                    </div>

                    <div class="form-group mb-3">
                        <label class="small font-weight-bold text-gray-700">Kelompok Kurikulum <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_kelompok" name="kelompok" required>
                            <option value="Umum">Umum</option>
                            <option value="Keagamaan">Keagamaan (Kemenag)</option>
                            <option value="Peminatan">Peminatan</option>
                            <option value="Muatan Lokal">Muatan Lokal</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-gray-700">Status Operasional <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_status" name="status" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary font-weight-bold text-white">
                        <i class="fas fa-save mr-1 text-white"></i> Perbarui Mata Pelajaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editButtons = document.querySelectorAll('.btn-edit-mapel');
        var formEdit = document.getElementById('formEditMapel');
        var inputNama = document.getElementById('edit_nama_mapel');
        var inputKode = document.getElementById('edit_kode_mapel');
        var selectKelompok = document.getElementById('edit_kelompok');
        var selectStatus = document.getElementById('edit_status');

        editButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var kode = this.getAttribute('data-kode');
                var nama = this.getAttribute('data-nama');
                var kelompok = this.getAttribute('data-kelompok');
                var status = this.getAttribute('data-status');

                formEdit.action = '<?= base_url('admin/akademik/mapel') ?>/' + id + '/update';
                inputNama.value = nama;
                inputKode.value = kode;
                selectKelompok.value = kelompok;
                selectStatus.value = status;

                if (window.jQuery) {
                    $('#modalEditMapel').modal('show');
                }
            });
        });
    });
</script>
<?= $this->endSection(); ?>
