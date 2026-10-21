<?php
$currUri = uri_string();
$isPengguna = (strpos($currUri, 'admin/pengguna') === 0);
$isAkademik = (strpos($currUri, 'admin/akademik') === 0);
$isInstrumen = (strpos($currUri, 'admin/instrumen') === 0);
?>
<aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand">
            <a href="<?= base_url('/admin') ?>" class="d-flex align-items-center text-decoration-none text-reset">
                <?php if (session()->get('sidebar_logo')): ?>
                    <img src="<?= base_url('uploads/' . session()->get('sidebar_logo')) ?>" alt="Logo" class="navbar-brand-image me-2" style="max-height: 32px;">
                <?php else: ?>
                    <i class="fas fa-graduation-cap me-2 text-primary fs-2"></i>
                <?php endif; ?>
                <span class="fw-bold">Sistem Supervisi</span>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
                <!-- Nav Item - Dashboard -->
                <li class="nav-item <?= ($currUri === 'admin' || $currUri === 'admin/dashboard' || $currUri === '') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/admin') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-tachometer-alt"></i></span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <!-- Section: Master Data & Instrumen -->
                <li class="px-3 pt-3 pb-1 text-uppercase text-secondary small fw-bold">Master Data & Instrumen</li>

                <!-- Nav Item - Manajemen Pengguna -->
                <li class="nav-item dropdown <?= $isPengguna ? 'active' : '' ?>">
                    <a class="nav-link dropdown-toggle <?= $isPengguna ? 'show' : '' ?>" href="#menuPengguna" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?= $isPengguna ? 'true' : 'false' ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-users"></i></span>
                        <span class="nav-link-title">Manajemen Pengguna</span>
                    </a>
                    <div class="dropdown-menu <?= $isPengguna ? 'show' : '' ?>">
                        <a class="dropdown-item <?= $currUri === 'admin/pengguna/guru' ? 'active' : '' ?>" href="<?= base_url('/admin/pengguna/guru') ?>">Guru</a>
                        <a class="dropdown-item <?= $currUri === 'admin/pengguna/supervisor' ? 'active' : '' ?>" href="<?= base_url('/admin/pengguna/supervisor') ?>">Supervisor</a>
                        <a class="dropdown-item <?= $currUri === 'admin/pengguna/semua' ? 'active' : '' ?>" href="<?= base_url('/admin/pengguna/semua') ?>">Semua Pengguna</a>
                    </div>
                </li>

                <!-- Nav Item - Data Akademik -->
                <li class="nav-item dropdown <?= $isAkademik ? 'active' : '' ?>">
                    <a class="nav-link dropdown-toggle <?= $isAkademik ? 'show' : '' ?>" href="#menuAkademik" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?= $isAkademik ? 'true' : 'false' ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-graduation-cap"></i></span>
                        <span class="nav-link-title">Data Akademik</span>
                    </a>
                    <div class="dropdown-menu <?= $isAkademik ? 'show' : '' ?>">
                        <a class="dropdown-item <?= strpos($currUri, 'admin/akademik/tahun-ajar') === 0 ? 'active' : '' ?>" href="<?= base_url('/admin/akademik/tahun-ajar') ?>">Tahun Ajaran</a>
                        <a class="dropdown-item <?= strpos($currUri, 'admin/akademik/mapel') === 0 ? 'active' : '' ?>" href="<?= base_url('/admin/akademik/mapel') ?>">Mata Pelajaran</a>
                        <a class="dropdown-item <?= strpos($currUri, 'admin/akademik/kelas') === 0 ? 'active' : '' ?>" href="<?= base_url('/admin/akademik/kelas') ?>">Kelas & Rombel</a>
                        <a class="dropdown-item <?= strpos($currUri, 'admin/akademik/mengajar') === 0 ? 'active' : '' ?>" href="<?= base_url('/admin/akademik/mengajar') ?>">Pembagian Mengajar</a>
                    </div>
                </li>

                <!-- Nav Item - Instrumen Supervisi -->
                <li class="nav-item dropdown <?= $isInstrumen ? 'active' : '' ?>">
                    <a class="nav-link dropdown-toggle <?= $isInstrumen ? 'show' : '' ?>" href="#menuInstrumen" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="<?= $isInstrumen ? 'true' : 'false' ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-tasks"></i></span>
                        <span class="nav-link-title">Instrumen Supervisi</span>
                    </a>
                    <div class="dropdown-menu <?= $isInstrumen ? 'show' : '' ?>">
                        <a class="dropdown-item <?= (strpos($currUri, 'admin/instrumen') === 0 && strpos($_SERVER['QUERY_STRING'] ?? '', 'tab=aspek') === false) ? 'active' : '' ?>" href="<?= base_url('/admin/instrumen?tab=jenis') ?>">Jenis Penilaian</a>
                        <a class="dropdown-item <?= (strpos($_SERVER['QUERY_STRING'] ?? '', 'tab=aspek') !== false) ? 'active' : '' ?>" href="<?= base_url('/admin/instrumen?tab=aspek') ?>">Aspek Penilaian</a>
                    </div>
                </li>

                <!-- Section: Pelaksanaan & Laporan -->
                <li class="px-3 pt-3 pb-1 text-uppercase text-secondary small fw-bold">Pelaksanaan & Laporan</li>

                <li class="nav-item <?= strpos($currUri, 'admin/jadwal') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/admin/jadwal') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-calendar-alt"></i></span>
                        <span class="nav-link-title">Jadwal Supervisi</span>
                    </a>
                </li>
                <li class="nav-item <?= strpos($currUri, 'admin/laporan/hasil-supervisi') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/admin/laporan/hasil-supervisi') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-clipboard-check"></i></span>
                        <span class="nav-link-title">Hasil Supervisi</span>
                    </a>
                </li>
                <li class="nav-item <?= ($currUri === 'admin/laporan' || ($currUri !== 'admin/laporan/hasil-supervisi' && $currUri !== 'admin/laporan/audit-log' && strpos($currUri, 'admin/laporan') === 0)) ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/admin/laporan') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-chart-bar"></i></span>
                        <span class="nav-link-title">Laporan & Rekapitulasi</span>
                    </a>
                </li>

                <!-- Section: Pengaturan & Sistem -->
                <li class="px-3 pt-3 pb-1 text-uppercase text-secondary small fw-bold">Pengaturan & Sistem</li>

                <li class="nav-item <?= strpos($currUri, 'admin/pengaturan') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/admin/pengaturan') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-cogs"></i></span>
                        <span class="nav-link-title">Pengaturan Sistem</span>
                    </a>
                </li>
                <li class="nav-item <?= strpos($currUri, 'admin/profile') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/admin/profile/edit') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-user-cog"></i></span>
                        <span class="nav-link-title">Profil Saya</span>
                    </a>
                </li>
                <li class="nav-item <?= strpos($currUri, 'admin/backup') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/admin/backup') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-database"></i></span>
                        <span class="nav-link-title">Backup & Restore</span>
                    </a>
                </li>
                <li class="nav-item <?= strpos($currUri, 'admin/laporan/audit-log') === 0 ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('/admin/laporan/audit-log') ?>">
                        <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="fas fa-history"></i></span>
                        <span class="nav-link-title">Audit & Log Sistem</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
