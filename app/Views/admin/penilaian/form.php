<?php
$rolePrefix  = $rolePrefix ?? (session()->get('role') ?? 'admin');
$layout      = $layout ?? ('layouts/' . $rolePrefix);
$pageTitle   = $pageTitle ?? ($rolePrefix === 'admin' ? 'Edit Hasil Penilaian Supervisi' : 'Form Penilaian Supervisi');
$backUrl     = $backUrl ?? ($rolePrefix === 'admin' ? base_url('admin/laporan/hasil-supervisi/detail/' . $schedule['id']) : base_url($rolePrefix . '/jadwal'));
$redirectUrl = $redirectUrl ?? ($rolePrefix === 'admin' ? base_url('admin/laporan/hasil-supervisi/detail/' . $schedule['id']) : base_url($rolePrefix . '/penilaian/view/' . $schedule['id']));
$totalTahap  = !empty($jenisPenilaian) ? count($jenisPenilaian) : 0;

// Pisahkan jenisPenilaian menjadi 2 Kategori Utama untuk Stepper:
// 1. Perangkat Ajar (ATP, Modul Ajar, Administrasi Perencanaan)
// 2. Observasi Kelas & Kinerja (8 Indikator PMM & Pelaksanaan)
$perangkatList = [];
$observasiList = [];

foreach ($jenisPenilaian as $jenis) {
    $jId = $jenis['id'];
    $namaJenis = $jenis['nama'] ?? ($jenis['nama_jenis'] ?? '');
    $kategori = $jenis['kategori_supervisi'] ?? '';
    
    $isPerangkat = ($kategori === 'perangkat_ajar') 
        || in_array($jId, [1, 13, 14]) 
        || stripos($namaJenis, 'perangkat') !== false 
        || stripos($namaJenis, 'administrasi') !== false 
        || stripos($namaJenis, 'atp') !== false 
        || stripos($namaJenis, 'modul ajar') !== false 
        || stripos($namaJenis, 'perencanaan') !== false;

    if ($isPerangkat) {
        $perangkatList[] = $jenis;
    } else {
        $observasiList[] = $jenis;
    }
}

$perangkatFilled = 0;
foreach ($perangkatList as $p) {
    if (isset($existingResults[$p['id']])) {
        $perangkatFilled++;
    }
}

$observasiFilled = 0;
foreach ($observasiList as $o) {
    if (isset($existingResults[$o['id']])) {
        $observasiFilled++;
    }
}

