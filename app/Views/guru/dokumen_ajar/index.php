<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-2">

    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small">
                    <li class="breadcrumb-item"><a href="<?= base_url('guru') ?>" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('guru/jadwal') ?>" class="text-decoration-none">Jadwal Supervisi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kelola Dokumen Ajar</li>
                </ol>
            </nav>
            <h1 class="h3 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span class="avatar bg-primary-lt text-primary rounded-2 p-2">
                    <i class="fas fa-folder-open"></i>
                </span>
                Kelola Dokumen Perangkat Ajar
            </h1>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('guru/jadwal') ?>" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Jadwal</span>
            </a>
            <a href="#formUploadCard" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm d-md-none">
                <i class="fas fa-plus"></i>
                <span>Tambah Dokumen</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fas fa-check-circle fs-3 text-success"></i>
            <div class="flex-grow-1">
                <strong>Berhasil!</strong> <?= session()->getFlashdata('success') ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle fs-3 text-danger"></i>
            <div class="flex-grow-1">
                <strong>Peringatan:</strong> <?= session()->getFlashdata('error') ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-times-circle fs-3 text-danger"></i>
                <strong>Terdapat beberapa kesalahan input:</strong>
            </div>
            <ul class="mb-0 ps-4 small">
                <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Context Card: Ringkasan Jadwal Supervisi Terpadu -->
    <div class="card shadow-sm border-0 mb-4 bg-gradient-light" style="border-left: 4px solid #206bc4 !important;">
        <div class="card-body py-3">
            <div class="row align-items-center g-3">
                <div class="col-lg-8 col-md-7">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge bg-blue text-white px-2 py-1">
                            <i class="fas fa-book-open me-1"></i> <?= esc($jadwal['mata_pelajaran'] ?? 'Mata Pelajaran') ?>
                        </span>
                        <span class="badge bg-azure-lt px-2 py-1">
                            <i class="fas fa-chalkboard me-1"></i> Kelas <?= esc($jadwal['nama_kelas'] ?? $jadwal['kelas'] ?? '-') ?>
                        </span>
                        <?php if (!empty($jadwal['kelas_fase'])): ?>
                            <span class="badge bg-purple-lt px-2 py-1">
                                <i class="fas fa-layer-group me-1"></i> Fase <?= esc($jadwal['kelas_fase']) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!empty($jadwal['nama_indikator'])): ?>
                            <span class="badge bg-teal-lt px-2 py-1">
                                <i class="fas fa-bullseye me-1"></i> <?= esc($jadwal['nama_indikator']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="text-secondary small d-flex flex-wrap align-items-center gap-3">
                        <span><i class="far fa-calendar-alt text-primary me-1"></i> <?= date('d F Y', strtotime($jadwal['tanggal_supervisi'])) ?></span>
                        <?php if (!empty($jadwal['jam_ke'])): ?>
                            <span><i class="far fa-clock text-primary me-1"></i> Jam Ke: <?= esc($jadwal['jam_ke']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($jadwal['nama_supervisor'])): ?>
                            <span><i class="fas fa-user-tie text-primary me-1"></i> Supervisor: <strong><?= esc($jadwal['nama_supervisor']) ?></strong></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-5 text-md-end">
                    <div class="d-inline-flex flex-column align-items-md-end">
                        <span class="text-secondary small mb-1">Status Supervisi:</span>
                        <?php 
                            $status = strtolower($jadwal['status'] ?? 'terjadwal');
                            $badgeClass = 'bg-secondary';
                            $badgeText = ucfirst($jadwal['status'] ?? 'Terjadwal');
                            if (in_array($status, ['terjadwal', 'dijadwalkan'])) {
                                $badgeClass = 'bg-info-lt text-info';
                                $badgeText = 'Terjadwal';
                            } elseif ($status === 'pra_observasi') {
                                $badgeClass = 'bg-warning-lt text-warning';
                                $badgeText = 'Tahap Pra-Observasi';
                            } elseif ($status === 'observasi') {
                                $badgeClass = 'bg-primary-lt text-primary';
                                $badgeText = 'Tahap Observasi';
                            } elseif (in_array($status, ['pasca_observasi', 'tindak_lanjut'])) {
                                $badgeClass = 'bg-purple-lt text-purple';
                                $badgeText = 'Pasca Observasi / Tindak Lanjut';
                            } elseif ($status === 'selesai') {
                                $badgeClass = 'bg-success-lt text-success';
                                $badgeText = 'Supervisi Selesai';
                            }
                        ?>
                        <span class="badge <?= $badgeClass ?> px-3 py-2 fs-6 rounded-pill fw-bold">
                            <i class="fas fa-circle fs-4 me-1 align-middle"></i> <?= $badgeText ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">

        <!-- Left Column: Form Upload / Tautkan Dokumen (5 Cols) -->
        <div class="col-xl-5 col-lg-5" id="formUploadCard">
            <div class="card shadow-sm border-0 sticky-top" style="top: 80px; z-index: 10;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">
                            <i class="fas fa-cloud-upload-alt text-primary me-2"></i> Tautkan Perangkat Ajar
                        </h3>
                        <p class="text-secondary small mb-0 mt-1">Unggah link Modul Ajar, ATP, atau asesmen Kurikulum Merdeka.</p>
                    </div>
                    <span class="badge bg-primary text-white rounded-pill px-2 py-1 small">Formulir</span>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('guru/dokumen-ajar/save') ?>" method="post" id="formDokumenAjar">
                        <?= csrf_field() ?>
                        <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">

                        <!-- Seksi 1: Identitas Dokumen -->
                        <div class="mb-3">
                            <label for="kategori_dokumen" class="form-label fw-bold text-dark">
                                Kategori Dokumen <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-tags"></i></span>
                                <select class="form-select" id="kategori_dokumen" name="kategori_dokumen" required>
                                    <option value="modul_ajar" selected>📄 Modul Ajar / RPP+ (Inti)</option>
                                    <option value="atp">🧭 Alur Tujuan Pembelajaran (ATP)</option>
                                    <option value="cp">🎯 Capaian Pembelajaran (CP)</option>
                                    <option value="asesmen_awal">📝 Asesmen Awal / Diagnostik</option>
                                    <option value="kktp">📊 Kriteria Ketercapaian TP (KKTP)</option>
                                    <option value="modul_p5">🌟 Modul Projek P5</option>
                                    <option value="lkpd">📋 Lembar Kerja Peserta Didik (LKPD)</option>
                                    <option value="lainnya">📁 Perangkat Pendukung Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-sm-5">
                                <label for="fase" class="form-label fw-bold text-dark">Fase</label>
                                <select class="form-select" id="fase" name="fase">
                                    <option value="">-- Pilih --</option>
                                    <option value="A" <?= (($jadwal['kelas_fase'] ?? '') === 'A') ? 'selected' : '' ?>>Fase A (Kelas 1-2)</option>
                                    <option value="B" <?= (($jadwal['kelas_fase'] ?? '') === 'B') ? 'selected' : '' ?>>Fase B (Kelas 3-4)</option>
                                    <option value="C" <?= (($jadwal['kelas_fase'] ?? '') === 'C') ? 'selected' : '' ?>>Fase C (Kelas 5-6)</option>
                                    <option value="D" <?= (($jadwal['kelas_fase'] ?? '') === 'D') ? 'selected' : '' ?>>Fase D (Kelas 7-9)</option>
                                    <option value="E" <?= (($jadwal['kelas_fase'] ?? '') === 'E') ? 'selected' : '' ?>>Fase E (Kelas 10)</option>
                                    <option value="F" <?= (($jadwal['kelas_fase'] ?? '') === 'F') ? 'selected' : '' ?>>Fase F (Kelas 11-12)</option>
                                </select>
                            </div>
                            <div class="col-sm-7">
                                <label for="nama_dokumen" class="form-label fw-bold text-dark">
                                    Nama Dokumen <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="nama_dokumen" name="nama_dokumen" 
                                    placeholder="Contoh: Modul Ajar Bab 2 Sesi 1" required>
                            </div>
                        </div>

                        <!-- Seksi 2: Fokus Kurikulum Merdeka -->
                        <div class="mb-3">
                            <label for="tujuan_pembelajaran" class="form-label fw-bold text-dark">
                                Tujuan Pembelajaran (TP)
                            </label>
                            <textarea class="form-control" id="tujuan_pembelajaran" name="tujuan_pembelajaran" rows="2" 
                                placeholder="Tuliskan 1-3 rumusan TP yang dicakup dalam modul/sesi pembelajaran ini..."></textarea>
                            <div class="form-text text-muted small">Membantu supervisor memahami target kompetensi yang diamati.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark d-block mb-2">
                                Pembelajaran Berdiferensiasi
                                <span class="text-muted fw-normal small">(Pilih yang diterapkan)</span>
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <label class="btn btn-outline-primary rounded-pill d-inline-flex align-items-center gap-1 cursor-pointer">
                                    <input type="checkbox" class="form-check-input mt-0 me-1" id="dif_konten" name="strategi_diferensiasi[]" value="konten">
                                    <span>🎨 Diferensiasi Konten</span>
                                </label>
                                <label class="btn btn-outline-info rounded-pill d-inline-flex align-items-center gap-1 cursor-pointer">
                                    <input type="checkbox" class="form-check-input mt-0 me-1" id="dif_proses" name="strategi_diferensiasi[]" value="proses">
                                    <span>⚙️ Diferensiasi Proses</span>
                                </label>
                                <label class="btn btn-outline-success rounded-pill d-inline-flex align-items-center gap-1 cursor-pointer">
                                    <input type="checkbox" class="form-check-input mt-0 me-1" id="dif_produk" name="strategi_diferensiasi[]" value="produk">
                                    <span>📦 Diferensiasi Produk</span>
                                </label>
                            </div>
                        </div>

                        <hr class="my-3 text-muted opacity-25">

                        <!-- Seksi 3: Tautan Berkas & Eksternal -->
                        <div class="mb-3">
                            <label for="link_drive" class="form-label fw-bold text-dark">
                                Tautan Dokumen (Google Drive / Canva) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-primary"><i class="fab fa-google-drive"></i></span>
                                <input type="url" class="form-control" id="link_drive" name="link_drive" 
                                    placeholder="https://drive.google.com/... atau https://canva.com/..." required>
                            </div>
                            <div class="form-text text-muted small mt-1">
                                <i class="fas fa-lock-open text-warning me-1"></i> Pastikan izin akses link disetel ke <strong>"Anyone with link / Siapa saja yang memiliki link"</strong>.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="link_eksternal" class="form-label fw-bold text-dark">
                                Tautan Sumber Belajar / PMM Eksternal <span class="text-muted fw-normal small">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-info"><i class="fas fa-globe"></i></span>
                                <input type="url" class="form-control" id="link_eksternal" name="link_eksternal" 
                                    placeholder="https://guru.kemdikbud.go.id/... atau tautan video/web">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-bold text-dark">
                                Catatan / Keterangan Tambahan <span class="text-muted fw-normal small">(Opsional)</span>
                            </label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2" 
                                placeholder="Informasi pendukung untuk supervisor (misal: revisi ke-2, modul pertemuan ganjil, dsb.)"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>Simpan Perangkat Ajar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Daftar Dokumen yang Ditautkan (7 Cols) -->
        <div class="col-xl-7 col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">
                            <i class="fas fa-clipboard-list text-primary me-2"></i> Dokumen Terunggah
                        </h3>
                        <p class="text-secondary small mb-0 mt-1">Daftar berkas yang disiapkan untuk supervisi ini.</p>
                    </div>
                    <span class="badge bg-blue-lt px-3 py-2 fs-6 rounded-pill fw-bold">
                        <?= count($dokumen) ?> Berkas
                    </span>
                </div>

                <div class="card-body p-3 p-md-4">
                    <?php if (empty($dokumen)): ?>
                        <!-- Empty State Menarik -->
                        <div class="text-center py-5">
                            <div class="avatar avatar-xl bg-blue-lt text-primary rounded-circle mb-3 mx-auto p-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-file-upload fa-3x"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">Belum Ada Dokumen yang Ditautkan</h4>
                            <p class="text-secondary small mx-auto mb-4" style="max-width: 420px;">
                                Perangkat ajar seperti Modul Ajar, ATP, atau LKPD belum diunggah untuk jadwal supervisi ini. Gunakan formulir di samping untuk mulai menautkan dokumen pertama Anda.
                            </p>
                            <a href="#nama_dokumen" class="btn btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-plus me-1"></i> Isi Formulir Sekarang
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- List Dokumen Cards -->
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($dokumen as $doc): ?>
                                <?php 
                                    $kategori = $doc['kategori_dokumen'] ?? 'modul_ajar';
                                    $kategoriNama = str_replace('_', ' ', $kategori);
                                    
                                    // Warna Badge Kategori
                                    $kategoriBadge = 'bg-primary-lt text-primary';
                                    $kategoriIcon = 'fa-file-alt';
                                    if ($kategori === 'modul_ajar') {
                                        $kategoriBadge = 'bg-blue text-white';
                                        $kategoriIcon = 'fa-book-reader';
                                    } elseif ($kategori === 'atp') {
                                        $kategoriBadge = 'bg-purple text-white';
                                        $kategoriIcon = 'fa-compass';
                                    } elseif ($kategori === 'asesmen_awal') {
                                        $kategoriBadge = 'bg-teal text-white';
                                        $kategoriIcon = 'fa-edit';
                                    } elseif ($kategori === 'lkpd') {
                                        $kategoriBadge = 'bg-orange text-white';
                                        $kategoriIcon = 'fa-tasks';
                                    } elseif ($kategori === 'modul_p5') {
                                        $kategoriBadge = 'bg-yellow text-dark';
                                        $kategoriIcon = 'fa-star';
                                    }

                                    // Status Badge & Border
                                    $status = $doc['status'] ?? 'Pending';
                                    $cardBorder = 'border-start-secondary';
                                    $statusBadge = 'bg-warning-lt text-warning';
                                    $statusIcon = 'fa-clock';
                                    if ($status === 'Disetujui') {
                                        $cardBorder = 'border-start-success';
                                        $statusBadge = 'bg-success-lt text-success';
                                        $statusIcon = 'fa-check-circle';
                                    } elseif ($status === 'Ditolak') {
                                        $cardBorder = 'border-start-danger';
                                        $statusBadge = 'bg-danger-lt text-danger';
                                        $statusIcon = 'fa-exclamation-circle';
                                    }
                                ?>
                                <div class="card border shadow-none rounded-3 position-relative overflow-hidden hover-shadow-sm transition-all <?= $cardBorder ?>" style="border-left-width: 4px !important;">
                                    <div class="card-body p-3 p-md-4">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-2 mb-2">
                                            <div class="d-flex flex-wrap align-items-center gap-2">
                                                <span class="badge <?= $kategoriBadge ?> px-2 py-1 text-uppercase font-weight-bold">
                                                    <i class="fas <?= $kategoriIcon ?> me-1"></i> <?= esc($kategoriNama) ?>
                                                </span>
                                                <?php if (!empty($doc['fase'])): ?>
                                                    <span class="badge bg-secondary-lt px-2 py-1">
                                                        Fase <?= esc($doc['fase']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="badge <?= $statusBadge ?> px-2 py-1 fw-bold">
                                                    <i class="fas <?= $statusIcon ?> me-1"></i> <?= esc($status) ?>
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="far fa-clock me-1"></i> <?= date('d M Y', strtotime($doc['created_at'] ?? 'now')) ?>
                                            </small>
                                        </div>

                                        <h4 class="fw-bold text-dark mb-2">
                                            <?= esc($doc['nama_dokumen']) ?>
                                        </h4>

                                        <!-- Tujuan Pembelajaran Callout -->
                                        <?php if (!empty($doc['tujuan_pembelajaran'])): ?>
                                            <div class="p-2 mb-2 rounded bg-light border-start border-primary border-3 small">
                                                <strong class="text-primary d-block mb-1"><i class="fas fa-bullseye me-1"></i> Tujuan Pembelajaran:</strong>
                                                <span class="text-secondary"><?= nl2br(esc($doc['tujuan_pembelajaran'])) ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Strategi Diferensiasi -->
                                        <?php if (!empty($doc['strategi_diferensiasi'])): ?>
                                            <div class="d-flex flex-wrap align-items-center gap-1 mb-2">
                                                <span class="small text-muted me-1">Diferensiasi:</span>
                                                <?php foreach (explode(',', $doc['strategi_diferensiasi']) as $dif): ?>
                                                    <span class="badge bg-light text-dark border px-2 py-1 small">
                                                        <i class="fas fa-check text-success me-1"></i> <?= ucfirst(trim($dif)) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Keterangan Tambahan -->
                                        <?php if (!empty($doc['keterangan'])): ?>
                                            <p class="text-muted small mb-2 fst-italic">
                                                <i class="fas fa-info-circle me-1"></i> <?= esc($doc['keterangan']) ?>
                                            </p>
                                        <?php endif; ?>

                                        <!-- Feedback / Catatan Revisi Jika Ditolak -->
                                        <?php if ($doc['status'] === 'Ditolak' && !empty($doc['feedback'])): ?>
                                            <div class="alert alert-danger p-3 rounded-2 mt-2 mb-2 border-0 bg-danger-lt">
                                                <div class="d-flex align-items-center gap-2 mb-1 text-danger fw-bold">
                                                    <i class="fas fa-comment-dots fs-4"></i> Catatan Perbaikan dari Supervisor:
                                                </div>
                                                <div class="text-dark small ps-4">
                                                    <?= nl2br(esc($doc['feedback'])) ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Action Buttons Toolbar -->
                                        <div class="d-flex flex-wrap align-items-center justify-content-between pt-2 mt-2 border-top gap-2">
                                            <div class="d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1 btn-preview"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#previewModal"
                                                    data-url="<?= esc($doc['link_drive']) ?>"
                                                    data-name="<?= esc($doc['nama_dokumen']) ?>">
                                                    <i class="fas fa-eye"></i>
                                                    <span>Preview</span>
                                                </button>
                                                <a href="<?= esc($doc['link_drive']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                                                    <i class="fas fa-external-link-alt"></i>
                                                    <span>Buka Tautan</span>
                                                </a>
                                                <?php if (!empty($doc['link_eksternal'])): ?>
                                                    <a href="<?= esc($doc['link_eksternal']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-info d-inline-flex align-items-center gap-1">
                                                        <i class="fas fa-globe"></i>
                                                        <span>PMM / Referensi</span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>

                                            <div>
                                                <?php if ($doc['status'] !== 'Disetujui'): ?>
                                                    <form action="<?= base_url('guru/dokumen-ajar/delete/' . $doc['id']) ?>" method="post" class="d-inline form-delete-doc">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1 btn-trigger-delete" data-doc-title="<?= esc($doc['nama_dokumen']) ?>">
                                                            <i class="fas fa-trash-alt"></i>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted small d-inline-flex align-items-center gap-1">
                                                        <i class="fas fa-lock text-success"></i> Terkunci (Disetujui)
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Preview Modal (Full Support Tabler / Bootstrap 5) -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-light py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="avatar bg-primary-lt text-primary rounded-circle"><i class="fas fa-file-alt"></i></span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="previewModalLabel">Preview Dokumen</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="openExternalBtn" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0" style="background-color: #2b3035;">
                <div id="previewLoader" class="text-center py-5 text-white">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <div class="small">Memuat berkas dokumen...</div>
                </div>
                <div class="ratio ratio-16x9" style="min-height: 520px;">
                    <iframe class="w-100 h-100 border-0" id="previewFrame" src="" allow="autoplay" style="display: none;"></iframe>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 justify-content-between">
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i> Jika dokumen tidak tampil, pastikan izin link publik atau klik <strong>Buka di Tab Baru</strong>.
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Preview Modal
        var previewModal = document.getElementById('previewModal');
        var previewFrame = document.getElementById('previewFrame');
        var previewLoader = document.getElementById('previewLoader');
        var openExternalBtn = document.getElementById('openExternalBtn');
        var previewModalLabel = document.getElementById('previewModalLabel');

        if (previewModal) {
            previewModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                if (!button) return;

                var url = button.getAttribute('data-url');
                var name = button.getAttribute('data-name');

                if (previewModalLabel) previewModalLabel.textContent = name;
                if (openExternalBtn) openExternalBtn.setAttribute('href', url);

                // Transform URL for preview if needed
                var previewUrl = url;
                if (url.includes('drive.google.com') && url.includes('/view')) {
                    previewUrl = url.replace('/view', '/preview');
                } else if (url.includes('drive.google.com') && !url.includes('/preview')) {
                    var match = url.match(/\/d\/([a-zA-Z0-9_-]+)/);
                    if (match && match[1]) {
                        previewUrl = 'https://drive.google.com/file/d/' + match[1] + '/preview';
                    }
                }

                if (previewLoader) previewLoader.style.display = 'block';
                if (previewFrame) {
                    previewFrame.style.display = 'none';
                    previewFrame.src = previewUrl;
                    previewFrame.onload = function() {
                        if (previewLoader) previewLoader.style.display = 'none';
                        previewFrame.style.display = 'block';
                    };
                }
            });

            previewModal.addEventListener('hidden.bs.modal', function() {
                if (previewFrame) previewFrame.src = '';
                if (previewLoader) previewLoader.style.display = 'block';
            });
        }

        // Delete confirmation with SweetAlert2 if available
        var deleteButtons = document.querySelectorAll('.btn-trigger-delete');
        deleteButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var form = this.closest('.form-delete-doc');
                var title = this.getAttribute('data-doc-title') || 'dokumen ini';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Hapus Dokumen?',
                        text: 'Apakah Anda yakin ingin menghapus "' + title + '"?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d63939',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('Apakah Anda yakin ingin menghapus "' + title + '"?')) {
                        form.submit();
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>