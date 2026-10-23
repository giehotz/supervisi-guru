<?php
// Kalkulasi metrik ringkasan
$totalAspek   = !empty($aspek_penilaians) ? count($aspek_penilaians) : 0;
$totalJenis   = !empty($jenis_penilaians) ? count($jenis_penilaians) : 0;
$countByJenis = [];
$countPmm     = 0;
$countStandar = 0;

if (!empty($aspek_penilaians)) {
    foreach ($aspek_penilaians as $asp) {
        $jId = $asp['jenis_penilaian_id'] ?? 0;
        $countByJenis[$jId] = ($countByJenis[$jId] ?? 0) + 1;
        if (($asp['tipe_skala'] ?? '') === 'perilaku_pmm_1_3') {
            $countPmm++;
        } else {
            $countStandar++;
        }
    }
}

// Skema warna aksen dinamis per jenis penilaian
$colorPalette = [
    1 => ['bg' => 'bg-blue-lt', 'text' => 'text-blue', 'badge' => 'bg-blue'],
    2 => ['bg' => 'bg-green-lt', 'text' => 'text-green', 'badge' => 'bg-green'],
    3 => ['bg' => 'bg-purple-lt', 'text' => 'text-purple', 'badge' => 'bg-purple'],
    4 => ['bg' => 'bg-orange-lt', 'text' => 'text-orange', 'badge' => 'bg-orange'],
    5 => ['bg' => 'bg-azure-lt', 'text' => 'text-azure', 'badge' => 'bg-azure'],
    6 => ['bg' => 'bg-pink-lt', 'text' => 'text-pink', 'badge' => 'bg-pink'],
];
?>

<!-- KPI Stat Cards Banner -->
<div class="row row-cards mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded bg-primary-lt text-primary shadow-sm">
                            <i class="fas fa-clipboard-list fa-lg"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-dark h2 mb-0"><?= $totalAspek ?></div>
                        <div class="text-secondary small">Total Aspek Penilaian</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded bg-success-lt text-success shadow-sm">
                            <i class="fas fa-layer-group fa-lg"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-dark h2 mb-0"><?= $totalJenis ?></div>
                        <div class="text-secondary small">Komponen / Jenis Supervisi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded bg-azure-lt text-azure shadow-sm">
                            <i class="fas fa-star-half-alt fa-lg"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-dark h2 mb-0"><?= $countStandar ?></div>
                        <div class="text-secondary small">Skala Standar (1 - 4)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-md rounded bg-purple-lt text-purple shadow-sm">
                            <i class="fas fa-compass fa-lg"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-dark h2 mb-0"><?= $countPmm ?></div>
                        <div class="text-secondary small">Skala PMM / Kurmer (1 - 3)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Card Aspek Penilaian -->
