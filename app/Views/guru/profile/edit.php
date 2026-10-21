<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Pengaturan Akun</div>
            <h2 class="page-title text-dark fw-bold">Edit Profil</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/guru') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Notifikasi -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-exclamation-circle fs-2 me-2"></i></div>
            <div><?= esc(session()->getFlashdata('error')) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php $flashErrors = session()->getFlashdata('errors'); ?>
<?php if (!empty($flashErrors)): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-exclamation-triangle fs-2 me-2"></i></div>
            <div>
                <strong class="d-block mb-1">Periksa kembali kesalahan berikut:</strong>
                <ul class="mb-0 ps-3">
                    <?php foreach ((array) $flashErrors as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
        <div class="d-flex">
            <div><i class="fas fa-check-circle fs-2 me-2"></i></div>
            <div><?= esc(session()->getFlashdata('success')) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Formulir terpadu -->
<form action="<?= base_url('guru/profile/update') ?>" method="post" enctype="multipart/form-data" id="profileForm">
    <?= csrf_field() ?>

    <div class="row row-cards">
        <!-- Kolom kiri: identitas & petunjuk -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center p-4">
                    <div class="position-relative d-inline-block mb-3">
                        <?php $avatarSrc = !empty($user['foto_profil'])
                            ? base_url('uploads/profile/' . $user['foto_profil'])
                            : base_url('assets/img/undraw_profile.svg'); ?>
                        <img id="avatarPreview" src="<?= $avatarSrc ?>" alt="Foto Profil"
                             class="avatar avatar-xl rounded-circle shadow-sm border"
                             style="width: 130px; height: 130px; object-fit: cover;">
                        <label for="foto_profil" class="btn btn-primary btn-icon rounded-circle position-absolute shadow-sm"
                               style="bottom: 0; right: 0; width: 38px; height: 38px; cursor: pointer;"
                               title="Pilih Foto Baru">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" class="d-none" id="foto_profil" name="foto_profil"
                               accept="image/jpeg,image/png,image/jpg">
                    </div>

                    <h3 class="fw-bold text-dark mb-1"><?= esc($guru['nama'] ?? $user['username']) ?></h3>
                    <div class="text-secondary small mb-3"><?= esc($user['email']) ?></div>

                    <div class="mb-2">
                        <span class="badge bg-primary-lt px-3 py-2 fw-bold text-uppercase me-1">
                            <i class="fas fa-chalkboard-teacher me-1"></i> <?= esc($user['role'] ?? 'Guru') ?>
                        </span>
                        <?php if (!empty($guru['nip'])): ?>
                            <span class="badge bg-secondary-lt px-3 py-2 fw-semibold">NIP: <?= esc($guru['nip']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($guru['mata_pelajaran'])): ?>
                        <div class="mb-2">
                            <span class="badge bg-info-lt px-3 py-2 fw-semibold">
                                <i class="fas fa-book me-1"></i> <?= esc($guru['mata_pelajaran']) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="text-start bg-light rounded p-3 small text-secondary mt-3">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-camera text-primary me-2"></i>
                            <span>Klik ikon kamera untuk memilih foto baru.</span>
                        </div>
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-image text-info me-2"></i>
                            <span>Format JPG/PNG, ukuran maksimal 2 MB.</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-save text-success me-2"></i>
                            <span>Foto tersimpan bersama tombol <strong>Simpan Perubahan</strong>.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-transparent">
                    <h4 class="card-title fw-bold text-primary mb-0">
                        <i class="fas fa-lightbulb me-1"></i> Petunjuk
                    </h4>
                </div>
                <div class="card-body small text-secondary">
                    <ul class="ps-3 mb-0">
                        <li class="mb-1">Email harus unik dan belum digunakan pengguna lain.</li>
                        <li class="mb-1">Kata sandi baru minimal 8 karakter; isi kedua kolom untuk menggantinya.</li>
                        <li class="mb-1">Format nomor telepon: 08XXXXXXXXXX atau +628XXXXXXXXXX.</li>
                        <li class="mb-1">Foto profil maksimal 2 MB (JPG/PNG).</li>
                        <li>Kolom bertanda <span class="text-danger fw-bold">*</span> wajib diisi.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Kolom kanan: formulir -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title fw-bold mb-0">
                        <i class="fas fa-user-edit text-primary me-2"></i> Informasi Profil
                    </h3>
                    <span class="badge bg-secondary-lt">ID Akun: #<?= esc($user['id']) ?></span>
                </div>

                <div class="card-body p-4">
                    <!-- Informasi Akun -->
                    <h4 class="fw-bold text-dark border-bottom pb-2 mb-3">Informasi Akun</h4>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary"><i class="fas fa-lock"></i></span>
                                <input type="text" class="form-control bg-light" id="username" name="username"
                                       value="<?= esc($user['username']) ?>" readonly>
                            </div>
                            <small class="form-hint">Username bersifat permanen dan tidak dapat diubah.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="email">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text text-primary"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= old('email', $user['email']) ?>" required
                                       placeholder="nama@email.com">
                            </div>
                        </div>
                    </div>

                    <!-- Data Pribadi -->
                    <h4 class="fw-bold text-dark border-bottom pb-2 mb-3">Data Pribadi &amp; Kepegawaian</h4>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label required" for="nama">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text text-success"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="nama" name="nama"
                                       value="<?= old('nama', $guru['nama'] ?? '') ?>" required
                                       placeholder="Nama lengkap beserta gelar">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="telepon">Nomor Telepon / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text text-info"><i class="fas fa-phone-alt"></i></span>
                                <input type="text" class="form-control" id="telepon" name="telepon"
                                       value="<?= old('telepon', $user['telepon'] ?? '') ?>"
                                       placeholder="Contoh: 081234567890">
                            </div>
                            <small class="form-hint">Format: 08XXXXXXXXXX atau +628XXXXXXXXXX.</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="alamat">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2"
                                      placeholder="Alamat tempat tinggal..."><?= esc(old('alamat', $user['alamat'] ?? '')) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mata_pelajaran">Mata Pelajaran</label>
                            <div class="input-group">
                                <span class="input-group-text text-warning"><i class="fas fa-book-open"></i></span>
                                <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran"
                                       value="<?= old('mata_pelajaran', $guru['mata_pelajaran'] ?? '') ?>"
                                       placeholder="Contoh: Matematika">
                            </div>
                        </div>
                    </div>

                    <!-- Kata Sandi -->
                    <h4 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fas fa-key text-warning me-2"></i>Ganti Kata Sandi
                        <span class="fs-6 fw-normal text-secondary">(Opsional)</span>
                    </h4>

                    <div class="alert alert-light border small text-secondary mb-3">
                        <i class="fas fa-info-circle text-info me-1"></i>
                        Kosongkan kedua kolom di bawah jika Anda <strong>tidak ingin</strong> mengganti kata sandi lama.
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label" for="password">Kata Sandi Baru</label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Minimal 8 karakter" autocomplete="new-password">
                                <span class="input-group-text">
                                    <a href="javascript:void(0)" class="link-secondary btn-toggle-password"
                                       data-target="password" title="Lihat/Sembunyikan">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password_confirm">Ulangi Kata Sandi Baru</label>
                            <div class="input-group input-group-flat">
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                                       placeholder="Ulangi kata sandi baru" autocomplete="new-password">
                                <span class="input-group-text">
                                    <a href="javascript:void(0)" class="link-secondary btn-toggle-password"
                                       data-target="password_confirm" title="Lihat/Sembunyikan">
                                        <i class="far fa-eye"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <a href="<?= base_url('/guru') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
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
    // Live preview foto profil (maksimal 2 MB)
    var photoInput = document.getElementById('foto_profil');
    var avatarPreview = document.getElementById('avatarPreview');

    if (photoInput && avatarPreview) {
        photoInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Foto terlalu besar',
                        text: 'Ukuran berkas maksimal 2 MB. Silakan pilih foto lain.',
                        confirmButtonText: 'Mengerti'
                    });
                } else {
                    alert('Ukuran berkas foto melebihi batas maksimal 2 MB.');
                }
                this.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function(event) {
                avatarPreview.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Tampilkan/sembunyikan kata sandi
    document.querySelectorAll('.btn-toggle-password').forEach(function(button) {
        button.addEventListener('click', function() {
            var input = document.getElementById(this.getAttribute('data-target'));
            var icon = this.querySelector('i');
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
