<!doctype html>
<html lang="id">
<head>
    <title><?= $title ?? 'Admin Dashboard - Sistem Supervisi' ?></title>
    <?= $this->include('partials/head') ?>
</head>
<body class="layout-fluid">
    <div class="page">
        <!-- Sidebar Menu Khusus Admin -->
        <?= $this->include('partials/sidebar/admin') ?>

        <!-- Topbar Navbar -->
        <?= $this->include('partials/navbar') ?>

        <div class="page-wrapper">
            <!-- Konten Halaman Dinamis -->
            <div class="page-body">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>

            <!-- Footer -->
            <?= $this->include('partials/footer') ?>
        </div>
    </div>

    <!-- Skrip & Plugins -->
    <?= $this->include('partials/scripts') ?>
</body>
</html>