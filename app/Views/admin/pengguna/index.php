<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manajemen Pengguna</div>
            <h2 class="page-title text-dark fw-bold">Daftar Semua Pengguna</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="<?= base_url('/admin/pengguna/sync-guru-data') ?>" class="btn btn-outline-info"
                    onclick="return confirm('Apakah Anda yakin ingin menyinkronkan data guru (username dan NIP)?')">
                    <i class="fas fa-sync me-1"></i> Sinkron Data Guru
                </a>
                <a href="<?= base_url('/admin/pengguna/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Pengguna
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-check-circle fs-2 me-2"></i></div>
            <div><?= session()->getFlashdata('success') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-exclamation-circle fs-2 me-2"></i></div>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Users Card & Table -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title fw-bold">
            <i class="fas fa-users text-primary me-2"></i> Data Akun Pengguna
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-striped card-table" id="dataTable" width="100%">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>NIP</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Terakhir Login</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm bg-primary-lt me-2 rounded-circle">
                                    <i class="fas fa-user"></i>
                                </span>
                                <div class="fw-bold text-dark"><?= esc($user['username']) ?></div>
                            </div>
                        </td>
                        <td class="text-secondary"><?= esc($user['email']) ?></td>
                        <td><code><?= esc($user['nip'] ?? '-') ?></code></td>
                        <td>
                            <?php 
                            $badge = [
                                'admin' => 'bg-dark-lt text-dark',
                                'kepala' => 'bg-primary-lt text-primary',
                                'supervisor' => 'bg-warning-lt text-warning',
                                'guru' => 'bg-info-lt text-info',
                            ];
                            $role = strtolower($user['role']); ?>
                            <span class="badge <?= $badge[$role] ?? 'bg-secondary-lt' ?> text-capitalize"><?= esc($role) ?></span>
                        </td>
                        <td>
                            <?php if (strtolower($user['status']) == 'aktif'): ?>
                                <span class="badge bg-success-lt">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger-lt">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-secondary small">
                            <?= !empty($user['last_login']) ? date('d M Y, H:i', strtotime($user['last_login'])) : '-' ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <div class="btn-list justify-content-end">
                                <a href="<?= base_url('/admin/pengguna/' . $user['id'] . '/edit') ?>" class="btn btn-pill btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-pill btn-outline-info btn-reset-password"
                                    data-id="<?= $user['id'] ?>"
                                    data-username="<?= esc($user['username']) ?>"
                                    title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </button>
                                <a href="<?= base_url('/admin/pengguna/' . $user['id'] . '/toggle-status') ?>"
                                    class="btn btn-pill btn-outline-<?= (strtolower($user['status']) == 'aktif') ? 'secondary' : 'success' ?>"
                                    onclick="return confirm('Apakah Anda yakin ingin mengubah status pengguna ini?')"
                                    title="<?= (strtolower($user['status']) == 'aktif') ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                    <i class="fas fa-power-off"></i>
                                </a>
                                <a href="<?= base_url('/admin/pengguna/' . $user['id'] . '/delete') ?>"
                                    class="btn btn-pill btn-outline-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"
                                    title="Hapus">
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

<!-- Modal Reset Password (BS5 / Tabler) -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-primary"></div>
            <form id="resetPasswordForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="resetPasswordModalLabel">
                        <i class="fas fa-key text-primary me-2"></i>Reset Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-secondary mb-3">Reset password untuk pengguna: <strong class="text-dark" id="resetUsername"></strong></p>
                    <div class="mb-3">
                        <label class="form-label" for="new_password">Password Baru</label>
                        <input type="text" class="form-control" id="new_password" name="password" value="12345678" required minlength="8">
                        <small class="form-hint">Default: <code>12345678</code> (minimal 8 karakter).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary ms-auto">Simpan Password Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-reset-password', function() {
        var id = $(this).data('id');
        var username = $(this).data('username');
        $('#resetUsername').text(username);
        $('#new_password').val('12345678');
        $('#resetPasswordForm').attr('action', '<?= base_url('/admin/pengguna') ?>/' + id + '/reset-password');
        
        var myModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        myModal.show();
    });
});
</script>
<?= $this->endSection(); ?>