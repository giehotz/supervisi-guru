<?php
$sigModel = new \App\Models\DokumenSignatureModel();
$userModel = new \App\Models\UserModel();
$guruModel = new \App\Models\GuruModel();

$jadwalId = $schedule['id'] ?? 0;
$signatures = $sigModel->getSignaturesByJadwal($jadwalId);
$currentRole = session()->get('role');
$currentUserId = session()->get('id');

$canSignCheck = $sigModel->canSign($jadwalId, $currentRole);
$isSignedByMe = !empty($signatures[$currentRole]);
$isFullySigned = !empty($signatures['kepala']);
$hasAnySignatures = !empty($signatures['guru']) || !empty($signatures['supervisor']) || !empty($signatures['kepala']);

// Resolusi aman data Guru
$namaGuru = $signatures['guru']['signer_nama'] ?? ($schedule['nama_guru'] ?? null);
$nipGuru  = $signatures['guru']['signer_nip'] ?? ($schedule['nip_guru'] ?? ($schedule['nip'] ?? null));
if (empty($namaGuru) && !empty($schedule['guru_id'])) {
    $gObj = $guruModel->find($schedule['guru_id']);
    if ($gObj) {
        $namaGuru = $gObj['nama'] ?? 'Guru';
        $nipGuru  = $nipGuru ?: ($gObj['nip'] ?? '-');
    }
}
$namaGuru = $namaGuru ?: 'Guru';
$nipGuru  = $nipGuru ?: '-';

// Resolusi aman data Supervisor
$namaSupervisor = $signatures['supervisor']['signer_nama'] ?? ($schedule['nama_supervisor'] ?? ($schedule['supervisor_name'] ?? null));
$nipSupervisor  = $signatures['supervisor']['signer_nip'] ?? ($schedule['nip_supervisor'] ?? null);
if ((empty($namaSupervisor) || empty($nipSupervisor)) && !empty($schedule['supervisor_id'])) {
    $sObj = $userModel->find($schedule['supervisor_id']);
    if ($sObj) {
        $namaSupervisor = $namaSupervisor ?: ($sObj['username'] ?? 'Supervisor');
        $nipSupervisor  = $nipSupervisor ?: ($sObj['nip'] ?? '-');
    }
}
$namaSupervisor = $namaSupervisor ?: 'Supervisor';
$nipSupervisor  = $nipSupervisor ?: '-';

// Resolusi aman data Kepala Madrasah
$namaKepala = $signatures['kepala']['signer_nama'] ?? ($schedule['nama_kepala'] ?? null);
$nipKepala  = $signatures['kepala']['signer_nip'] ?? ($schedule['nip_kepala'] ?? null);
if (empty($namaKepala) || empty($nipKepala)) {
    $kObj = $userModel->where('role', 'kepala')->first();
    if ($kObj) {
        $namaKepala = $namaKepala ?: ($kObj['username'] ?? 'Kepala Madrasah');
        $nipKepala  = $nipKepala ?: ($kObj['nip'] ?? '-');
    }
}
$namaKepala = $namaKepala ?: 'Kepala Madrasah';
$nipKepala  = $nipKepala ?: '-';
?>

