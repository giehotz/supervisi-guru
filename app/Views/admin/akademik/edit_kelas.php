<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Edit Kelas / Rombel</h1>
            <p class="text-muted small mb-0">Perbarui identitas kelas, tahun ajaran, atau wali kelas.</p>
        </div>
        <a href="<?= base_url('/admin/akademik/kelas') ?>" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Kelas
        </a>
    </div>

    <!-- Edit Form Card -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 mb-4">
                <div class="card-header py-3 bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit mr-1"></i> Formulir Perubahan Data Kelas
                    </h6>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('/admin/akademik/kelas/' . $kelas['id'] . '/update') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-gray-700">Nama Kelas / Rombel <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_kelas" 
                                   value="<?= esc($kelas['nama_kelas']) ?>" placeholder="Contoh: X IPA 1" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-gray-700">Tahun Ajaran & Semester <span class="text-danger">*</span></label>
                            <select class="form-control" name="tahun_ajar_id" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php foreach ($tahun_ajars as $tahun): ?>
                                    <option value="<?= $tahun['id'] ?>" <?= $kelas['tahun_ajar_id'] == $tahun['id'] ? 'selected' : '' ?>>
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
                                    <option value="<?= $guru['id'] ?>" <?= $kelas['wali_kelas'] == $guru['id'] ? 'selected' : '' ?>>
                                        <?= esc($guru['nama']) ?> <?= !empty($guru['nip']) ? '(' . esc($guru['nip']) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-gray-700">Status Operasional <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" required>
                                <option value="Aktif" <?= $kelas['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="Nonaktif" <?= $kelas['status'] === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="<?= base_url('/admin/akademik/kelas') ?>" class="btn btn-secondary mr-2">
                                <i class="fas fa-times mr-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary font-weight-bold text-white">
                                <i class="fas fa-save mr-1 text-white"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>