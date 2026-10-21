<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manajemen Pengguna</div>
            <h2 class="page-title text-dark fw-bold">Edit Profil Guru: <?= esc($guru['nama'] ?? $user['username']) ?></h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/pengguna/guru') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Guru
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <div><i class="fas fa-exclamation-triangle fs-2 me-2"></i></div>
                    <div>
                        <ul class="mb-0 ps-3">
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= esc($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-chalkboard-teacher text-success me-2"></i> Formulir Perubahan Data Tenaga Pendidik
                </h3>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/admin/pengguna/guru/' . $user['id'] . '/update') ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="POST">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="nama">Nama Lengkap & Gelar</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama', $guru['nama'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="nip">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" value="<?= old('nip', $guru['nip'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="pangkat_golongan">Pangkat / Golongan</label>
                            <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan" value="<?= old('pangkat_golongan', $guru['pangkat_golongan'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="mata_pelajaran">Mata Pelajaran yang Diampu</label>
                            <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" value="<?= old('mata_pelajaran', $guru['mata_pelajaran'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label" for="status_kepegawaian">Status Kepegawaian</label>
                            <select class="form-select" id="status_kepegawaian" name="status_kepegawaian">
                                <option value="">-- Pilih Status --</option>
                                <option value="PNS" <?= old('status_kepegawaian', $guru['status_kepegawaian'] ?? '') == 'PNS' ? 'selected' : '' ?>>PNS</option>
                                <option value="PPPK" <?= old('status_kepegawaian', $guru['status_kepegawaian'] ?? '') == 'PPPK' ? 'selected' : '' ?>>PPPK</option>
                                <option value="Honorer" <?= old('status_kepegawaian', $guru['status_kepegawaian'] ?? '') == 'Honorer' ? 'selected' : '' ?>>Honorer</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required" for="status">Status Akun Login</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Aktif" <?= old('status', $user['status']) == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="Nonaktif" <?= old('status', $user['status']) == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="card p-3 border bg-light-subtle">
                                <label class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" id="is_supervisor" name="is_supervisor" value="1" <?= old('is_supervisor', $guru['is_supervisor'] ?? 0) ? 'checked' : '' ?>>
                                    <span class="form-check-label fw-bold">
                                        <?= ($guru['is_supervisor'] ?? 0) ? 'Guru ini terdaftar aktif sebagai Supervisor Penilai' : 'Berikan hak akses tambahan sebagai Supervisor Penilai' ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('/admin/pengguna/guru') ?>" class="btn btn-link link-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Simpan Pembaruan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>