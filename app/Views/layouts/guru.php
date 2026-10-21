<!doctype html>
<html lang="id">
<head>
    <title><?= $title ?? 'Guru Dashboard - Sistem Supervisi' ?></title>
    <?= $this->include('partials/head') ?>
</head>
<body class="layout-fluid">
    <div class="page">
        <!-- Sidebar Menu Khusus Guru -->
        <?= $this->include('partials/sidebar/guru') ?>

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