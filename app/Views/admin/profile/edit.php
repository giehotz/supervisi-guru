<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Pengaturan Akun</div>
            <h2 class="page-title text-dark fw-bold">Pengaturan Profil Pengguna</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Alert Notifikasi -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-exclamation-circle fs-2 me-2"></i></div>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-check-circle fs-2 me-2"></i></div>
            <div><?= session()->getFlashdata('success') ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-exclamation-triangle fs-2 me-2"></i></div>
            <div>
                <strong class="d-block mb-1">Periksa kembali kesalahan berikut:</strong>
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

<!-- Formulir Terpadu -->
<form action="<?= base_url('admin/profile/update') ?>" method="post" enctype="multipart/form-data" id="profileForm">
    <?= csrf_field() ?>

    <div class="row row-cards mb-4">
        <!-- Kolom Kiri: Avatar & Kartu Identitas -->
        <div class="col-lg-4 col-xl-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center p-4">
                    <!-- Avatar Wrapper -->
                    <div class="position-relative d-inline-block mb-3">
                        <?php 
                            $avatarSrc = !empty($user['foto_profil']) 
                                ? base_url('uploads/profile/' . $user['foto_profil']) 
                                : base_url('assets/img/undraw_profile.svg');
                        ?>
                        <img id="avatarPreview" src="<?= $avatarSrc ?>" 
                             alt="Foto Profil" 
                             class="avatar avatar-xl rounded-circle shadow-sm border" 
                             style="width: 130px; height: 130px; object-fit: cover;">
                        
                        <!-- Tombol Ganti Foto (Icon Badge) -->
                        <label for="foto_profil" class="btn btn-primary btn-icon rounded-circle position-absolute shadow-sm" 
                               style="bottom: 0; right: 0; width: 38px; height: 38px; cursor: pointer;"
                               title="Pilih Foto Baru">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" class="d-none" id="foto_profil" name="foto_profil" accept="image/jpeg,image/png,image/jpg">
                    </div>

                    <h3 class="fw-bold text-dark mb-1"><?= esc($user['username']) ?></h3>
                    <div class="text-secondary small mb-3"><?= esc($user['email']) ?></div>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary-lt px-3 py-2 fw-bold text-uppercase">
                            <i class="fas fa-user-shield me-1"></i> <?= esc($user['role'] ?? 'Administrator') ?>
                        </span>
                    </div>

                    <div class="text-start bg-light rounded p-3 small text-secondary">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <span>Klik ikon kamera untuk memilih foto profil baru.</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check text-success me-2"></i>
                            <span>Mendukung JPG/PNG, ukuran maksimal 2 MB.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Bantuan / Keamanan -->
            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h4 class="card-title fw-bold text-primary mb-0">
                        <i class="fas fa-shield-alt me-1"></i> Keamanan Akun
                    </h4>
                </div>
                <div class="card-body small text-secondary">
                    <p class="mb-2"><strong>Tips menjaga akun Anda:</strong></p>
                    <ul class="ps-3 mb-0">
                        <li class="mb-1">Pastikan email selalu aktif untuk verifikasi sistem.</li>
                        <li class="mb-1">Gunakan kombinasi huruf, angka, dan simbol untuk kata sandi.</li>
                        <li>Jangan membagikan akun Administrator kepada pihak tidak berwenang.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Formulir Akun & Password -->
        <div class="col-lg-8 col-xl-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold">
                        <i class="fas fa-user-edit text-primary me-2"></i> Informasi Akun & Kontak
                    </h3>
                    <span class="badge bg-secondary-lt">ID Akun: #<?= esc($user['id']) ?></span>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Seksi 1: Informasi Dasar & Kontak -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="text" class="form-control bg-light" id="username" name="username" 
                                       value="<?= esc($user['username']) ?>" readonly>
                            </div>
                            <small class="form-hint">Username bersifat permanen dan tidak dapat diubah.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required" for="email">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text text-primary">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email', $user['email']) ?>" required placeholder="nama@email.com">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="telepon">Nomor Telepon / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text text-success">
                                    <i class="fas fa-phone-alt"></i>
                                </span>
                                <input type="text" class="form-control" id="telepon" name="telepon" 
                                       value="<?= old('telepon', $user['telepon'] ?? '') ?>" 
                                       placeholder="Contoh: 081234567890">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="alamat">Alamat Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text text-danger">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <textarea class="form-control" id="alamat" name="alamat" rows="1" 
                                          placeholder="Alamat tempat tinggal..."><?= old('alamat', $user['alamat'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Seksi 2: Keamanan & Kata Sandi -->
                    <h4 class="text-dark fw-bold border-bottom pb-2 mb-3">
                        <i class="fas fa-key text-warning me-2"></i> Ganti Kata Sandi (Opsional)
                    </h4>

                    <div class="alert alert-light border small text-secondary mb-3">
                        <i class="fas fa-info-circle text-info me-1"></i>
                        Kosongkan kedua kolom di bawah jika Anda <strong>tidak ingin</strong> mengganti kata sandi lama Anda.
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="password">Kata Sandi Baru</label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Minimal 8 karakter" autocomplete="new-password">
                                <span class="input-group-text">
                                    <a href="javascript:void(0)" class="link-secondary btn-toggle-password" data-target="password" title="Lihat/Sembunyikan">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="password_confirm">Konfirmasi Kata Sandi Baru</label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                                       placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                                <span class="input-group-text">
                                    <a href="javascript:void(0)" class="link-secondary btn-toggle-password" data-target="password_confirm" title="Lihat/Sembunyikan">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Footer Tombol Aksi -->
                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                    <a href="<?= base_url('/admin') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan Profil
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Live Avatar Preview saat file dipilih
    var photoInput = document.getElementById('foto_profil');
    var avatarPreview = document.getElementById('avatarPreview');

    if (photoInput && avatarPreview) {
        photoInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran berkas foto melebihi batas maksimal 2 MB.');
                    this.value = '';
                    return;
                }

                var reader = new FileReader();
                reader.onload = function(event) {
                    avatarPreview.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 2. Toggle Show/Hide Password
    document.querySelectorAll('.btn-toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');

            if (input) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>