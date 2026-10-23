<!-- Tab Pane Foto Bukti Supervisi -->
<div class="tab-pane fade <?= (isset($activeTab) && $activeTab == 'foto') ? 'show active' : '' ?>" 
     id="step-foto" 
     role="tabpanel" 
     aria-labelledby="step-foto-tab">
     
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-weight-bold text-gray-800 m-0">
            <?= ($totalTahap + 1) ?>. FOTO BUKTI SUPERVISI
            <span class="badge badge-info ml-2" id="photoCountBadge"><?= !empty($existingPhotos) ? count($existingPhotos) : 0 ?> / 5 Foto</span>
        </h5>
        <a href="<?= base_url($rolePrefix . '/foto-bukti/upload/' . $schedule['id']) ?>" class="btn btn-outline-primary">
            <i class="fas fa-external-link-alt mr-1"></i> Kelola di Halaman Khusus
        </a>
    </div>

    <!-- Form Upload Cepat -->
    <form id="quickPhotoUploadForm" enctype="multipart/form-data" class="mb-4 p-3 bg-light rounded border shadow-sm">
        <div class="row align-items-end">
            <div class="col-md-5">
                <label for="quickPhotos" class="font-weight-bold small text-gray-700">Pilih Foto Dokumentasi:</label>
                <input type="file" class="form-control-file" id="quickPhotos" name="photos[]" multiple accept="image/*" required>
                <small class="text-muted">Maks. 3MB per file (JPG, JPEG, PNG, WEBP)</small>
            </div>
            <div class="col-md-5 mt-2 mt-md-0">
                <label for="quickKeterangan" class="font-weight-bold small text-gray-700">Keterangan Foto (Opsional):</label>
                <input type="text" class="form-control form-control-sm" id="quickKeterangan" name="keterangan" placeholder="Contoh: Dokumentasi proses pembelajaran">
            </div>
            <div class="col-md-2 mt-3 mt-md-0 text-md-right">
                <button type="submit" id="btnQuickUpload" class="btn btn-primary btn-block shadow-sm">
                    <i class="fas fa-upload mr-1"></i> Upload Foto
                </button>
            </div>
        </div>
    </form>

    <!-- Galeri Foto Terunggah -->
    <div id="quickPhotoGallery" class="row">
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

    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary btn-prev-tab" data-prev-id="<?= !empty($jenisPenilaian) ? $jenisPenilaian[count($jenisPenilaian) - 1]['id'] : '' ?>">
            <i class="fas fa-arrow-left mr-1"></i> Tahap Sebelumnya
        </button>
        <button type="button" class="btn btn-success btn-trigger-complete shadow-sm">
            <i class="fas fa-check-double mr-1"></i> Selesaikan Supervisi
        </button>
    </div>
</div>