<div class="card shadow-sm border-0">
    <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="card-title text-primary d-flex align-items-center m-0 font-weight-bold">
                <i class="fas fa-list-check me-2"></i> Butir Aspek Penilaian Supervisi
            </h3>
            <div class="text-muted small mt-1">Kelola indikator, urutan, dan skala instrumen observasi guru</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Dropdown Aksi Jenis -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle shadow-sm" type="button" id="menuCepatJenis" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-layer-group me-1 text-primary"></i> Kelola Jenis
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="menuCepatJenis">
                    <span class="dropdown-header">Navigasi Cepat:</span>
                    <a class="dropdown-item" href="<?= base_url('/admin/instrumen?tab=jenis') ?>" onclick="window.showTab('#jenis-tab'); return false;">
                        <i class="fas fa-list fa-fw me-2 text-primary"></i> Tab Jenis Penilaian
                    </a>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="window.showTab('#jenis-tab'); setTimeout(function(){ if(window.bootstrap && bootstrap.Modal){ bootstrap.Modal.getOrCreateInstance('#createJenisModal').show(); } else { $('#createJenisModal').modal('show'); } }, 350);">
                        <i class="fas fa-plus-circle fa-fw me-2 text-success"></i> Tambah Jenis Penilaian Baru
                    </a>
                </div>
            </div>

            <!-- Tombol Sinkron Aspek Penilaian -->
            <button type="button" class="btn btn-outline-info shadow-sm font-weight-bold" id="btnSyncAspek" title="Sinkronkan butir aspek baku K13 & Kurikulum Merdeka PMM">
                <i class="fas fa-sync-alt me-1"></i> Sinkron Aspek Penilaian
            </button>

            <!-- Tombol Tambah Aspek -->
            <button class="btn btn-primary shadow-sm font-weight-bold" data-bs-toggle="modal" data-bs-target="#createAspekModal">
                <i class="fas fa-plus me-1"></i> Tambah Aspek Penilaian
            </button>
        </div>
    </div>

    <!-- Quick Filter Pills Bar -->
    <style>
        #filterPillsContainer { display: grid; grid-template-columns: repeat(auto-fill, minmax(11rem, 1fr)); gap: .5rem; }
        #filterPillsContainer .filter-bar-label { grid-column: 1 / -1; }
        #filterPillsContainer .btn-filter-aspek { width: 100%; }
        #filterPillsContainer .btn-filter-aspek > .text-truncate { min-width: 0; }
        #filterPillsContainer #btnResetFilter { grid-column: 1 / -1; justify-self: end; }
    </style>
    <div class="p-3 bg-light border-bottom">
        <div id="filterPillsContainer" role="group" aria-label="Filter Komponen Penilaian">
            <span class="filter-bar-label small font-weight-bold text-secondary">
                <i class="fas fa-filter text-muted me-1"></i> Filter Komponen:
            </span>
            <button type="button" class="btn btn-sm btn-primary btn-filter-aspek d-flex align-items-center" data-jenis="" data-color="primary" aria-pressed="true" title="Semua Komponen">
                <i class="fas fa-check icon-filter-active me-1 d-none flex-shrink-0"></i>
                <span class="text-truncate text-start">Semua Komponen</span>
                <span class="badge pill-count bg-white text-primary ms-auto flex-shrink-0"><?= $totalAspek ?></span>
            </button>
            <?php if (!empty($jenis_penilaians)): ?>
                <?php foreach ($jenis_penilaians as $jenis): ?>
                    <?php
                        $jId = $jenis['id'];
                        $count = $countByJenis[$jId] ?? 0;
                        $namaClean = $jenis['nama_jenis'] ?? ($jenis['nama'] ?? '');
                        $pillColor = preg_replace('/^bg-|-lt$/', '', $colorPalette[$jId % 6 + 1]['bg'] ?? 'bg-secondary-lt') ?: 'secondary';
                    ?>
                    <button type="button" class="btn btn-sm btn-outline-<?= $pillColor ?> btn-filter-aspek d-flex align-items-center" data-jenis="<?= esc($namaClean, 'attr') ?>" data-color="<?= $pillColor ?>" aria-pressed="false" title="<?= esc($namaClean) ?>">
                        <i class="fas fa-check icon-filter-active me-1 d-none flex-shrink-0"></i>
                        <span class="text-truncate text-start"><?= esc($namaClean) ?></span>
                        <span class="badge pill-count bg-<?= $pillColor ?>-lt text-<?= $pillColor ?> ms-auto flex-shrink-0"><?= $count ?></span>
                    </button>
                <?php endforeach; ?>
            <?php endif; ?>
            <button type="button" class="btn btn-sm btn-outline-secondary ms-1" id="btnResetFilter" title="Reset Filter" style="display: none;">
                <i class="fas fa-undo me-1"></i> Reset
            </button>
        </div>
    </div>

    <!-- Table Responsive -->
    <div class="table-responsive">
        <table class="table table-vcenter table-hover card-table mb-0" id="dataTableAspek" width="100%">
            <thead class="thead-light">
                <tr>
                    <th class="w-1 text-center">No</th>
                    <th style="width: 25%;">Komponen Supervisi</th>
                    <th>Nama Butir Aspek Penilaian</th>
                    <th style="width: 14%;" class="text-center">Tipe Skala</th>
                    <th style="width: 8%;" class="text-center">Urutan</th>
                    <th class="w-1 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($aspek_penilaians as $aspek): ?>
                <?php
                    $jId = $aspek['jenis_penilaian_id'] ?? 0;
                    $palette = $colorPalette[$jId % 6 + 1] ?? ['bg' => 'bg-secondary-lt', 'text' => 'text-secondary'];
                    $isPmm = ($aspek['tipe_skala'] ?? '') === 'perilaku_pmm_1_3';
                ?>
                <tr>
                    <td class="text-center text-muted small align-middle"><?= $no++ ?></td>
                    <td class="align-middle">
                        <span class="badge <?= $palette['bg'] ?> <?= $palette['text'] ?> font-weight-bold px-2 py-1">
                            <i class="fas fa-layer-group me-1"></i> <?= esc($aspek['nama_jenis'] ?? '-') ?>
                        </span>
                    </td>
                    <td class="align-middle">
                        <div class="font-weight-bold text-dark mb-0" style="font-size: 0.95rem;">
                            <?= esc($aspek['nama_aspek'] ?? '-') ?>
                        </div>
                        <?php if (!empty($aspek['fokus_perilaku'])): ?>
                            <div class="small text-muted mt-1">
                                <i class="fas fa-bullseye text-indigo me-1"></i> <?= esc($aspek['fokus_perilaku']) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="text-center align-middle">
                        <?php if ($isPmm): ?>
                            <span class="badge bg-purple-lt text-purple px-2 py-1" title="Model Perilaku Kurikulum Merdeka (1 - 3)">
                                <i class="fas fa-compass me-1"></i> PMM (1 - 3)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-azure-lt text-azure px-2 py-1" title="Skala Standar Supervisi (1 - 4)">
                                <i class="fas fa-sliders-h me-1"></i> Standar (1 - 4)
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center align-middle font-weight-bold">
                        <span class="badge bg-light border text-dark px-2 py-1 font-weight-bold" title="Urutan ke-<?= esc($aspek['urutan'] ?? '0') ?>">
                            #<?= esc($aspek['urutan'] ?? '0') ?>
                        </span>
                    </td>
                    <td class="text-center align-middle">
                        <div class="btn-group btn-group-sm shadow-sm" role="group">
                            <button type="button" class="btn btn-warning text-dark font-weight-bold" data-bs-toggle="modal" data-bs-target="#editAspekModal<?= $aspek['id'] ?? '' ?>" title="Edit Aspek">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAspekModal<?= $aspek['id'] ?? '' ?>" title="Hapus Aspek">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editAspekModal<?= $aspek['id'] ?? '' ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content shadow-lg border-0">
                                    <form action="<?= base_url('/admin/instrumen/aspek-penilaian/' . ($aspek['id'] ?? '') . '/update') ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="modal-status bg-warning"></div>
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title font-weight-bold text-dark d-flex align-items-center">
                                                <i class="fas fa-edit text-warning me-2"></i> Edit Aspek Penilaian
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label required font-weight-bold text-secondary">Jenis / Komponen Penilaian</label>
                                                <select class="form-select" name="jenis_penilaian_id" required>
                                                    <option value="">Pilih Jenis Penilaian</option>
                                                    <?php foreach ($jenis_penilaians as $jenis): ?>
                                                        <option value="<?= $jenis['id'] ?>" <?= (isset($aspek['jenis_penilaian_id']) && $aspek['jenis_penilaian_id'] == $jenis['id']) ? 'selected' : '' ?>>
                                                            <?= esc($jenis['nama_jenis']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label required font-weight-bold text-secondary">Nama Butir Aspek Penilaian</label>
                                                <textarea class="form-control" name="nama_aspek" rows="3" required><?= esc($aspek['nama_aspek'] ?? '') ?></textarea>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required font-weight-bold text-secondary">Tipe Skala</label>
                                                    <select class="form-select" name="tipe_skala">
                                                        <option value="skor_1_4" <?= ($aspek['tipe_skala'] ?? '') !== 'perilaku_pmm_1_3' ? 'selected' : '' ?>>Standar (1 - 4)</option>
                                                        <option value="perilaku_pmm_1_3" <?= ($aspek['tipe_skala'] ?? '') === 'perilaku_pmm_1_3' ? 'selected' : '' ?>>PMM Kurmer (1 - 3)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label required font-weight-bold text-secondary">Nomor Urut</label>
                                                    <input type="number" class="form-control" name="urutan" value="<?= esc($aspek['urutan'] ?? '') ?>" required min="1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning font-weight-bold text-dark">
                                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteAspekModal<?= $aspek['id'] ?? '' ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                <div class="modal-content shadow-lg border-0">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    <div class="modal-status bg-danger"></div>
                                    <div class="modal-body text-center py-4">
                                        <div class="avatar avatar-lg rounded-circle bg-danger-lt text-danger mb-3 mx-auto shadow-sm">
                                            <i class="fas fa-trash-alt fa-lg"></i>
                                        </div>
                                        <h3 class="font-weight-bold text-dark">Hapus Aspek Penilaian?</h3>
                                        <div class="text-secondary small mt-2">
                                            Apakah Anda yakin ingin menghapus butir:
                                            <div class="font-weight-bold text-dark mt-1 px-2 py-1 bg-light rounded border">
                                                "<?= esc($aspek['nama_aspek'] ?? '') ?>"
                                            </div>
                                        </div>
                                        <div class="text-danger small mt-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Data yang terhapus tidak dapat dipulihkan.
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <div class="w-100 d-flex gap-2">
                                            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">Batal</button>
                                            <a href="<?= base_url('/admin/instrumen/aspek-penilaian/' . ($aspek['id'] ?? '') . '/delete') ?>" class="btn btn-danger w-50">
                                                <i class="fas fa-trash-alt me-1"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Aspek Baru -->
<div class="modal fade" id="createAspekModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <form action="<?= base_url('/admin/instrumen/aspek-penilaian/create') ?>" method="post" id="createAspekForm">
                <?= csrf_field() ?>
                <div class="modal-status bg-primary"></div>
                <div class="modal-header bg-light">
                    <h5 class="modal-title font-weight-bold text-primary d-flex align-items-center">
                        <i class="fas fa-plus-circle me-2"></i> Tambah Aspek Penilaian Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label required font-weight-bold text-secondary mb-0">Komponen / Jenis Penilaian</label>
                            <a href="javascript:void(0)" onclick="if(window.bootstrap && bootstrap.Modal){ bootstrap.Modal.getInstance(document.getElementById('createAspekModal'))?.hide(); } else { $('#createAspekModal').modal('hide'); } window.showTab('#jenis-tab');" class="small text-primary text-decoration-none">
                                <i class="fas fa-external-link-alt me-1"></i> Kelola Jenis
                            </a>
                        </div>
                        <select class="form-select" name="jenis_penilaian_id" id="jenisPenilaianCreate" required>
                            <option value="">-- Pilih Jenis Penilaian --</option>
                            <?php foreach ($jenis_penilaians as $jenis): ?>
                                <option value="<?= $jenis['id'] ?>"><?= esc($jenis['nama_jenis']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required font-weight-bold text-secondary">Nama Butir Aspek Penilaian</label>
                        <textarea class="form-control" name="nama_aspek" required rows="3" placeholder="Contoh: Kesesuaian tujuan pembelajaran dan alur tujuan pembelajaran (ATP)..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required font-weight-bold text-secondary">Tipe Skala</label>
                            <select class="form-select" name="tipe_skala">
                                <option value="skor_1_4" selected>Standar (Skala 1 - 4)</option>
                                <option value="perilaku_pmm_1_3">PMM Kurmer (Skala 1 - 3)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required font-weight-bold text-secondary">Nomor Urut</label>
                            <input type="number" class="form-control" name="urutan" id="urutanCreate" required min="1">
                            <small class="form-hint text-muted">Akan terisi otomatis saat memilih jenis penilaian</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary shadow-sm font-weight-bold">
                        <i class="fas fa-save me-1"></i> Simpan Aspek Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.filterAspekByJenis = function(jenisName) {
    if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#dataTableAspek')) {
        var dt = $('#dataTableAspek').DataTable();
        // Kolom 1 adalah Jenis / Komponen Supervisi
        dt.column(1).search(jenisName ? $.fn.dataTable.util.escapeRegex(jenisName) : '', true, false).draw();

        // Update tampilan pill aktif (warna pill mengikuti badge komponen di tabel)
        $('.btn-filter-aspek').each(function() {
            var $b = $(this);
            var color = $b.data('color') || 'primary';
            var isActive = ($b.attr('data-jenis') || '') === (jenisName || '');
            $b.toggleClass('active btn-' + color, isActive)
              .toggleClass('btn-outline-' + color, !isActive)
              .attr('aria-pressed', isActive ? 'true' : 'false')
              .find('.icon-filter-active').toggleClass('d-none', !isActive);
            $b.find('.pill-count').toggleClass('bg-white text-' + color, isActive)
              .toggleClass('bg-' + color + '-lt text-' + color, !isActive);
        });

        $('#btnResetFilter')[jenisName ? 'fadeIn' : 'fadeOut'](200);
    }
};

// Event listener tombol filter pill
function bindAspekFilterEvents() {
    $(document).off('click', '.btn-filter-aspek').on('click', '.btn-filter-aspek', function(e) {
        e.preventDefault();
        var jenis = $(this).attr('data-jenis');
        window.filterAspekByJenis(jenis);
    });

    // Reset filter
    $('#btnResetFilter').on('click', function() {
        window.filterAspekByJenis('');
    });

    // Handler Tombol Sinkron Aspek Penilaian
    $(document).off('click', '#btnSyncAspek').on('click', '#btnSyncAspek', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Sinkronisasi Aspek Penilaian?',
            html: '<div class="text-muted small text-start">Sistem akan menyinkronkan <strong>79 butir aspek standar</strong> (14 komponen K13 & Kurikulum Merdeka PMM).<br><br>' +
                  '<ul class="mb-0 ps-3">' +
                  '<li>Menambahkan butir aspek baku yang belum terdaftar.</li>' +
                  '<li>Memperbarui fokus perilaku, rubrik panduan, dan tipe skala standar.</li>' +
                  '<li><strong class="text-success">Aman:</strong> Tidak menghapus data atau riwayat supervisi yang sudah ada.</li>' +
                  '</ul></div>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#206bc4',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-sync-alt me-1"></i> Mulai Sinkronisasi',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menyinkronkan Butir Aspek...',
                    text: 'Mohon tunggu beberapa saat, sistem sedang memproses data.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('admin/aspek-penilaian/sync') ?>',
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: {
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(res) {
                        if (res.token) {
                            $('input[name="<?= csrf_token() ?>"]').val(res.token);
                        }
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sinkronisasi Selesai!',
                                html: '<p class="mb-2">' + res.message + '</p>' +
                                      '<div class="badge bg-success-lt text-success px-3 py-2">' +
                                      'Baru ditambahkan: ' + (res.inserted || 0) + ' | Diperbarui: ' + (res.updated || 0) +
                                      '</div>',
                                confirmButtonColor: '#206bc4',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Sinkronisasi',
                                text: res.message || 'Terjadi kesalahan saat memproses sinkronisasi.',
                                confirmButtonColor: '#d63939'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Sistem',
                            text: 'Gagal terhubung ke server: ' + (xhr.responseJSON?.message || error || 'Koneksi terputus'),
                            confirmButtonColor: '#d63939'
                        });
                    }
                });
            }
        });
    });
}

// jQuery dimuat di akhir body (partials/scripts), script partial dieksekusi sebelum itu
if (window.jQuery) {
    bindAspekFilterEvents();
} else {
    document.addEventListener('DOMContentLoaded', bindAspekFilterEvents);
}
</script>