<div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; background: #ffffff;">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="p-2 rounded-2" style="background: #ecfdf5; color: #059669;">
                <i class="fas fa-signature fs-3"></i>
            </span>
            <div>
                <h5 class="mb-0 fw-bold text-dark">Status Penandatanganan Digital (QR Code)</h5>
                <small class="text-muted">Alur persetujuan bertingkat: Guru &rarr; Supervisor &rarr; Kepala Madrasah</small>
            </div>
        </div>
        <div>
            <?php if ($isFullySigned): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="fas fa-check-double me-1"></i> Disahkan Lengkap
                </span>
            <?php else: ?>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-semibold">
                    <i class="fas fa-clock me-1"></i> Sedang Dalam Proses TTD
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card-body p-4">
        <!-- Sequential Steps Visualizer -->
        <div class="row g-3 mb-4">
            <!-- 1. Guru -->
            <div class="col-md-4">
                <div class="p-3 rounded-3 border <?= !empty($signatures['guru']) ? 'border-success bg-success-subtle bg-opacity-25' : 'border-dashed bg-light' ?> h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge <?= !empty($signatures['guru']) ? 'bg-success text-white' : 'bg-secondary text-white' ?> rounded-pill">1. Guru</span>
                        <i class="fas <?= !empty($signatures['guru']) ? 'fa-check-circle text-success fs-4' : 'fa-hourglass-half text-muted fs-5' ?>"></i>
                    </div>
                    <div class="fw-bold text-dark text-truncate" title="<?= esc($namaGuru) ?>"><?= esc($namaGuru) ?></div>
                    <div class="small text-muted mb-2">NIP: <?= esc($nipGuru) ?></div>
                    <?php if (!empty($signatures['guru'])): ?>
                        <div class="small text-success fw-medium">
                            <i class="fas fa-qrcode me-1"></i> TTD: <?= date('d/m/Y', strtotime($signatures['guru']['tanggal_ttd'])) ?>
                        </div>
                    <?php else: ?>
                        <div class="small text-muted fst-italic">Menunggu TTD Guru</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 2. Supervisor -->
            <div class="col-md-4">
                <div class="p-3 rounded-3 border <?= !empty($signatures['supervisor']) ? 'border-success bg-success-subtle bg-opacity-25' : 'border-dashed bg-light' ?> h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge <?= !empty($signatures['supervisor']) ? 'bg-success text-white' : 'bg-secondary text-white' ?> rounded-pill">2. Supervisor</span>
                        <i class="fas <?= !empty($signatures['supervisor']) ? 'fa-check-circle text-success fs-4' : 'fa-hourglass-half text-muted fs-5' ?>"></i>
                    </div>
                    <div class="fw-bold text-dark text-truncate" title="<?= esc($namaSupervisor) ?>"><?= esc($namaSupervisor) ?></div>
                    <div class="small text-muted mb-2">NIP: <?= esc($nipSupervisor) ?></div>
                    <?php if (!empty($signatures['supervisor'])): ?>
                        <div class="small text-success fw-medium">
                            <i class="fas fa-qrcode me-1"></i> TTD: <?= date('d/m/Y', strtotime($signatures['supervisor']['tanggal_ttd'])) ?>
                        </div>
                    <?php else: ?>
                        <div class="small text-muted fst-italic">Menunggu TTD Supervisor</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 3. Kepala Madrasah -->
            <div class="col-md-4">
                <div class="p-3 rounded-3 border <?= !empty($signatures['kepala']) ? 'border-success bg-success-subtle bg-opacity-25' : 'border-dashed bg-light' ?> h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge <?= !empty($signatures['kepala']) ? 'bg-success text-white' : 'bg-secondary text-white' ?> rounded-pill">3. Kepala Madrasah</span>
                        <i class="fas <?= !empty($signatures['kepala']) ? 'fa-check-double text-success fs-4' : 'fa-hourglass-half text-muted fs-5' ?>"></i>
                    </div>
                    <div class="fw-bold text-dark text-truncate" title="<?= esc($namaKepala) ?>"><?= esc($namaKepala) ?></div>
                    <div class="small text-muted mb-2">NIP: <?= esc($nipKepala) ?></div>
                    <?php if (!empty($signatures['kepala'])): ?>
                        <div class="small text-success fw-medium">
                            <i class="fas fa-qrcode me-1"></i> Sah: <?= date('d/m/Y', strtotime($signatures['kepala']['tanggal_ttd'])) ?>
                        </div>
                    <?php else: ?>
                        <div class="small text-muted fst-italic">Menunggu Pengesahan</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Action Area -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 rounded-3 bg-light">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-info-circle text-primary"></i>
                <span class="small text-muted">
                    <?php if ($currentRole === 'admin'): ?>
                        <?php if ($isFullySigned): ?>
                            Seluruh peran telah menandatangani dokumen ini. Status dokumen: <strong>Disahkan Lengkap</strong>.
                        <?php else: ?>
                            Sebagai Administrator, Anda dapat mengesahkan seluruh tanda tangan peran sekaligus (Guru, Supervisor, Kepala Madrasah).
                        <?php endif; ?>
                    <?php elseif ($isSignedByMe): ?>
                        Anda telah menandatangani dokumen ini. QR Code keabsahan telah tercetak di dokumen PDF.
                    <?php elseif ($canSignCheck['allowed']): ?>
                        Giliran Anda untuk menandatangani dokumen ini secara digital.
                    <?php else: ?>
                        <?= esc($canSignCheck['message']) ?>
                    <?php endif; ?>
                </span>
            </div>

            <div class="d-flex align-items-center gap-2">
                <?php if ($currentRole === 'admin' && !$isFullySigned): ?>
                    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSignAllAdmin">
                        <i class="fas fa-check-double"></i>
                        <span>Sahkan Semua Sekaligus</span>
                    </button>
                <?php endif; ?>

                <?php if ($canSignCheck['allowed'] && in_array($currentRole, ['guru', 'supervisor', 'kepala'])): ?>
                    <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSignDigital">
                        <i class="fas fa-pen-fancy"></i>
                        <span>Tanda Tangani Digital</span>
                    </button>
                <?php elseif ($isSignedByMe): ?>
                    <button type="button" class="btn btn-outline-success btn-sm disabled" disabled>
                        <i class="fas fa-check me-1"></i> Sudah Anda Tanda Tangani
                    </button>
                <?php endif; ?>

                <?php if ($currentRole === 'admin' && $hasAnySignatures): ?>
                    <button type="button" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalResetSignature">
                        <i class="fas fa-undo-alt"></i>
                        <span>Reset Tanda Tangan</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Tanda Tangan Digital -->