$fokusPmmId = $schedule['indikator_pmm_id'] ?? null;
?>
<?= $this->extend($layout) ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($pageTitle) ?></h1>
        <div>
            <a href="<?= base_url($rolePrefix . '/foto-bukti/upload/' . $schedule['id']) ?>" class="btn btn-info mr-2 shadow-sm font-weight-bold">
                <i class="fas fa-camera mr-1"></i> Foto Bukti Supervisi
            </a>
            <a href="<?= $backUrl ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> <?= $rolePrefix === 'admin' ? 'Kembali ke Detail' : 'Kembali ke Jadwal' ?>
            </a>
        </div>
    </div>

    <?php if (!empty($isLockedBySignature)): ?>
    <!-- Alert Penilaian Terkunci -->
    <div class="alert alert-danger border-left-danger shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-lock fa-2x mr-3 text-danger"></i>
            <div>
                <h5 class="alert-heading font-weight-bold mb-1">Penilaian Terkunci (Tanda Tangan Digital Aktif)</h5>
                <p class="mb-0 small">Dokumen hasil supervisi ini telah mulai ditandatangani secara digital. Untuk menjaga integritas data keabsahan, formulir penilaian dinonaktifkan. Jika terdapat revisi nilai yang mendesak, Administrator harus melakukan <strong>Reset Tanda Tangan</strong> terlebih dahulu melalui halaman Detail Hasil Supervisi.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($rolePrefix === 'admin'): ?>
    <!-- Alert Info Admin -->
    <div class="alert alert-warning border-left-warning shadow-sm" role="alert">
        <i class="fas fa-user-shield mr-2"></i>
        <strong>Mode Administrator:</strong> Anda memiliki hak akses penuh untuk meninjau dan memperbarui skor, catatan, serta rekomendasi penilaian supervisi ini. Perubahan yang Anda simpan akan otomatis memperbarui nilai akhir, predikat ketercapaian, dan dicatat di Audit Log sistem.
    </div>
    <?php endif; ?>

    <!-- Teacher & Schedule Info Card Partial -->
    <?= view('admin/penilaian/partials/info_jadwal', ['schedule' => $schedule]) ?>

    <!-- 3-Step Wizard Navigation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tasks mr-1"></i> Form Penilaian Supervisi (3 Tahap Ringkas)
                </h6>
                <div class="small text-muted">
                    Total: <strong><?= $totalTahap ?> Komponen Instrumen</strong> terkelompok rapi
                </div>
            </div>
        </div>
        <div class="card-body p-3 p-md-4">
            
            <!-- Step Navigation Pills -->
            <ul class="nav nav-pills nav-fill bg-light p-2 rounded mb-4 shadow-sm" id="wizardSteps" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold py-3 text-left text-md-center wizard-step-tab" 
                       id="step-perangkat-tab" 
                       data-toggle="pill" 
                       href="#step-perangkat" 
                       role="tab" 
                       aria-controls="step-perangkat" 
                       aria-selected="true">
                        <span class="badge badge-primary rounded-circle mr-1 px-2 py-1 step-num">1</span>
                        <span><i class="fas fa-file-alt mr-1"></i> 1. Telaah Perangkat Ajar</span>
                        <span class="badge badge-light border ml-1 badge-step-counter" id="badgeCountPerangkat">
                            <?= $perangkatFilled ?> / <?= count($perangkatList) ?>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold py-3 text-left text-md-center wizard-step-tab" 
                       id="step-observasi-tab" 
                       data-toggle="pill" 
                       href="#step-observasi" 
                       role="tab" 
                       aria-controls="step-observasi" 
                       aria-selected="false">
                        <span class="badge badge-secondary rounded-circle mr-1 px-2 py-1 step-num">2</span>
                        <span><i class="fas fa-chalkboard-teacher mr-1"></i> 2. Observasi Kelas & Kinerja</span>
                        <span class="badge badge-light border ml-1 badge-step-counter" id="badgeCountObservasi">
                            <?= $observasiFilled ?> / <?= count($observasiList) ?>
                        </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold py-3 text-left text-md-center wizard-step-tab" 
                       id="step-foto-tab" 
                       data-toggle="pill" 
                       href="#step-foto-pane" 
                       role="tab" 
                       aria-controls="step-foto-pane" 
                       aria-selected="false">
                        <span class="badge badge-secondary rounded-circle mr-1 px-2 py-1 step-num">3</span>
                        <span><i class="fas fa-camera-retro mr-1"></i> 3. Foto Bukti & Selesai</span>
                        <span class="badge badge-light border ml-1">
                            <?= !empty($existingPhotos) ? count($existingPhotos) : 0 ?> Foto
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Wizard Step Content Panes -->
            <div class="tab-content" id="wizardStepsContent">

                <!-- ========================================== -->
                <!-- TAHAP 1: TELAAH PERANGKAT AJAR            -->
                <!-- ========================================== -->
                <div class="tab-pane fade show active" id="step-perangkat" role="tabpanel" aria-labelledby="step-perangkat-tab">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h5 class="font-weight-bold text-gray-800 m-0">
                                <i class="fas fa-clipboard-list text-primary mr-1"></i> Tahap 1: Telaah Dokumen & Perangkat Ajar
                            </h5>
                            <small class="text-muted">Telaah perencanaan, Alur Tujuan Pembelajaran (ATP), dan Modul Ajar / RPP Guru</small>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-expand-all mr-1" data-target="#accordionPerangkat">
                                <i class="fas fa-angle-double-down mr-1"></i> Buka Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-collapse-all" data-target="#accordionPerangkat">
                                <i class="fas fa-angle-double-up mr-1"></i> Tutup Semua
                            </button>
                        </div>
                    </div>

                    <!-- Accordion Perangkat Ajar -->
                    <div class="accordion" id="accordionPerangkat">
                        <?php foreach ($perangkatList as $pIdx => $jenis): ?>
                            <?php 
                                $jId = $jenis['id'];
                                $namaJenis = $jenis['nama'] ?? ($jenis['nama_jenis'] ?? ('Komponen ' . $jId));
                                $aspeks = $aspekByJenis[$jId] ?? [];
                                $hasResult = isset($existingResults[$jId]);
                                $isOpen = ($pIdx === 0); // Komponen pertama dibuka secara default
                            ?>
                            <div class="card mb-3 border shadow-sm accordion-component-card" id="card-component-<?= $jId ?>">
                                <div class="card-header bg-white py-3" id="heading-<?= $jId ?>">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                                        <button class="btn btn-link text-dark text-left font-weight-bold p-0 d-flex align-items-center accordion-toggle-btn" 
                                                type="button" 
                                                data-toggle="collapse" 
                                                data-target="#collapse-<?= $jId ?>" 
                                                aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" 
                                                aria-controls="collapse-<?= $jId ?>">
                                            <i class="fas <?= $isOpen ? 'fa-chevron-down' : 'fa-chevron-right' ?> mr-2 text-primary chevron-icon"></i>
                                            <span class="component-title"><?= ($pIdx + 1) . '. ' . esc($namaJenis) ?></span>
                                            <span class="badge-status-<?= $jId ?> ml-2">
                                                <?php if ($hasResult): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Tersimpan</span>
                                                <?php else: ?>
                                                    <span class="badge badge-light border text-muted">Belum Diisi</span>
                                                <?php endif; ?>
                                            </span>
                                        </button>
                                        <div class="d-flex align-items-center mt-2 mt-md-0">
                                            <span class="badge badge-light border mr-2">
                                                <?= count($aspeks) ?> Aspek
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-save-single" data-form-id="form-<?= $jId ?>">
                                                <i class="fas fa-save mr-1"></i> Simpan
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="collapse-<?= $jId ?>" class="collapse <?= $isOpen ? 'show' : '' ?>" aria-labelledby="heading-<?= $jId ?>">
                                    <div class="card-body bg-light-soft pt-3">
                                        <!-- Bulk Edit Box -->
                                        <div class="alert alert-light border shadow-sm mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <label class="small font-weight-bold text-gray-700 mb-1">
                                                        <i class="fas fa-magic mr-1 text-primary"></i> Atur Cepat Skor:
                                                    </label>
                                                    <select class="form-control bulk-skor-select" id="bulk-skor-<?= $jId ?>" data-tab-id="<?= $jId ?>">
                                                        <option value="">-- Pilih Skor --</option>
                                                        <option value="4">4 - Sangat Baik</option>
                                                        <option value="3">3 - Baik</option>
                                                        <option value="2">2 - Cukup</option>
                                                        <option value="1">1 - Kurang</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5 mt-md-4 mt-2">
                                                    <button type="button" class="btn btn-outline-primary bulk-edit-apply-btn mr-1 mb-1" data-tab-id="<?= $jId ?>">
                                                        <i class="fas fa-check mr-1"></i> Terapkan ke Semua Aspek
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-reset-bulk-catatan mb-1" data-tab-id="<?= $jId ?>">
                                                        <i class="fas fa-undo mr-1"></i> Reset Catatan
                                                    </button>
                                                </div>
                                                <div class="col-md-3 text-md-right text-muted small mt-md-4 mt-2">
                                                    <i class="fas fa-info-circle mr-1"></i> Catatan dapat diedit atau di-reset.
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Assessment Form -->
                                        <form id="form-<?= $jId ?>" class="form-step-penilaian" data-group="perangkat">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="jadwal_id" value="<?= esc($schedule['id']) ?>">
                                            <input type="hidden" name="jenis_penilaian_id" value="<?= esc($jId) ?>">

                                            <div class="table-responsive bg-white rounded border mb-3">
                                                <table class="table table-bordered table-hover mb-0" width="100%">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th width="5%" class="text-center">No</th>
                                                            <th width="35%">Aspek Penilaian</th>
                                                            <th width="20%">Skor (1 - 4)</th>
                                                            <th width="40%">Catatan / Bukti Fisik</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($aspeks)): ?>
                                                            <?php $no = 1; ?>
                                                            <?php foreach ($aspeks as $aspek): ?>
                                                                <?php 
                                                                    $aspekId = $aspek['id'];
                                                                    $existingDetail = $existingDetails[$jId][$aspekId] ?? null;
                                                                    $currentSkor = $existingDetail['skor'] ?? '';
                                                                    $currentCatatan = $existingDetail['catatan'] ?? '';
                                                                ?>
                                                                <tr>
                                                                    <td class="text-center align-middle"><?= $no++ ?></td>
                                                                    <td class="align-middle">
                                                                        <strong><?= esc($aspek['nama_aspek']) ?></strong>
                                                                        <?php if (!empty($aspek['fokus_perilaku'])): ?>
                                                                            <div class="small text-muted mt-1">
                                                                                <i class="fas fa-bullseye text-info mr-1"></i><?= esc($aspek['fokus_perilaku']) ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <select name="skor_<?= $aspekId ?>" 
                                                                                class="form-control skala-dropdown" 
                                                                                data-aspek="<?= $aspekId ?>"
                                                                                data-nama-aspek="<?= esc($aspek['nama_aspek']) ?>"
                                                                                data-fokus-perilaku="<?= esc($aspek['fokus_perilaku'] ?? '') ?>"
                                                                                data-kategori="<?= esc($jenis['kategori_supervisi'] ?? '') ?>"
                                                                                data-tab-id="<?= $jId ?>" 
                                                                                required>
                                                                            <option value="">-- Pilih --</option>
                                                                            <option value="4" <?= $currentSkor == 4 ? 'selected' : '' ?>>4 - Sangat Baik</option>
                                                                            <option value="3" <?= $currentSkor == 3 ? 'selected' : '' ?>>3 - Baik</option>
                                                                            <option value="2" <?= $currentSkor == 2 ? 'selected' : '' ?>>2 - Cukup</option>
                                                                            <option value="1" <?= $currentSkor == 1 ? 'selected' : '' ?>>1 - Kurang</option>
                                                                        </select>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <textarea name="catatan_<?= $aspekId ?>" 
                                                                                  class="form-control catatan-field" 
                                                                                  data-aspek="<?= $aspekId ?>" 
                                                                                  data-nama-aspek="<?= esc($aspek['nama_aspek']) ?>"
                                                                                  data-fokus-perilaku="<?= esc($aspek['fokus_perilaku'] ?? '') ?>"
                                                                                  data-kategori="<?= esc($jenis['kategori_supervisi'] ?? '') ?>"
                                                                                  rows="3" 
                                                                                  placeholder="Tulis catatan atau bukti fisik..."><?= esc($currentCatatan) ?></textarea>
                                                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                                                            <button type="button" class="btn btn-xs btn-link text-muted p-0 btn-regen-catatan" data-aspek="<?= $aspekId ?>" title="Refresh catatan otomatis sesuai skor">
                                                                                <i class="fas fa-magic mr-1"></i>Catatan Otomatis
                                                                            </button>
                                                                            <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-clear-single-catatan" data-aspek="<?= $aspekId ?>" title="Kosongkan catatan aspek ini">
                                                                                <i class="fas fa-eraser mr-1"></i>Kosongkan
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center py-3 text-muted">
                                                                    Tidak ada aspek penilaian pada komponen ini.
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Rekomendasi Section -->
                                            <div class="form-group mb-3 bg-white p-3 rounded border">
                                                <label for="rekomendasi-<?= $jId ?>" class="font-weight-bold text-gray-800 small">
                                                    <i class="fas fa-comment-dots text-primary mr-1"></i> Rekomendasi Perbaikan:
                                                </label>
                                                <textarea name="rekomendasi" 
                                                          id="rekomendasi-<?= $jId ?>" 
                                                          data-jenis-id="<?= $jId ?>"
                                                          class="form-control rekomendasi-textarea" 
                                                          rows="3" 
                                                          placeholder="Tuliskan rekomendasi atau catatan tindak lanjut perbaikan..."><?= esc($existingResults[$jId]['rekomendasi'] ?? '') ?></textarea>
                                                <input type="hidden" name="rekomendasi_<?= $jId ?>" id="rekomendasi-hidden-<?= $jId ?>" value="<?= esc($existingResults[$jId]['rekomendasi'] ?? '') ?>">
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Step 1 Bottom Navigation -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 border-top mt-4">
                        <div class="text-muted small mb-2 mb-md-0">
                            <i class="fas fa-info-circle mr-1"></i> Data pada tahap ini otomatis disimpan saat Anda menekan tombol lanjut.
                        </div>
                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-outline-primary mr-2 btn-save-step-group" data-group="perangkat">
                                <i class="fas fa-save mr-1"></i> Simpan Tahap 1
                            </button>
                            <button type="button" class="btn btn-primary btn-wizard-next shadow-sm" data-next-step="step-observasi">
                                Lanjut ke Observasi Kelas <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TAHAP 2: OBSERVASI KELAS & KINERJA         -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="step-observasi" role="tabpanel" aria-labelledby="step-observasi-tab">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h5 class="font-weight-bold text-gray-800 m-0">
                                <i class="fas fa-chalkboard-teacher text-primary mr-1"></i> Tahap 2: Observasi Kelas & Praktik Kinerja
                            </h5>
                            <small class="text-muted">Penilaian fokus observasi praktik pembelajaran di dalam ruang kelas</small>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-expand-all mr-1" data-target="#accordionObservasi">
                                <i class="fas fa-angle-double-down mr-1"></i> Buka Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-collapse-all" data-target="#accordionObservasi">
                                <i class="fas fa-angle-double-up mr-1"></i> Tutup Semua
                            </button>
                        </div>
                    </div>

                    <!-- Accordion Observasi Kelas -->
                    <div class="accordion" id="accordionObservasi">
                        <?php foreach ($observasiList as $oIdx => $jenis): ?>
                            <?php 
                                $jId = $jenis['id'];
                                $namaJenis = $jenis['nama'] ?? ($jenis['nama_jenis'] ?? ('Komponen ' . $jId));
                                $aspeks = $aspekByJenis[$jId] ?? [];
                                $hasResult = isset($existingResults[$jId]);
                                
                                // Cek apakah komponen ini merupakan Fokus Utama Jadwal guru
                                $isFokus = ($fokusPmmId && $fokusPmmId == $jId);
                                
                                // Status buka: fokus utama otomatis terbuka; bila tidak ada fokus spesifik, item pertama yang terbuka
                                $isOpen = $isFokus || (!$fokusPmmId && $oIdx === 0);
                            ?>
                            <div class="card mb-3 border shadow-sm accordion-component-card <?= $isFokus ? 'border-left-warning' : '' ?>" id="card-component-<?= $jId ?>">
                                <div class="card-header bg-white py-3 <?= $isFokus ? 'bg-warning-soft' : '' ?>" id="heading-<?= $jId ?>">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                                        <button class="btn btn-link text-dark text-left font-weight-bold p-0 d-flex align-items-center accordion-toggle-btn" 
                                                type="button" 
                                                data-toggle="collapse" 
                                                data-target="#collapse-<?= $jId ?>" 
                                                aria-expanded="<?= $isOpen ? 'true' : 'false' ?>" 
                                                aria-controls="collapse-<?= $jId ?>">
                                            <i class="fas <?= $isOpen ? 'fa-chevron-down' : 'fa-chevron-right' ?> mr-2 text-primary chevron-icon"></i>
                                            <span class="component-title"><?= ($oIdx + 1) . '. ' . esc($namaJenis) ?></span>
                                            
                                            <?php if ($isFokus): ?>
                                                <span class="badge badge-warning text-dark ml-2 font-weight-bold shadow-sm">
                                                    <i class="fas fa-star mr-1"></i>Fokus Utama Jadwal
                                                </span>
                                            <?php endif; ?>

                                            <span class="badge-status-<?= $jId ?> ml-2">
                                                <?php if ($hasResult): ?>
                                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Tersimpan</span>
                                                <?php else: ?>
                                                    <span class="badge badge-light border text-muted">Belum Diisi</span>
                                                <?php endif; ?>
                                            </span>
                                        </button>
                                        <div class="d-flex align-items-center mt-2 mt-md-0">
                                            <span class="badge badge-light border mr-2">
                                                <?= count($aspeks) ?> Aspek
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-save-single" data-form-id="form-<?= $jId ?>">
                                                <i class="fas fa-save mr-1"></i> Simpan
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div id="collapse-<?= $jId ?>" class="collapse <?= $isOpen ? 'show' : '' ?>" aria-labelledby="heading-<?= $jId ?>">
                                    <div class="card-body bg-light-soft pt-3">
                                        <!-- Bulk Edit Box -->
                                        <div class="alert alert-light border shadow-sm mb-3">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <label class="small font-weight-bold text-gray-700 mb-1">
                                                        <i class="fas fa-magic mr-1 text-primary"></i> Atur Cepat Skor:
                                                    </label>
                                                    <select class="form-control bulk-skor-select" id="bulk-skor-<?= $jId ?>" data-tab-id="<?= $jId ?>">
                                                        <option value="">-- Pilih Skor --</option>
                                                        <option value="4">4 - Sangat Baik</option>
                                                        <option value="3">3 - Baik</option>
                                                        <option value="2">2 - Cukup</option>
                                                        <option value="1">1 - Kurang</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5 mt-md-4 mt-2">
                                                    <button type="button" class="btn btn-outline-primary bulk-edit-apply-btn mr-1 mb-1" data-tab-id="<?= $jId ?>">
                                                        <i class="fas fa-check mr-1"></i> Terapkan ke Semua Aspek
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-reset-bulk-catatan mb-1" data-tab-id="<?= $jId ?>">
                                                        <i class="fas fa-undo mr-1"></i> Reset Catatan
                                                    </button>
                                                </div>
                                                <div class="col-md-3 text-md-right text-muted small mt-md-4 mt-2">
                                                    <i class="fas fa-info-circle mr-1"></i> Catatan dapat diedit atau di-reset.
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Assessment Form -->
                                        <form id="form-<?= $jId ?>" class="form-step-penilaian" data-group="observasi">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="jadwal_id" value="<?= esc($schedule['id']) ?>">
                                            <input type="hidden" name="jenis_penilaian_id" value="<?= esc($jId) ?>">

                                            <div class="table-responsive bg-white rounded border mb-3">
                                                <table class="table table-bordered table-hover mb-0" width="100%">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th width="5%" class="text-center">No</th>
                                                            <th width="35%">Aspek Penilaian</th>
                                                            <th width="20%">Skor (1 - 4)</th>
                                                            <th width="40%">Catatan / Bukti Fisik</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($aspeks)): ?>
                                                            <?php $no = 1; ?>
                                                            <?php foreach ($aspeks as $aspek): ?>
                                                                <?php 
                                                                    $aspekId = $aspek['id'];
                                                                    $existingDetail = $existingDetails[$jId][$aspekId] ?? null;
                                                                    $currentSkor = $existingDetail['skor'] ?? '';
                                                                    $currentCatatan = $existingDetail['catatan'] ?? '';
                                                                ?>
                                                                <tr>
                                                                    <td class="text-center align-middle"><?= $no++ ?></td>
                                                                    <td class="align-middle">
                                                                        <strong><?= esc($aspek['nama_aspek']) ?></strong>
                                                                        <?php if (!empty($aspek['fokus_perilaku'])): ?>
                                                                            <div class="small text-muted mt-1">
                                                                                <i class="fas fa-bullseye text-info mr-1"></i><?= esc($aspek['fokus_perilaku']) ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <select name="skor_<?= $aspekId ?>" 
                                                                                class="form-control skala-dropdown" 
                                                                                data-aspek="<?= $aspekId ?>"
                                                                                data-nama-aspek="<?= esc($aspek['nama_aspek']) ?>"
                                                                                data-fokus-perilaku="<?= esc($aspek['fokus_perilaku'] ?? '') ?>"
                                                                                data-kategori="<?= esc($jenis['kategori_supervisi'] ?? '') ?>"
                                                                                data-tab-id="<?= $jId ?>" 
                                                                                required>
                                                                            <option value="">-- Pilih --</option>
                                                                            <option value="4" <?= $currentSkor == 4 ? 'selected' : '' ?>>4 - Sangat Baik</option>
                                                                            <option value="3" <?= $currentSkor == 3 ? 'selected' : '' ?>>3 - Baik</option>
                                                                            <option value="2" <?= $currentSkor == 2 ? 'selected' : '' ?>>2 - Cukup</option>
                                                                            <option value="1" <?= $currentSkor == 1 ? 'selected' : '' ?>>1 - Kurang</option>
                                                                        </select>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <textarea name="catatan_<?= $aspekId ?>" 
                                                                                  class="form-control catatan-field" 
                                                                                  data-aspek="<?= $aspekId ?>" 
                                                                                  data-nama-aspek="<?= esc($aspek['nama_aspek']) ?>"
                                                                                  data-fokus-perilaku="<?= esc($aspek['fokus_perilaku'] ?? '') ?>"
                                                                                  data-kategori="<?= esc($jenis['kategori_supervisi'] ?? '') ?>"
                                                                                  rows="3" 
                                                                                  placeholder="Tulis catatan atau bukti fisik..."><?= esc($currentCatatan) ?></textarea>
                                                                        <div class="d-flex justify-content-between align-items-center mt-1">
                                                                            <button type="button" class="btn btn-xs btn-link text-muted p-0 btn-regen-catatan" data-aspek="<?= $aspekId ?>" title="Refresh catatan otomatis sesuai skor">
                                                                                <i class="fas fa-magic mr-1"></i>Catatan Otomatis
                                                                            </button>
                                                                            <button type="button" class="btn btn-xs btn-link text-danger p-0 btn-clear-single-catatan" data-aspek="<?= $aspekId ?>" title="Kosongkan catatan aspek ini">
                                                                                <i class="fas fa-eraser mr-1"></i>Kosongkan
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td colspan="4" class="text-center py-3 text-muted">
                                                                    Tidak ada aspek penilaian pada komponen ini.
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Rekomendasi Section -->
                                            <div class="form-group mb-3 bg-white p-3 rounded border">
                                                <label for="rekomendasi-<?= $jId ?>" class="font-weight-bold text-gray-800 small">
                                                    <i class="fas fa-comment-dots text-primary mr-1"></i> Rekomendasi Perbaikan:
                                                </label>
                                                <textarea name="rekomendasi" 
                                                          id="rekomendasi-<?= $jId ?>" 
                                                          data-jenis-id="<?= $jId ?>"
                                                          class="form-control rekomendasi-textarea" 
                                                          rows="3" 
                                                          placeholder="Tuliskan rekomendasi atau catatan tindak lanjut perbaikan..."><?= esc($existingResults[$jId]['rekomendasi'] ?? '') ?></textarea>
                                                <input type="hidden" name="rekomendasi_<?= $jId ?>" id="rekomendasi-hidden-<?= $jId ?>" value="<?= esc($existingResults[$jId]['rekomendasi'] ?? '') ?>">
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Step 2 Bottom Navigation -->
                    <div class="d-flex flex-wrap justify-content-between align-items-center pt-3 border-top mt-4">
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-wizard-prev" data-prev-step="step-perangkat">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Perangkat Ajar
                            </button>
                        </div>
                        <div class="d-flex align-items-center mt-2 mt-md-0">
                            <button type="button" class="btn btn-outline-primary mr-2 btn-save-step-group" data-group="observasi">
                                <i class="fas fa-save mr-1"></i> Simpan Tahap 2
                            </button>
                            <button type="button" class="btn btn-primary btn-wizard-next shadow-sm" data-next-step="step-foto-pane">
                                Lanjut ke Foto & Selesai <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- TAHAP 3: FOTO BUKTI & PENYELESAIAN         -->
                <!-- ========================================== -->
                <div class="tab-pane fade" id="step-foto-pane" role="tabpanel" aria-labelledby="step-foto-tab">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <div>
                            <h5 class="font-weight-bold text-gray-800 m-0">
                                <i class="fas fa-camera-retro text-primary mr-1"></i> Tahap 3: Foto Bukti Supervisi & Penyelesaian
                            </h5>
                            <small class="text-muted">Upload dokumentasi visual pelaksanaan supervisi dan finalisasi rekapitulasi penilaian</small>
                        </div>
                        <span class="badge badge-info px-3 py-2" id="photoCountBadge">
                            <?= !empty($existingPhotos) ? count($existingPhotos) : 0 ?> / 5 Foto Terunggah
                        </span>
                    </div>

                    <!-- Form Upload Cepat Foto -->
                    <form id="quickPhotoUploadForm" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded border shadow-sm">
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <label for="quickPhotos" class="font-weight-bold small text-gray-700">Pilih Foto Dokumentasi:</label>
                                <input type="file" class="form-control-file" id="quickPhotos" name="photos[]" multiple accept="image/*" required>
                                <small class="text-muted">Maks. 3MB per file (JPG, JPEG, PNG, WEBP)</small>
                            </div>
                            <div class="col-md-5 mt-2 mt-md-0">
                                <label for="quickKeterangan" class="font-weight-bold small text-gray-700">Keterangan Foto (Opsional):</label>
                                <input type="text" class="form-control" id="quickKeterangan" name="keterangan" placeholder="Contoh: Dokumentasi proses pembelajaran di kelas">
                            </div>
                            <div class="col-md-2 mt-3 mt-md-0 text-md-right">
                                <button type="submit" id="btnQuickUpload" class="btn btn-primary btn-block shadow-sm">
                                    <i class="fas fa-upload mr-1"></i> Upload Foto
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Galeri Foto Terunggah -->
                    <div id="quickPhotoGallery" class="row mb-4">
                        <?php if (!empty($existingPhotos)): ?>
                            <?php foreach ($existingPhotos as $photo): ?>
                                <div class="col-md-3 col-sm-6 mb-3 photo-card-<?= $photo['id'] ?>">
                                    <div class="card border h-100 shadow-sm">
                                        <img src="<?= base_url($photo['file_path']) ?>" class="card-img-top foto-preview-item" alt="Foto Bukti" style="height: 160px; object-fit: cover; cursor: pointer;" onclick="window.open('<?= base_url($photo['file_path']) ?>', '_blank')">
                                        <div class="card-body p-2 d-flex flex-column justify-content-between">
                                            <p class="small text-muted mb-2 text-truncate" title="<?= esc($photo['keterangan'] ?? '-') ?>">
                                                <?= esc($photo['keterangan'] ?: 'Tanpa keterangan') ?>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="<?= base_url($photo['file_path']) ?>" target="_blank" class="btn btn-outline-info btn-xs py-1 px-2">
                                                    <i class="fas fa-search-plus"></i> Lihat
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-xs py-1 px-2 btn-delete-quick-photo" data-id="<?= $photo['id'] ?>">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-4 text-muted empty-photo-msg">
                                <i class="fas fa-images fa-3x mb-2 text-gray-300"></i>
                                <p class="mb-0">Belum ada foto dokumentasi yang diupload untuk supervisi ini.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Final Save & Complete Card -->
                    <div class="alert alert-success border-left-success shadow-sm p-4 mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="font-weight-bold text-success mb-1">
                                    <i class="fas fa-check-circle mr-1"></i> Siap Menyelesaikan Penilaian Supervisi?
                                </h5>
                                <p class="mb-1 text-gray-800">
                                    Klik tombol di samping untuk menyimpan seluruh skor secara terpadu, menghitung nilai akhir, menetapkan predikat kualifikasi, dan memperbarui rekapitulasi.
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle mr-1"></i> Anda akan otomatis dialihkan ke halaman <strong>Detail Hasil Supervisi</strong> setelah proses selesai.
                                </small>
                            </div>
                            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                <button type="button" id="btnCompleteSupervisi" class="btn btn-success btn-lg shadow" data-jadwal-id="<?= esc($schedule['id']) ?>">
                                    <i class="fas fa-check-double mr-1"></i> Simpan & Selesaikan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 Bottom Navigation -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                        <button type="button" class="btn btn-outline-secondary btn-wizard-prev" data-prev-step="step-observasi">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Observasi Kelas
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>

<style>
.bg-light-soft {
    background-color: #fbfbfd;
}
.bg-warning-soft {
    background-color: #fffdf5;
}
.wizard-step-tab {
    color: #4e73df;
    border-radius: 8px !important;
    transition: all 0.2s ease-in-out;
}
.wizard-step-tab.active {
    background-color: #4e73df !important;
    color: #fff !important;
    box-shadow: 0 4px 10px rgba(78, 115, 223, 0.25);
}
.wizard-step-tab.active .badge-primary {
    background-color: #fff !important;
    color: #4e73df !important;
}
.wizard-step-tab.active .badge-step-counter {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: #fff !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
}
.accordion-toggle-btn:hover {
    text-decoration: none;
}
.chevron-icon {
    transition: transform 0.2s ease;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= view('admin/penilaian/partials/scripts', get_defined_vars()) ?>
<?= $this->endSection() ?>
