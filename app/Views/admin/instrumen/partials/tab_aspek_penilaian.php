<!-- Card Aspek Penilaian -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h3 class="card-title">Daftar Aspek Penilaian</h3>
        <div class="d-flex align-items-center gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-primary btn dropdown-toggle" type="button" id="menuCepatJenis" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-layer-group me-1"></i> Jenis Penilaian
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="menuCepatJenis">
                    <span class="dropdown-header">Aksi Cepat:</span>
                    <a class="dropdown-item" href="<?= base_url('/admin/instrumen?tab=jenis') ?>" onclick="window.showTab('#jenis-tab'); return false;">
                        <i class="fas fa-list fa-fw me-2 text-primary"></i> Kelola Jenis Penilaian
                    </a>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="window.showTab('#jenis-tab'); setTimeout(function(){ if(window.bootstrap && bootstrap.Modal){ bootstrap.Modal.getOrCreateInstance('#createJenisModal').show(); } else { $('#createJenisModal').modal('show'); } }, 350);">
                        <i class="fas fa-plus fa-fw me-2 text-success"></i> Tambah Jenis Penilaian Baru
                    </a>
                    <?php if (!empty($jenis_penilaians)): ?>
                        <div class="dropdown-divider"></div>
                        <span class="dropdown-header">Filter / Lihat Jenis:</span>
                        <?php foreach ($jenis_penilaians as $jenis): ?>
                            <a class="dropdown-item small btn-filter-jenis" href="javascript:void(0)" data-jenis="<?= esc($jenis['nama_jenis'], 'attr') ?>" onclick="window.filterAspekByJenis ? filterAspekByJenis('<?= esc(addslashes($jenis['nama_jenis'])) ?>') : null">
                                <i class="fas fa-filter fa-fw me-2 text-muted"></i> <?= esc($jenis['nama_jenis']) ?>
                            </a>
                        <?php endforeach; ?>
                        <a class="dropdown-item small text-muted btn-filter-jenis" href="javascript:void(0)" data-jenis="" onclick="window.filterAspekByJenis ? filterAspekByJenis('') : null">
                            <i class="fas fa-undo fa-fw me-2"></i> Reset Filter Jenis
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <button class="btn btn-primary btn btn-pill" data-bs-toggle="modal" data-bs-target="#createAspekModal">
                <i class="fas fa-plus me-1"></i> Tambah Aspek Penilaian
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped" id="dataTableAspek">
            <thead>
                <tr>
                    <th class="w-1">No</th>
                    <th style="width: 25%;">Jenis Penilaian</th>
                    <th>Nama Aspek</th>
                    <th style="width: 10%;" class="text-center">Urutan</th>
                    <th class="w-1 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($aspek_penilaians as $aspek): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><span class="badge bg-blue-lt text-blue"><?= esc($aspek['nama_jenis'] ?? '') ?></span></td>
                    <td><?= esc($aspek['nama_aspek'] ?? '') ?></td>
                    <td class="text-center font-weight-bold">
                        <span class="badge bg-secondary-lt"><?= esc($aspek['urutan'] ?? '') ?></span>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap justify-content-center">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editAspekModal<?= $aspek['id'] ?? '' ?>">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAspekModal<?= $aspek['id'] ?? '' ?>">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editAspekModal<?= $aspek['id'] ?? '' ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <form action="<?= base_url('/admin/instrumen/aspek-penilaian/' . ($aspek['id'] ?? '') . '/update') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Aspek Penilaian</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label required">Jenis Penilaian</label>
                                                <select class="form-select" name="jenis_penilaian_id" required>
                                                    <option value="">Pilih Jenis Penilaian</option>
                                                    <?php foreach ($jenis_penilaians as $jenis): ?>
                                                        <option value="<?= $jenis['id'] ?>" <?= (isset($aspek['jenis_penilaian_id']) && $aspek['jenis_penilaian_id'] == $jenis['id']) ? 'selected' : '' ?>>
                                                            <?= esc($jenis['nama_jenis']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label required">Nama Aspek</label>
                                                <input type="text" class="form-control" name="nama_aspek" value="<?= esc($aspek['nama_aspek'] ?? '') ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label required">Urutan</label>
                                                <input type="number" class="form-control" name="urutan" value="<?= esc($aspek['urutan'] ?? '') ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteAspekModal<?= $aspek['id'] ?? '' ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="modal-status bg-danger"></div>
                                    <div class="modal-body text-center py-4">
                                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                        <h3>Konfirmasi Hapus</h3>
                                        <div class="text-secondary">Apakah Anda yakin ingin menghapus aspek penilaian "<strong><?= esc($aspek['nama_aspek'] ?? '') ?></strong>"?</div>
                                        <div class="text-danger small mt-2">Tindakan ini tidak dapat dibatalkan.</div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="w-100">
                                            <div class="row">
                                                <div class="col">
                                                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                                                </div>
                                                <div class="col">
                                                    <a href="<?= base_url('/admin/instrumen/aspek-penilaian/' . ($aspek['id'] ?? '') . '/delete') ?>" class="btn btn-danger w-100">Hapus</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createAspekModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url('/admin/instrumen/aspek-penilaian/create') ?>" method="post" id="createAspekForm">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Aspek Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label required mb-0">Jenis Penilaian</label>
                            <a href="javascript:void(0)" onclick="if(window.bootstrap && bootstrap.Modal){ bootstrap.Modal.getInstance(document.getElementById('createAspekModal'))?.hide(); } else { $('#createAspekModal').modal('hide'); } window.showTab('#jenis-tab');" class="small text-primary">
                                <i class="fas fa-external-link-alt me-1"></i> Kelola Jenis Penilaian
                            </a>
                        </div>
                        <select class="form-select" name="jenis_penilaian_id" id="jenisPenilaianCreate" required>
                            <option value="">Pilih Jenis Penilaian</option>
                            <?php foreach ($jenis_penilaians as $jenis): ?>
                                <option value="<?= $jenis['id'] ?>"><?= esc($jenis['nama_jenis']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Nama Aspek</label>
                        <input type="text" class="form-control" name="nama_aspek" required placeholder="Contoh: Kesesuaian RPP dengan Silabus">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Urutan</label>
                        <input type="number" class="form-control" name="urutan" id="urutanCreate" required>
                        <small class="form-hint">Nomor urut akan otomatis melanjutkan dari urutan terakhir</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.filterAspekByJenis = function(jenisName) {
    if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#dataTableAspek')) {
        $('#dataTableAspek').DataTable().column(1).search(jenisName ? '^' + $.fn.dataTable.util.escapeRegex(jenisName) + '$' : '', true, false).draw();
    }
};

if (window.jQuery) {
    $(document).off('click', '.btn-filter-jenis').on('click', '.btn-filter-jenis', function(e) {
        e.preventDefault();
        var jenis = $(this).attr('data-jenis');
        if (typeof window.filterAspekByJenis === 'function') {
            window.filterAspekByJenis(jenis);
        }
    });
}
</script>