<?php if ($canSignCheck['allowed'] && in_array($currentRole, ['guru', 'supervisor', 'kepala'])): ?>
<div class="modal fade" id="modalSignDigital" tabindex="-1" aria-labelledby="modalSignDigitalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="modalSignDigitalLabel">
                    <i class="fas fa-file-signature me-2"></i>Konfirmasi Tanda Tangan Digital
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSignDigital" action="<?= base_url('signature/sign/' . $jadwalId) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="dokumen_type" value="supervisi">
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle" style="width: 64px; height: 64px;">
                            <i class="fas fa-qrcode fs-1"></i>
                        </div>
                    </div>
                    <p class="text-center text-dark fw-semibold mb-3">
                        Anda akan menandatangani dokumen hasil supervisi ini sebagai 
                        <span class="badge bg-primary text-uppercase"><?= esc($currentRole) ?></span>
                    </p>
                    
                    <div class="mb-3">
                        <label for="tanggal_ttd" class="form-label fw-bold small text-muted">Tanggal Penandatanganan Dokumen:</label>
                        <input type="date" class="form-control" id="tanggal_ttd" name="tanggal_ttd" value="<?= esc($schedule['tanggal_supervisi'] ?? date('Y-m-d')) ?>" required>
                        <div class="form-text small">Dapat disesuaikan dengan tanggal riil pelaksanaan supervisi.</div>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-0">
                        <i class="fas fa-shield-alt me-1"></i>
                        Sistem akan menghasilkan QR Code verifikasi unik berstandar digital dan mencatat identitas akun, alamat IP, dan waktu sistem.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold d-inline-flex align-items-center gap-2" id="btnSubmitSign">
                        <i class="fas fa-check"></i>
                        <span>Ya, Tanda Tangani Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formSignDigital');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitSign');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Memproses...';

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message || 'Dokumen berhasil ditandatangani!');
                    window.location.reload();
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem.'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check"></i> <span>Ya, Tanda Tangani Sekarang</span>';
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan jaringan atau koneksi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> <span>Ya, Tanda Tangani Sekarang</span>';
            });
        });
    }
});
</script>
<?php endif; ?>

