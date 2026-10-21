<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manajemen Pengguna</div>
            <h2 class="page-title text-dark fw-bold">Laporan Hasil Import Data Guru</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
                <a href="<?= base_url('/admin/pengguna/guru') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-1"></i> Lihat Daftar Guru
                </a>
                <a href="<?= base_url('/admin/pengguna/import-guru') ?>" class="btn btn-primary">
                    <i class="fas fa-redo me-1"></i> Import Berkas Lain
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Summary Metric Cards -->
<div class="row row-cards mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-green-lt avatar rounded">
                            <i class="fas fa-check-circle fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-secondary text-uppercase fw-bold small">Berhasil Diimport</div>
                        <div class="h2 mb-0 fw-bold text-success"><?= $successCount ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-danger-lt avatar rounded">
                            <i class="fas fa-exclamation-circle fs-2"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="text-secondary text-uppercase fw-bold small">Gagal / Dilewati</div>
                        <div class="h2 mb-0 fw-bold text-danger"><?= $errorCount ?? 0 ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Table Card -->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h3 class="card-title fw-bold">
            <i class="fas fa-clipboard-list text-primary me-2"></i> Rincian Baris Data Excel
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter table-striped card-table" id="dataTable" width="100%">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Status</th>
                    <th>User ID</th>
                    <th>Guru ID</th>
                    <th>Keterangan / Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result): ?>
                <tr>
                    <td class="fw-bold"><code><?= esc($result['username']) ?></code></td>
                    <td>
                        <?php if (strtolower($result['status']) === 'success' || strtolower($result['status']) === 'berhasil'): ?>
                            <span class="badge bg-success-lt">Sukses</span>
                        <?php else: ?>
                            <span class="badge bg-danger-lt"><?= esc($result['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($result['user_id'] ?? '-') ?></td>
                    <td><?= esc($result['guru_id'] ?? '-') ?></td>
                    <td class="text-secondary small"><?= esc($result['keterangan']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>