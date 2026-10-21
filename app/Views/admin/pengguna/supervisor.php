<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manajemen Pengguna</div>
            <h2 class="page-title text-dark fw-bold">Daftar Supervisor Penilai</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/pengguna/guru') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Pilih Guru sebagai Supervisor
            </a>
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

<!-- Supervisor Table Card -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title fw-bold">
            <i class="fas fa-user-shield text-info me-2"></i> Data Tim Supervisor
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-striped card-table" id="dataTable" width="100%">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Supervisor</th>
                    <th>Akun User</th>
                    <th>Email</th>
                    <th>Tanggal Penugasan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($supervisors as $supervisor): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm bg-warning-lt me-2 rounded-circle">
                                <i class="fas fa-user-tie"></i>
                            </span>
                            <div class="fw-bold text-dark"><?= esc($supervisor['nama_guru']) ?></div>
                        </div>
                    </td>
                    <td><code><?= esc($supervisor['username']) ?></code></td>
                    <td class="text-secondary"><?= esc($supervisor['email']) ?></td>
                    <td><?= !empty($supervisor['tanggal_penugasan']) ? date('d M Y', strtotime($supervisor['tanggal_penugasan'])) : '-' ?></td>
                    <td>
                        <?php if ($supervisor['status'] == 'Aktif'): ?>
                            <span class="badge bg-success-lt">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-danger-lt">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end text-nowrap">
                        <div class="btn-list justify-content-end">
                            <a href="<?= base_url('/admin/pengguna/supervisor/' . $supervisor['user_id'] . '/edit') ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= base_url('/admin/pengguna/' . $supervisor['user_id'] . '/delete') ?>" 
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Apakah Anda yakin ingin menghapus supervisor ini? Semua data terkait akan terhapus.')"
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
<?= $this->endSection(); ?>