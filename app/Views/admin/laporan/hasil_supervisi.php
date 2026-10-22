<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                <i class="fas fa-chart-line text-primary mr-2"></i>Laporan Hasil Supervisi
            </h1>
            <p class="text-muted small mb-0">Rekapitulasi nilai evaluasi dan pencapaian supervisi proses pembelajaran guru.</p>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-times-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filter Card -->
    <div class="card shadow-sm mb-4 border-left-primary">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-sliders-h mr-1"></i> Filter Data Supervisi
            </h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/laporan/hasil-supervisi') ?>">
                <div class="row align-items-end">
                    <div class="col-md-5 mb-2 mb-md-0">
                        <label for="tahun_ajar_id" class="font-weight-bold text-gray-700 small mb-1">Tahun Ajaran:</label>
                        <select name="tahun_ajar_id" id="tahun_ajar_id" class="form-control form-control font-weight-bold">
                            <option value="all" <?= ($tahun_ajar_id === 'all') ? 'selected' : '' ?>>Semua Tahun Ajaran</option>
                            <?php foreach ($tahun_ajars as $tahun): ?>
                                <option value="<?= $tahun['id'] ?>" <?= ((string)$tahun_ajar_id === (string)$tahun['id']) ? 'selected' : '' ?>>
                                    <?= esc($tahun['tahun_ajar']) ?> - <?= esc($tahun['semester']) ?> <?= ($tahun['status_aktif'] === 'Aktif') ? '(Aktif)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label for="status" class="font-weight-bold text-gray-700 small mb-1">Status Pelaksanaan:</label>
                        <select name="status" id="status" class="form-control font-weight-bold">
                            <option value="">Semua Status</option>
                            <option value="Terjadwal" <?= (isset($status) && $status == 'Terjadwal') ? 'selected' : '' ?>>Terjadwal</option>
                            <option value="Selesai" <?= (isset($status) && $status == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="d-none d-md-block font-weight-bold text-gray-700 small mb-1">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-sm btn-block font-weight-bold shadow-sm">
                            <i class="fas fa-filter mr-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>

                <?php if (!empty($activeTahun) && (string)$tahun_ajar_id === (string)$activeTahun['id']): ?>
                    <div class="mt-2 small text-success font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Menampilkan Tahun Ajaran Aktif (<?= esc($activeTahun['tahun_ajar']) ?> - <?= esc($activeTahun['semester']) ?>)
                    </div>
                <?php endif; ?>
            </form>
            
            <?php
            $exportParams = [];
            if (!empty($tahun_ajar_id)) {
                $exportParams['tahun_ajar_id'] = $tahun_ajar_id;
            }
            if (!empty($status)) {
                $exportParams['status'] = $status;
            }
            $exportQuery = !empty($exportParams) ? '?' . http_build_query($exportParams) : '';
            ?>

            <!-- Export Buttons Strip -->
            <hr class="my-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="small font-weight-bold text-gray-700 mb-2 mb-md-0">
                    <i class="fas fa-file-export text-primary mr-1"></i> Ekspor Laporan Rekap:
                </div>
                <div class="d-flex flex-wrap" style="gap: 8px;">
                    <a href="<?= base_url('admin/laporan/hasil-supervisi/pdf') ?><?= $exportQuery ?>" 
                       class="btn btn-sm btn-pill btn-danger shadow-sm font-weight-bold" target="_blank" title="Unduh rekap nilai format PDF"> 
                        <i class="fas fa-file-pdf mr-1"></i> Rekap PDF
                    </a>
                    <a href="<?= base_url('admin/laporan/hasil-supervisi/excel') ?><?= $exportQuery ?>" 
                       class="btn btn-sm btn-pill btn-success shadow-sm font-weight-bold" title="Unduh rekap penilaian format Excel">
                        <i class="fas fa-file-excel mr-1"></i> Rekap Excel
                    </a>
                    <a href="<?= base_url('admin/laporan/hasil-supervisi/batch-excel-all') ?><?= $exportQuery ?>" 
                       class="btn btn-sm btn-pill btn-info shadow-sm font-weight-bold" title="Unduh detail seluruh nilai instrumen guru">
                        <i class="fas fa-table mr-1"></i> Detail Semua Guru (Excel)
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center mb-1 mb-md-0">
                <h6 class="m-0 font-weight-bold text-primary mr-2">
                    <i class="fas fa-list-alt mr-1"></i> Data Hasil Supervisi
                </h6>
                <span class="badge badge-light border text-gray-700 px-2 py-1 small">
                    Total: <strong><?= !empty($jadwals) ? count($jadwals) : 0 ?></strong> Data
                </span>
            </div>
            <button type="button" id="batchExcelBtn" class="btn btn-sm btn-pill btn-success shadow-sm font-weight-bold" disabled>
                <i class="fas fa-file-excel mr-1"></i> Export Pilihan Excel (<span id="selectedCountText">0</span>)
            </button>
        </div>
        <div class="card-body p-0">
            <form id="batchExcelForm" method="post" action="<?= base_url('admin/laporan/hasil-supervisi/batch-excel') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="tahun_ajar_id" value="<?= esc($tahun_ajar_id ?? '') ?>">
                <input type="hidden" name="status" value="<?= esc($status ?? '') ?>">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="3%" class="text-center align-middle">
                                    <input type="checkbox" id="checkAll" title="Pilih Semua yang Selesai">
                                </th>
                                <th width="4%" class="text-center align-middle">No</th>
                                <th width="12%" class="align-middle">Tahun Ajaran</th>
                                <th class="align-middle">Nama Guru</th>
                                <th width="15%" class="align-middle">Mata Pelajaran</th>
                                <th width="8%" class="text-center align-middle">Kelas</th>
                                <th width="12%" class="align-middle">Waktu Pelaksanaan</th>
                                <th width="9%" class="text-center align-middle">Status</th>
                                <th width="10%" class="text-center align-middle">Nilai Akhir</th>
                                <th width="12%" class="text-center align-middle">Ketercapaian</th>
                                <th width="12%" class="text-center align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($jadwals)): ?>
                                <?php $no = 1; foreach ($jadwals as $jadwal): ?>
                                    <tr>
                                        <td class="text-center align-middle">
                                            <?php if ($jadwal['status'] == 'Selesai'): ?>
                                                <input type="checkbox" class="checkbox-item" name="selected_ids[]" value="<?= $jadwal['id'] ?>">
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle font-weight-bold text-gray-700"><?= $no++ ?></td>
                                        <td class="align-middle small font-weight-bold text-gray-800">
                                            <?= esc($jadwal['tahun_ajar']) ?>
                                            <div class="small font-weight-normal text-muted"><?= esc($jadwal['semester']) ?></div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold text-gray-900"><?= esc($jadwal['nama_guru']) ?></div>
                                            <?php if (!empty($jadwal['nip']) && $jadwal['nip'] !== '-'): ?>
                                                <small class="text-muted d-block">NIP: <?= esc($jadwal['nip']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle text-gray-800">
                                            <span class="badge badge-light border text-gray-800 font-weight-normal px-2 py-1">
                                                <?= esc($jadwal['mata_pelajaran']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center align-middle font-weight-bold text-gray-800">
                                            <?= esc($jadwal['kelas']) ?>
                                        </td>
                                        <td class="align-middle">
                                            <div class="text-nowrap font-weight-bold text-gray-800">
                                                <i class="far fa-calendar-alt text-primary mr-1"></i>
                                                <?= date('d M Y', strtotime($jadwal['tanggal_supervisi'])) ?>
                                            </div>
                                            <?php if (!empty($jadwal['hari'])): ?>
                                                <div class="small text-muted">
                                                    <?= esc($jadwal['hari']) ?>
                                                    <?= !empty($jadwal['jam_ke']) ? '· Jam ke-' . esc($jadwal['jam_ke']) : '' ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if ($jadwal['status'] == 'Terjadwal'): ?>
                                                <span class="badge badge-pill badge-info px-2 py-1">
                                                    <i class="fas fa-clock mr-1"></i> Terjadwal
                                                </span>
                                            <?php elseif ($jadwal['status'] == 'Selesai'): ?>
                                                <span class="badge badge-pill badge-success px-2 py-1">
                                                    <i class="fas fa-check-circle mr-1"></i> Selesai
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-pill badge-secondary px-2 py-1"><?= esc($jadwal['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if (isset($jadwal['hasil']['nilai_akhir']) && !is_null($jadwal['hasil']['nilai_akhir'])): ?>
                                                <span class="badge badge-pill badge-primary font-weight-bold px-3 py-1 shadow-sm" style="font-size: 0.9rem; font-variant-numeric: tabular-nums;">
                                                    <?= number_format($jadwal['hasil']['nilai_akhir'], 2) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-pill badge-light border text-muted px-2 py-1 small">
                                                    Belum Dinilai
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if (isset($jadwal['hasil']['ketercapaian']) && !is_null($jadwal['hasil']['ketercapaian'])): ?>
                                                <?php
                                                    $ket = $jadwal['hasil']['ketercapaian'];
                                                    $badgeClass = 'badge-secondary';
                                                    $ketIcon = 'fa-check';
                                                    if ($ket == 'Baik Sekali') {
                                                        $badgeClass = 'badge-success';
                                                        $ketIcon = 'fa-star text-warning';
                                                    } elseif ($ket == 'Baik') {
                                                        $badgeClass = 'badge-info';
                                                        $ketIcon = 'fa-thumbs-up';
                                                    } elseif ($ket == 'Cukup') {
                                                        $badgeClass = 'badge-warning text-dark';
                                                        $ketIcon = 'fa-minus';
                                                    } elseif ($ket == 'Kurang') {
                                                        $badgeClass = 'badge-danger';
                                                        $ketIcon = 'fa-exclamation-triangle';
                                                    }
                                                ?>
                                                <span class="badge badge-pill <?= $badgeClass ?> font-weight-bold px-2 py-1 shadow-sm">
                                                    <i class="fas <?= $ketIcon ?> mr-1"></i> <?= esc($ket) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-pill badge-light border text-muted px-2 py-1 small">
                                                    -
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php if ($jadwal['status'] == 'Selesai'): ?>
                                                <div class="d-inline-flex" style="gap: 4px;">
                                                    <a href="<?= base_url('admin/laporan/hasil-supervisi/detail/' . $jadwal['id']) ?>" 
                                                       class="btn btn-sm btn-pill btn-primary px-2 font-weight-bold shadow-sm" title="Lihat Rekap Nilai">
                                                        <i class="fas fa-eye mr-1"></i> Detail
                                                    </a>
                                                    <a href="<?= base_url('admin/penilaian/form/' . $jadwal['id']) ?>" 
                                                       class="btn btn-sm btn-pill btn-outline-warning text-dark font-weight-bold px-2 shadow-sm" title="Edit Hasil Penilaian">
                                                        <i class="fas fa-edit mr-1"></i> Edit
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge badge-pill badge-light border text-muted px-2 py-1 small" title="Supervisi belum selesai dinilai">
                                                    <i class="fas fa-hourglass-half mr-1 text-warning"></i> Menunggu
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-clipboard-list fa-3x text-gray-300 mb-3 d-block"></i>
                                            <h6 class="font-weight-bold text-gray-700 mb-1">Belum Ada Data Hasil Supervisi</h6>
                                            <p class="small text-muted mb-0">Tidak ditemukan data supervisi untuk tahun ajaran atau status yang dipilih.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    function updateBatchButtonState() {
        const checkedCount = $('.checkbox-item:checked').length;
        $('#batchExcelBtn').prop('disabled', checkedCount === 0);
        $('#selectedCountText').text(checkedCount);
    }

    // Check all checkboxes
    $('#checkAll').on('change', function() {
        $('.checkbox-item').prop('checked', this.checked);
        updateBatchButtonState();
    });
    
    // Update batch button state when individual checkboxes are clicked
    $(document).on('change', '.checkbox-item', function() {
        updateBatchButtonState();
        
        const totalItems = $('.checkbox-item').length;
        const checkedItems = $('.checkbox-item:checked').length;
        $('#checkAll').prop('checked', totalItems > 0 && totalItems === checkedItems);
    });
    
    // Handle batch Excel export
    $('#batchExcelBtn').on('click', function() {
        const checkedCount = $('.checkbox-item:checked').length;
        if (checkedCount === 0) {
            alert('Silakan pilih setidaknya satu jadwal untuk diexport.');
            return;
        }
        
        // Submit the form
        $('#batchExcelForm').submit();
    });

    updateBatchButtonState();
});
</script>
<?= $this->endSection(); ?>