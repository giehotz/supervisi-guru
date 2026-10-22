<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manajemen Pengguna</div>
            <h2 class="page-title text-dark fw-bold">Daftar Tenaga Pendidik (Guru)</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="<?= base_url('/admin/pengguna/import-guru') ?>" class="btn btn-outline-success">
                    <i class="fas fa-file-excel me-1"></i> Import dari Excel
                </a>
                <a href="<?= base_url('/admin/pengguna/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Tambah Guru
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

<!-- Data Table Card -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title fw-bold">
            <i class="fas fa-chalkboard-teacher text-success me-2"></i> Data Guru Terdaftar
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-striped card-table" id="dataTable" width="100%">
            <thead>
                <tr>
                    <th>Nama & NIP</th>
                    <th>Akun User</th>
                    <th>Pangkat/Golongan</th>
                    <th>Mata Pelajaran</th>
                    <th>Kepegawaian</th>
                    <th>Peran Supervisi</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gurus as $guru): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><?= esc($guru['nama']) ?></div>
                            <div class="small text-secondary font-monospace"><?= esc($guru['nip'] ?? '-') ?></div>
                        </td>
                        <td>
                            <div class="small fw-bold"><?= esc($guru['username'] ?? '-') ?></div>
                            <div class="small text-secondary"><?= esc($guru['email'] ?? '-') ?></div>
                        </td>
                        <td><span class="badge bg-secondary-lt"><?= esc($guru['pangkat_golongan'] ?? '-') ?></span></td>
                        <td><?= esc($guru['mata_pelajaran'] ?? '-') ?></td>
                        <td>
                            <span class="badge bg-blue-lt"><?= esc($guru['status_kepegawaian'] ?? '-') ?></span>
                        </td>
                        <td>
                            <?php if (isset($guru['is_supervisor']) && $guru['is_supervisor'] == 1): ?>
                                <span class="badge bg-warning-lt fw-bold"><i class="fas fa-user-shield me-1"></i> Supervisor</span>
                            <?php else: ?>
                                <span class="badge bg-info-lt">Guru</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end text-nowrap">
                            <div class="btn-list justify-content-end">
                                <?php if (!empty($guru['account_id'])): ?>
                                    <a href="<?= base_url('/admin/pengguna/guru/' . $guru['account_id'] . '/edit') ?>" class="btn btn-pill btn-outline-warning" title="Edit Data Guru">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if (isset($guru['is_supervisor']) && $guru['is_supervisor'] == 1): ?>
                                        <a href="<?= base_url('/admin/guru/' . $guru['id'] . '/toggle-supervisor') ?>" class="btn btn-pill btn-outline-info" title="Turunkan sebagai Guru">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('/admin/pengguna/guru/' . $guru['id'] . '/make-supervisor') ?>" class="btn btn-pill btn-outline-success" title="Angkat sebagai Supervisor">
                                            <i class="fas fa-arrow-up"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= base_url('/admin/pengguna/' . $guru['account_id'] . '/delete') ?>"
                                        class="btn btn-pill btn-outline-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus guru ini? Semua data terkait akan terhapus.')"
                                        title="Hapus Akun & Data Guru">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-danger-lt me-1">User Hilang</span>
                                    <a href="<?= base_url('/admin/guru/' . $guru['id'] . '/delete') ?>"
                                        class="btn btn-pill btn-outline-danger"
                                        onclick="return confirm('Data user tidak ditemukan. Apakah Anda yakin ingin menghapus data profil guru ini?')"
                                        title="Hapus Data Guru Saja">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>