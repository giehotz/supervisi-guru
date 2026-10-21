<?php
$currUri = uri_string();
$guruModel = new \App\Models\GuruModel();
$isGuru = $guruModel->where('user_id', session()->get('id'))->countAllResults() > 0;
?>
<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand">
            <a href="<?= base_url('/supervisor') ?>" class="d-flex align-items-center text-decoration-none text-reset">
                <?php if (session()->get('sidebar_logo')): ?>
                    <img src="<?= base_url('uploads/' . session()->get('sidebar_logo')) ?>" alt="Logo" class="navbar-brand-image me-2" style="max-height: 32px;">
                <?php else: ?>
                    <i class="fas fa-user-shield me-2 text-warning fs-2"></i>
                <?php endif; ?>
                <span class="fw-bold">Sistem Supervisi</span>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item <?= ($currUri === 'supervisor' || $currUri === 'supervisor/dashboard' || $currUri === '') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/supervisor') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-tachometer-alt"></i></span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <?php if ($isGuru): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/guru') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-chalkboard-teacher text-info"></i></span>
                        <span class="nav-link-title">Dashboard Guru</span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="px-3 pt-3 pb-1 text-uppercase text-secondary small fw-bold">Supervisi</li>

                <li class="nav-item <?= strpos($currUri, 'supervisor/guru-binaan') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/supervisor/guru-binaan') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-users-cog text-primary"></i></span>
                        <span class="nav-link-title">Guru Binaan (Kurmer)</span>
                    </a>
                </li>
                <li class="nav-item <?= strpos($currUri, 'supervisor/jadwal') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/supervisor/jadwal') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-calendar-alt"></i></span>
                        <span class="nav-link-title">Jadwal Supervisi</span>
                    </a>
                </li>
                <li class="nav-item <?= strpos($currUri, 'supervisor/penilaian') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/supervisor/penilaian') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-clipboard-list"></i></span>
                        <span class="nav-link-title">Penilaian</span>
                    </a>
                </li>

                <li class="px-3 pt-3 pb-1 text-uppercase text-secondary small fw-bold">Laporan</li>

                <li class="nav-item <?= strpos($currUri, 'supervisor/hasil') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/supervisor/hasil') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-chart-bar"></i></span>
                        <span class="nav-link-title">Lihat Hasil</span>
                    </a>
                </li>

                <li class="px-3 pt-3 pb-1 text-uppercase text-secondary small fw-bold">Pengaturan</li>

                <li class="nav-item <?= strpos($currUri, 'supervisor/profile') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/supervisor/profile/edit') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-user"></i></span>
                        <span class="nav-link-title">Profil Saya</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