<!-- Modal Reset Tanda Tangan (Khusus Admin) -->
<?php if ($currentRole === 'admin' && $hasAnySignatures): ?>
<div class="modal fade" id="modalResetSignature" tabindex="-1" aria-labelledby="modalResetSignatureLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="modalResetSignatureLabel">
                    <i class="fas fa-undo-alt me-2"></i>Reset Tanda Tangan Digital
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formResetSignature" action="<?= base_url('signature/reset/' . $jadwalId) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="dokumen_type" value="supervisi">
                <div class="modal-body p-4">
                    <div class="alert alert-danger py-2 px-3 small mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Perhatian:</strong> Aksi ini akan menghapus seluruh tanda tangan digital (Guru, Supervisor, Kepala Madrasah) pada jadwal ini dan membuka kembali izin pengeditan penilaian untuk revisi.
                    </div>

                    <div class="mb-3">
                        <label for="alasan_reset" class="form-label fw-bold small text-muted">Alasan Reset Tanda Tangan (Wajib):</label>
                        <textarea class="form-control" id="alasan_reset" name="alasan" rows="3" placeholder="Contoh: Perbaikan skor komponen penilaian RPP/Modul Ajar sesuai instruksi pengawas..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold d-inline-flex align-items-center gap-2" id="btnSubmitReset">
                        <i class="fas fa-trash-alt"></i>
                        <span>Reset Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formReset = document.getElementById('formResetSignature');
    if (formReset) {
        formReset.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!confirm('Apakah Anda yakin ingin mereset seluruh tanda tangan digital dokumen ini?')) {
                return;
            }

            const btn = document.getElementById('btnSubmitReset');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Memproses...';

            const formData = new FormData(formReset);

            fetch(formReset.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message || 'Tanda tangan digital berhasil di-reset!');
                    window.location.reload();
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem.'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trash-alt"></i> <span>Reset Sekarang</span>';
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan jaringan atau koneksi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-alt"></i> <span>Reset Sekarang</span>';
            });
        });
    }
});
</script>
<?php endif; ?>

<!-- Modal Pengesahan Sekaligus Semua Peran (Khusus Admin) -->
<?php if ($currentRole === 'admin' && !$isFullySigned): ?>
<div class="modal fade" id="modalSignAllAdmin" tabindex="-1" aria-labelledby="modalSignAllAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalSignAllAdminLabel">
                    <i class="fas fa-check-double me-2"></i>Sahkan Semua Tanda Tangan Sekaligus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formSignAllAdmin" action="<?= base_url('signature/sign-all-admin/' . $jadwalId) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="dokumen_type" value="supervisi">
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle" style="width: 64px; height: 64px;">
                            <i class="fas fa-stamp fs-1"></i>
                        </div>
                    </div>
                    <p class="text-center text-dark fw-semibold mb-3">
                        Anda akan membubuhi tanda tangan digital untuk seluruh peran 
                        (<span class="text-primary font-weight-bold">Guru, Supervisor, dan Kepala Madrasah</span>) secara langsung.
                    </p>

                    <div class="p-3 bg-light rounded-3 border small mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-muted"><i class="fas fa-user me-1 text-primary"></i> Guru:</span>
                            <span class="fw-bold text-dark"><?= esc($namaGuru) ?> (NIP: <?= esc($nipGuru) ?>)</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="text-muted"><i class="fas fa-user-tie me-1 text-success"></i> Supervisor:</span>
                            <span class="fw-bold text-dark"><?= esc($namaSupervisor) ?> (NIP: <?= esc($nipSupervisor) ?>)</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-muted"><i class="fas fa-user-shield me-1 text-dark"></i> Kepala:</span>
                            <span class="fw-bold text-dark"><?= esc($namaKepala) ?> (NIP: <?= esc($nipKepala) ?>)</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal_ttd_all" class="form-label fw-bold small text-muted">Tanggal Penandatanganan Seragam:</label>
                        <input type="date" class="form-control" id="tanggal_ttd_all" name="tanggal_ttd" value="<?= esc($schedule['tanggal_supervisi'] ?? date('Y-m-d')) ?>" required>
                        <div class="form-text small">Tanggal ini akan diaplikasikan ke tanda tangan yang belum terisi.</div>
                    </div>

                    <div class="alert alert-info py-2 px-3 small mb-0">
                        <i class="fas fa-shield-alt me-1"></i>
                        Sistem akan menghasilkan QR Code resmi keabsahan dokumen untuk ketiga peran, dan watermark DRAFT pada cetakan PDF akan otomatis dihilangkan.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold d-inline-flex align-items-center gap-2" id="btnSubmitSignAll">
                        <i class="fas fa-check-double"></i>
                        <span>Ya, Sahkan Semua Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formAll = document.getElementById('formSignAllAdmin');
    if (formAll) {
        formAll.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitSignAll');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Memproses...';

            const formData = new FormData(formAll);

            fetch(formAll.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message || 'Seluruh tanda tangan berhasil disahkan!');
                    window.location.reload();
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan sistem.'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-double"></i> <span>Ya, Sahkan Semua Sekarang</span>';
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan jaringan atau koneksi.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-double"></i> <span>Ya, Sahkan Semua Sekarang</span>';
            });
        });
    }
});
</script>
<?php endif; ?>
