<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading & Filter -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Jadwal Supervisi Akademik</h1>
            <p class="text-muted small mb-0">Daftar agenda observasi kinerja dan pembinaan pembelajaran guru</p>
        </div>
        <form action="<?= base_url('guru/jadwal') ?>" method="get" class="form-inline mt-3 mt-sm-0">
            <label for="tahun_ajar_id" class="mr-2 small font-weight-bold text-muted">Tahun Ajaran:</label>
            <select name="tahun_ajar_id" id="tahun_ajar_id" class="form-control form-control-sm shadow-sm" onchange="this.form.submit()">
                <option value="all" <?= ($selectedTahunAjar === 'all') ? 'selected' : '' ?>>Semua Tahun Ajaran</option>
                <?php foreach ($tahun_ajars as $ta): ?>
                    <option value="<?= $ta['id'] ?>" <?= ((string)$selectedTahunAjar === (string)$ta['id']) ? 'selected' : '' ?>>
                        <?= esc($ta['tahun_ajar']) ?> (Semester <?= esc($ta['semester']) ?>) <?= (!empty($activeTahun) && $activeTahun['id'] == $ta['id']) ? '★ Aktif' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Data Jadwal -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-calendar-alt mr-1"></i> Agenda Observasi Kinerja Anda
            </h6>
            <span class="badge badge-light border text-muted small">Total: <?= count($jadwal) ?> Jadwal</span>
        </div>
        <div class="card-body">
            <?php if (empty($jadwal)): ?>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-1"></i> Tidak ada jadwal supervisi yang tersedia untuk filter tahun ajaran yang dipilih.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Hari / Tanggal</th>
                                <th>Jam Ke-</th>
                                <th>Kelas / Fase</th>
                                <th>Mata Pelajaran</th>
                                <th>Tahun Ajaran</th>
                                <th>Supervisor</th>
                                <th>Tahapan</th>
                                <th>Status Form A</th>
                                <th style="width: 22%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($jadwal as $item): 
                                $canRtl = $jadwalModel->canAccessPascaObservasi($item['status'], true);
                                $statusFormA = $item['status_form_a'] ?? '';
                            ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                    <td>
                                        <strong class="d-block text-dark"><?= format_hari_indonesia($item['tanggal_supervisi']) ?></strong>
                                        <small class="text-muted"><?= date('d M Y', strtotime($item['tanggal_supervisi'])) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border"><?= esc($item['jam_ke'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <strong><?= esc($item['nama_kelas'] ?? ($item['kelas'] ?? '-')) ?></strong>
                                        <?php if (!empty($item['kelas_fase'])): ?>
                                            <span class="badge badge-info ml-1">Fase <?= esc($item['kelas_fase']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($item['mata_pelajaran']) ?></td>
                                    <td class="small">
                                        <?= esc($item['tahun_ajar'] ?? '-') ?>
                                        <span class="text-muted d-block">Sem. <?= esc($item['semester'] ?? '-') ?></span>
                                    </td>
                                    <td>
                                        <strong class="text-dark small"><i class="fas fa-user-tie text-primary mr-1"></i><?= esc($item['supervisor_name'] ?? 'Supervisor') ?></strong>
                                    </td>
                                    <td>
                                        <?php if ($item['status'] == 'Terjadwal' || $item['status'] == 'dijadwalkan'): ?>
                                            <span class="badge badge-info px-2 py-1"><i class="fas fa-calendar-check mr-1"></i> Terjadwal</span>
                                        <?php elseif ($item['status'] == 'pra_observasi'): ?>
                                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-clipboard-list mr-1"></i> Pra-Observasi</span>
                                        <?php elseif ($item['status'] == 'observasi'): ?>
                                            <span class="badge badge-primary px-2 py-1"><i class="fas fa-chalkboard-teacher mr-1"></i> Observasi Kelas</span>
                                        <?php elseif ($item['status'] == 'pasca_observasi' || $item['status'] == 'tindak_lanjut'): ?>
                                            <span class="badge badge-info px-2 py-1"><i class="fas fa-tasks mr-1"></i> Tindak Lanjut</span>
                                        <?php elseif ($item['status'] == 'Selesai' || $item['status'] == 'selesai'): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i> Selesai</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1"><?= esc($item['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($statusFormA === 'disepakati'): ?>
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i> Disepakati</span>
                                        <?php elseif ($statusFormA === 'diajukan'): ?>
                                            <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Diajukan</span>
                                        <?php elseif ($statusFormA === 'draft_guru'): ?>
                                            <span class="badge badge-secondary px-2 py-1"><i class="fas fa-edit mr-1"></i> Draf Guru</span>
                                        <?php else: ?>
                                            <span class="badge badge-light border text-muted px-2 py-1"><i class="fas fa-minus-circle mr-1"></i> Belum Diisi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1 align-items-center">
                                            <!-- Tombol Form A -->
                                            <?php if ($statusFormA === 'disepakati'): ?>
                                                <a href="<?= base_url('guru/pra-observasi/' . $item['id']) ?>" class="btn btn-sm btn-outline-success" title="Lihat Formulir A yang telah disepakati">
                                                    <i class="fas fa-clipboard-check mr-1"></i> Form A
                                                </a>
                                            <?php elseif ($statusFormA === 'diajukan'): ?>
                                                <a href="<?= base_url('guru/pra-observasi/' . $item['id']) ?>" class="btn btn-sm btn-outline-warning" title="Formulir A sedang ditelaah supervisor">
                                                    <i class="fas fa-clock mr-1"></i> Form A
                                                </a>
                                            <?php elseif ($statusFormA === 'draft_guru'): ?>
                                                <a href="<?= base_url('guru/pra-observasi/' . $item['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Lanjutkan pengisian draf Formulir A">
                                                    <i class="fas fa-edit mr-1"></i> Form A
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= base_url('guru/pra-observasi/' . $item['id']) ?>" class="btn btn-sm btn-primary" title="Isi Formulir A Diskusi Persiapan">
                                                    <i class="fas fa-plus mr-1"></i> Form A
                                                </a>
                                            <?php endif; ?>

                                            <!-- Tombol Modul Perangkat Ajar -->
                                            <a href="<?= base_url('guru/dokumen-ajar/manage/' . $item['id']) ?>" class="btn btn-sm btn-outline-warning" title="Unggah / Kelola Perangkat Ajar">
                                                <i class="fas fa-folder-open mr-1"></i> Modul
                                                <?php if (!empty($item['total_dokumen']) && $item['total_dokumen'] > 0): ?>
                                                    <span class="badge badge-warning text-dark ml-1"><?= $item['total_dokumen'] ?></span>
                                                <?php endif; ?>
                                            </a>

                                            <!-- Tombol RTL (Formulir C & D) dengan State Guarding -->
                                            <?php if ($canRtl): ?>
                                                <a href="<?= base_url('guru/tindak-lanjut/' . $item['id']) ?>" class="btn btn-sm btn-outline-info" title="Formulir C & D: Tindak Lanjut & Refleksi">
                                                    <i class="fas fa-tasks mr-1"></i> RTL
                                                </a>
                                            <?php else: ?>
                                                <span class="d-inline-block" tabindex="0" data-toggle="tooltip" data-placement="top" title="RTL terbuka setelah observasi kelas selesai dinilai supervisor">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary disabled" style="pointer-events: none; opacity: 0.6;">
                                                        <i class="fas fa-lock mr-1"></i> RTL
                                                    </button>
                                                </span>
                                            <?php endif; ?>

                                            <!-- Tombol Hasil Observasi -->
                                            <?php if (in_array(strtolower($item['status']), ['observasi', 'pasca_observasi', 'tindak_lanjut', 'selesai'])): ?>
                                                <a href="<?= base_url('guru/hasil/' . $item['id']) ?>" class="btn btn-sm btn-primary" title="Lihat Hasil Observasi Kelas">
                                                    <i class="fas fa-eye mr-1"></i> Hasil
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "order": [[1, "asc"]]
            });
        }
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<?= $this->endSection() ?>
