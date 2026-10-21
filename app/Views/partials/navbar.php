<header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
    <div class="container-fluid">
        <!-- Sidebar Toggle (Sembunyikan / Tampilkan Sidebar Navigasi) -->
        <button class="btn btn-icon btn-ghost-secondary border-0 me-2" type="button" id="sidebarToggle" aria-label="Sembunyikan / Tampilkan Menu" title="Sembunyikan / Tampilkan Menu">
            <i class="fas fa-bars fs-3"></i>
        </button>
        <div class="navbar-nav flex-row order-md-last ms-auto">
            <!-- User Information Dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <?php if (!empty(session()->get('foto_profil'))): ?>
                        <span class="avatar avatar-sm" style="background-image: url('<?= base_url('uploads/profile/' . session()->get('foto_profil')) ?>')"></span>
                    <?php else: ?>
                        <span class="avatar avatar-sm bg-primary-lt text-uppercase font-weight-bold">
                            <?= esc(substr(session()->get('username') ?? 'U', 0, 2)) ?>
                        </span>
                    <?php endif; ?>
                    <div class="d-none d-xl-block ps-2">
                        <div><?= esc(session()->get('nama_lengkap') ?? session()->get('username') ?? 'User') ?></div>
                        <div class="mt-1 small text-secondary text-capitalize"><?= esc(session()->get('role') ?? 'Pengguna') ?></div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <?php
                        $role = strtolower(session()->get('role') ?? 'admin');
                        $profileUrl = base_url('/' . $role . '/profile/edit');
                    ?>
                    <a href="<?= $profileUrl ?>" class="dropdown-item">
                        <i class="fas fa-user-cog me-2 text-secondary"></i> Profil Saya
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                        <i class="fas fa-sign-out-alt me-2 text-danger"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
