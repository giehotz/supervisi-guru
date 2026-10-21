<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<!-- Page header -->
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle text-secondary">Manajemen Pengguna</div>
            <h2 class="page-title text-dark fw-bold">Tambah Akun Pengguna Baru</h2>
        </div>
        <div class="col-auto ms-auto d-print-none">
            <a href="<?= base_url('/admin/pengguna') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Errors -->
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

        <!-- Form Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold">
                    <i class="fas fa-user-plus text-primary me-2"></i> Formulir Akun Pengguna
                </h3>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/admin/pengguna/store') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username unik" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required" for="email">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="contoh@madrasah.sch.id" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required" for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label required" for="role">Peran / Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">-- Pilih Peran Akun --</option>
                                <option value="admin">Administrator</option>
                                <option value="kepala">Kepala Madrasah</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="guru">Guru (Tenaga Pendidik)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fields Khusus Guru -->
                    <div id="guru-fields" class="card mt-4 border bg-light-subtle" style="display: none;">
                        <div class="card-header bg-transparent">
                            <h4 class="card-title fw-bold text-success mb-0">
                                <i class="fas fa-id-card me-2"></i> Data Profil Tenaga Pendidik (Guru)
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label" for="nama_lengkap">Nama Lengkap & Gelar</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="contoh: Drs. Ahmad Fauzi, M.Pd">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label" for="nip">NIP (Nomor Induk Pegawai)</label>
                                    <input type="text" class="form-control" id="nip" name="nip" placeholder="18 digit NIP / - jika honorer">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label" for="pangkat_golongan">Pangkat / Golongan</label>
                                    <input type="text" class="form-control" id="pangkat_golongan" name="pangkat_golongan" placeholder="contoh: Penata Tk. I / III/d">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label" for="mata_pelajaran">Mata Pelajaran yang Diampu</label>
                                    <input type="text" class="form-control" id="mata_pelajaran" name="mata_pelajaran" placeholder="contoh: Matematika / Fiqih">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label" for="status_kepegawaian">Status Kepegawaian</label>
                                    <select class="form-select" id="status_kepegawaian" name="status_kepegawaian">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="PNS">PNS</option>
                                        <option value="PPPK">PPPK</option>
                                        <option value="Honorer">Honorer</option>
                                    </select>
                                </div>
                                
                                <div class="col-12 mt-3">
                                    <label class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_supervisor" name="is_supervisor" value="1">
                                        <span class="form-check-label fw-bold">
                                            Jadikan sekaligus sebagai Supervisor penilai aktif
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= base_url('/admin/pengguna') ?>" class="btn btn-link link-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-1"></i> Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
document.getElementById('role').addEventListener('change', function() {
    var guruFields = document.getElementById('guru-fields');
    if (this.value === 'guru') {
        guruFields.style.display = 'block';
    } else {
        guruFields.style.display = 'none';
    }
});
</script>
<?= $this->endSection(); ?>