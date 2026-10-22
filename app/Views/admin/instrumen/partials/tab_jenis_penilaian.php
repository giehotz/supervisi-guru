<!-- Card Jenis Penilaian -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Daftar Jenis Penilaian</h3>
        <button class="btn btn-primary btn-pill btn" data-bs-toggle="modal" data-bs-target="#createJenisModal">
            <i class="fas fa-plus me-1"></i> Tambah Jenis Penilaian
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped" id="dataTableJenis">
            <thead>
                <tr>
                    <th class="w-1">No</th>
                    <th>Nama Jenis</th>
                    <th style="width: 15%;">Skor Maksimal</th>
                    <th style="width: 30%;">Kategori Skor</th>
                    <th class="w-1 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($jenis_penilaians as $jenis): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= esc($jenis['nama_jenis'] ?? '') ?></strong></td>
                    <td><span class="badge bg-blue-lt"><?= esc($jenis['skor_maksimal'] ?? '') ?></span></td>
                    <td>
                        <?php 
                        if (isset($jenis['kategori_skor'])) {
                            $kategori = json_decode($jenis['kategori_skor'], true);
                            if (is_array($kategori)) {
                                echo '<ul class="mb-0 ps-3 small text-secondary">';
                                foreach ($kategori as $range => $label) {
                                    echo '<li><strong>' . esc($range) . '</strong>: ' . esc($label) . '</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo esc($jenis['kategori_skor']);
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap justify-content-center">
                            <button class="btn btn-warning btn-pill" data-bs-toggle="modal" data-bs-target="#editJenisModal<?= $jenis['id'] ?? '' ?>">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-pill" data-bs-toggle="modal" data-bs-target="#deleteJenisModal<?= $jenis['id'] ?? '' ?>">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editJenisModal<?= $jenis['id'] ?? '' ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <form action="<?= base_url('/admin/instrumen/jenis-penilaian/' . ($jenis['id'] ?? '') . '/update') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Jenis Penilaian</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label required">Nama Jenis</label>
                                                <input type="text" class="form-control" name="nama_jenis" value="<?= esc($jenis['nama_jenis'] ?? '') ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label required">Skor Maksimal</label>
                                                <input type="number" class="form-control" name="skor_maksimal" value="<?= esc($jenis['skor_maksimal'] ?? '') ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kategori Skor (JSON)</label>
                                                <textarea class="form-control font-monospace" name="kategori_skor" rows="4"><?= esc($jenis['kategori_skor'] ?? '') ?></textarea>
                                                <small class="form-hint">Format: {"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}</small>
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
                        <div class="modal fade" id="deleteJenisModal<?= $jenis['id'] ?? '' ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="modal-status bg-danger"></div>
                                    <div class="modal-body text-center py-4">
                                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                        <h3>Konfirmasi Hapus</h3>
                                        <div class="text-secondary">Apakah Anda yakin ingin menghapus jenis penilaian "<strong><?= esc($jenis['nama_jenis'] ?? '') ?></strong>"?</div>
                                        <div class="text-danger small mt-2">Tindakan ini tidak dapat dibatalkan.</div>
                                    </div>
                                    <div class="modal-footer">
                                        <div class="w-100">
                                            <div class="row">
                                                <div class="col">
                                                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Batal</button>
                                                </div>
                                                <div class="col">
                                                    <a href="<?= base_url('/admin/instrumen/jenis-penilaian/' . ($jenis['id'] ?? '') . '/delete') ?>" class="btn btn-danger w-100">Hapus</a>
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
<div class="modal fade" id="createJenisModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url('/admin/instrumen/jenis-penilaian/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jenis Penilaian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required">Nama Jenis</label>
                        <input type="text" class="form-control" name="nama_jenis" required placeholder="Contoh: Supervisi Administrasi Guru">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Skor Maksimal</label>
                        <input type="number" class="form-control" name="skor_maksimal" value="48" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori Skor (JSON)</label>
                        <textarea class="form-control font-monospace" name="kategori_skor" rows="4">{"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}</textarea>
                        <small class="form-hint">Format: {"86-100":"Baik Sekali","76-85":"Baik","61-75":"Cukup","0-60":"Kurang"}</small>
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
