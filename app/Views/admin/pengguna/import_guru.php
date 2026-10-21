<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manajemen Pengguna</div>
            <h2 class="page-title text-dark fw-bold">Import Data Guru Masal</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/pengguna/guru') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Guru
            </a>
        </div>
    </div>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-exclamation-circle fs-2 me-2"></i></div>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-exclamation-triangle fs-2 me-2"></i></div>
            <div>
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row row-cards mb-4">
    <!-- Upload Card -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-upload text-primary me-2"></i> Unggah Berkas Spreadsheet
                </h3>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/admin/pengguna/import-guru/process') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    
                    <div class="mb-4">
                        <label class="form-label required" for="excel_file">Pilih Dokumen Excel (.xlsx / .xls)</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
                        <small class="form-hint">Maksimal ukuran file 10MB.</small>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-import me-1"></i> Proses Import Data
                        </button>
                        <a href="<?= base_url('/admin/pengguna/import-guru/template') ?>" class="btn btn-outline-success">
                            <i class="fas fa-download me-1"></i> Unduh Format Template
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Petunjuk Format -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-info-circle text-info me-2"></i> Panduan Penyusunan Kolom
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-sm">
                        <thead>
                            <tr>
                                <th>Kolom</th>
                                <th>Nama Atribut</th>
                                <th>Ketentuan Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>A</code></td>
                                <td class="fw-bold">USERNAME</td>
                                <td class="small text-secondary">Unik, tanpa spasi</td>
                            </tr>
                            <tr>
                                <td><code>B</code></td>
                                <td class="fw-bold">PASSWORD</td>
                                <td class="small text-secondary">Minimal 6 karakter</td>
                            </tr>
                            <tr>
                                <td><code>C</code></td>
                                <td class="fw-bold">EMAIL</td>
                                <td class="small text-secondary">Format email valid</td>
                            </tr>
                            <tr>
                                <td><code>D</code></td>
                                <td class="fw-bold">ROLE</td>
                                <td class="small text-secondary">guru / supervisor / kepala / admin</td>
                            </tr>
                            <tr>
                                <td><code>E</code></td>
                                <td class="fw-bold">NAMA GURU</td>
                                <td class="small text-secondary">Nama lengkap beserta gelar</td>
                            </tr>
                            <tr>
                                <td><code>F</code></td>
                                <td class="fw-bold">NIP</td>
                                <td class="small text-secondary">18 digit / '-' jika honorer</td>
                            </tr>
                            <tr>
                                <td><code>G</code></td>
                                <td class="fw-bold">MATA PELAJARAN</td>
                                <td class="small text-secondary">Mata pelajaran utama</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>