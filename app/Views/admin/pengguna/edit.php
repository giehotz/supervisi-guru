<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manajemen Pengguna</div>
            <h2 class="page-title text-dark fw-bold">Edit Akun Pengguna</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/pengguna') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-user-edit text-warning me-2"></i> Perbarui Data Akun: <?= esc($user['username']) ?>
                </h3>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/admin/pengguna/' . $user['id'] . '/update') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label required" for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= esc($user['username']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required" for="email">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= esc($user['email']) ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="password">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Biarkan kosong jika tidak ingin mengubah password">
                        <small class="form-hint">Isi hanya jika ingin mengganti kata sandi pengguna.</small>
                    </div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="role">Peran / Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrator</option>
                                <option value="kepala" <?= $user['role'] == 'kepala' ? 'selected' : '' ?>>Kepala Madrasah</option>
                                <option value="supervisor" <?= $user['role'] == 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                                <option value="guru" <?= $user['role'] == 'guru' ? 'selected' : '' ?>>Guru</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required" for="status">Status Akun</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Aktif" <?= $user['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="Nonaktif" <?= $user['status'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('/admin/pengguna') ?>" class="btn btn-link link-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>