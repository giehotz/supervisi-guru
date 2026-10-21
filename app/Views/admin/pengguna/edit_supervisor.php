<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manajemen Pengguna</div>
            <h2 class="page-title text-dark fw-bold">Edit Data Supervisor: <?= esc($user['username']) ?></h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/pengguna/supervisor') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Supervisor
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-user-shield text-info me-2"></i> Formulir Pembaruan Penugasan Supervisor
                </h3>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/admin/pengguna/supervisor/' . $user['id'] . '/update') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <!-- Akun Pengguna -->
                        <div class="col-md-6">
                            <h4 class="text-secondary fw-bold mb-3 border-bottom pb-2">Informasi Akun</h4>
                            
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
                                <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti">
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label required" for="role">Role Akun</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        <option value="kepala" <?= $user['role'] == 'kepala' ? 'selected' : '' ?>>Kepala</option>
                                        <option value="supervisor" <?= $user['role'] == 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                                        <option value="guru" <?= $user['role'] == 'guru' ? 'selected' : '' ?>>Guru</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label required" for="status">Status Akun</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Aktif" <?= $user['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                        <option value="Nonaktif" <?= $user['status'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Data Penugasan Supervisor -->
                        <div class="col-md-6">
                            <h4 class="text-secondary fw-bold mb-3 border-bottom pb-2">Detail Penugasan</h4>
                            
                            <div class="mb-3">
                                <label class="form-label required" for="tanggal_penugasan">Tanggal Surat Tugas</label>
                                <input type="date" class="form-control" id="tanggal_penugasan" name="tanggal_penugasan" 
                                       value="<?= esc($supervisor['tanggal_penugasan'] ?? '') ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label required" for="supervisor_status">Status Penugasan</label>
                                <select class="form-select" id="supervisor_status" name="supervisor_status" required>
                                    <option value="Aktif" <?= isset($supervisor['status']) && $supervisor['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif Melakukan Supervisi</option>
                                    <option value="Nonaktif" <?= isset($supervisor['status']) && $supervisor['status'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif / Selesai Tugas</option>
                                </select>
                            </div>

                            <div class="alert alert-info py-2 mt-4">
                                <i class="fas fa-info-circle me-1"></i>
                                Penugasan supervisor aktif memungkinkan guru terkait mengakses dashboard penilai supervisi.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                        <a href="<?= base_url('/admin/pengguna/supervisor') ?>" class="btn btn-link link-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Simpan Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>