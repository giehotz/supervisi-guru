<?php
$uri = service('uri');
$path = $uri->getPath();
$segments = explode('/', trim($path, '/'));
$segment2 = isset($segments[1]) ? $segments[1] : '';
$segment3 = isset($segments[2]) ? $segments[2] : '';
?>
<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand">
            <a href="<?= base_url('/kepala') ?>" class="d-flex align-items-center text-decoration-none text-reset">
                <?php if (session()->get('sidebar_logo')): ?>
                    <img src="<?= base_url('uploads/' . session()->get('sidebar_logo')) ?>" alt="Logo" class="navbar-brand-image me-2" style="max-height: 32px;">
                <?php else: ?>
                    <i class="fas fa-user-tie me-2 text-success fs-2"></i>
                <?php endif; ?>
                <span class="fw-bold">Sistem Supervisi</span>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item <?= ($segment2 == '' || $segment2 == 'dashboard') && $segment3 != 'performance' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/kepala') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-tachometer-alt"></i></span>
                        <span class="nav-link-title">Dashboard Utama</span>
                    </a>
                </li>

                <li class="nav-item <?= ($segment2 == 'dashboard' && $segment3 == 'performance') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/kepala/dashboard/performance') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-chart-line"></i></span>
                        <span class="nav-link-title">Dashboard Kinerja</span>
                    </a>
                </li>

                <li class="px-3 pt-3 pb-1 text-uppercase text-secondary small fw-bold">Supervisi</li>

                <li class="nav-item <?= $segment2 == 'jadwal' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/kepala/jadwal') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-calendar-alt"></i></span>
                        <span class="nav-link-title">Jadwal Supervisi</span>
                    </a>
                </li>
                <li class="nav-item <?= $segment2 == 'hasil' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/kepala/hasil') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-clipboard-list"></i></span>
                        <span class="nav-link-title">Penilaian & Hasil</span>
                    </a>
                </li>

                <li class="px-3 pt-3 pb-1 text-uppercase text-secondary small fw-bold">Laporan</li>

                <li class="nav-item <?= $segment2 == 'laporan' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/kepala/laporan') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-file-alt"></i></span>
                        <span class="nav-link-title">Laporan</span>
                    </a>
                </li>

                <li class="px-3 pt-3 pb-1 text-uppercase text-secondary small fw-bold">Pengaturan</li>

                <li class="nav-item <?= $segment2 == 'profile' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/kepala/profile/edit') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-user"></i></span>
                        <span class="nav-link-title">Profil Saya</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
