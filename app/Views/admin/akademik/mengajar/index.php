<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">
                <i class="fas fa-calendar-week text-primary mr-2"></i>Jadwal KBM & Beban Mengajar
            </h1>
            <p class="text-muted small mb-0">Manajemen jadwal pelajaran mingguan, plotting guru mengajar, dan monitoring beban jam tatap muka (JTM).</p>
        </div>
        <div class="mt-3 mt-sm-0 d-flex flex-wrap">
            <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold px-3 text-white" style="color: #ffffff !important;" data-toggle="modal" data-target="#modalAddJadwal" onclick="resetFormAdd()">
                <i class="fas fa-plus-circle mr-1 text-white"></i> Tambah Plotting Mengajar
            </button>
        </div>
    </div>

    <!-- Flash Messages (Single notification container) -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-lg mr-2 text-success"></i>
                <div><?= session()->getFlashdata('success') ?></div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-left-danger" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-lg mr-2 text-danger"></i>
                <div><?= session()->getFlashdata('error') ?></div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Filter Global Card (Tahun Ajaran) -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-2 px-3 bg-light rounded d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <label class="small font-weight-bold text-gray-700 mb-0 mr-2 text-nowrap">
                    <i class="fas fa-calendar-alt text-primary mr-1"></i> Tahun Pelajaran:
                </label>
                <form method="get" action="<?= base_url('admin/akademik/mengajar') ?>" id="formTahunFilter" class="form-inline">
                    <input type="hidden" name="tab" value="<?= esc($currentTab) ?>">
                    <?php if ($currentTab === 'kelas' && !empty($selectedKelasId)): ?>
                        <input type="hidden" name="kelas_id" value="<?= esc($selectedKelasId) ?>">
                    <?php endif; ?>
                    <?php if ($currentTab === 'guru' && !empty($selectedGuruId)): ?>
                        <input type="hidden" name="guru_id" value="<?= esc($selectedGuruId) ?>">
                    <?php endif; ?>
                    <select class="custom-select custom-select-sm font-weight-bold border-primary" name="tahun_ajar_id" onchange="this.form.submit()">
                        <?php foreach ($tahun_ajars as $th): ?>
                            <option value="<?= $th['id'] ?>" <?= (string)$selectedTahunId === (string)$th['id'] ? 'selected' : '' ?>>
                                TP <?= esc($th['tahun_ajar']) ?> (Semester <?= esc($th['semester']) ?>) <?= ($th['status_aktif'] ?? '') === 'Aktif' ? '★ Aktif' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <div class="small text-muted font-italic">
                Total Jadwal Terdaftar: <strong class="text-primary font-weight-bold"><?= $totalJadwal ?></strong> sesi mata pelajaran
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs nav-justified font-weight-bold mb-4 shadow-sm bg-white rounded-top" id="jadwalTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link py-3 <?= $currentTab === 'kelas' ? 'active text-primary border-bottom-0' : 'text-secondary' ?>" 
               href="<?= base_url('admin/akademik/mengajar?tab=kelas&tahun_ajar_id=' . $selectedTahunId . ($selectedKelasId ? '&kelas_id=' . $selectedKelasId : '')) ?>">
                <i class="fas fa-chalkboard mr-1"></i> 1. Matriks Jadwal per Kelas
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-3 <?= $currentTab === 'guru' ? 'active text-primary border-bottom-0' : 'text-secondary' ?>" 
               href="<?= base_url('admin/akademik/mengajar?tab=guru&tahun_ajar_id=' . $selectedTahunId . ($selectedGuruId ? '&guru_id=' . $selectedGuruId : '')) ?>">
                <i class="fas fa-user-tie mr-1"></i> 2. Matriks Jadwal per Guru
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-3 <?= $currentTab === 'rekap' ? 'active text-primary border-bottom-0' : 'text-secondary' ?>" 
               href="<?= base_url('admin/akademik/mengajar?tab=rekap&tahun_ajar_id=' . $selectedTahunId) ?>">
                <i class="fas fa-chart-pie mr-1"></i> 3. Rekap Beban Mengajar (JTM)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link py-3 <?= $currentTab === 'tabel' ? 'active text-primary border-bottom-0' : 'text-secondary' ?>" 
               href="<?= base_url('admin/akademik/mengajar?tab=tabel&tahun_ajar_id=' . $selectedTahunId) ?>">
                <i class="fas fa-list-ul mr-1"></i> 4. Semua Data Plotting (Tabel)
            </a>
        </li>
    </ul>

    <!-- TAB 1: MATRIKS JADWAL PER KELAS -->
    <?php if ($currentTab === 'kelas'): ?>
        <div class="card shadow border-0 mb-4">
            <div class="card-header py-3 bg-white d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <span class="mr-2 font-weight-bold text-gray-700 small"><i class="fas fa-school text-primary mr-1"></i> Pilih Kelas / Rombel:</span>
                    <form method="get" action="<?= base_url('admin/akademik/mengajar') ?>" class="form-inline">
                        <input type="hidden" name="tab" value="kelas">
                        <input type="hidden" name="tahun_ajar_id" value="<?= esc($selectedTahunId) ?>">
                        <select name="kelas_id" class="custom-select custom-select-sm font-weight-bold" onchange="this.form.submit()">
                            <?php foreach ($kelases as $kls): ?>
                                <option value="<?= $kls['id'] ?>" <?= $selectedKelasId == $kls['id'] ? 'selected' : '' ?>>
                                    Kelas <?= esc($kls['nama_kelas']) ?> (Tingkat <?= esc($kls['tingkat'] ?? '-') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <div>
                    <?php if ($selectedKelasId): ?>
                        <a href="<?= base_url('admin/akademik/mengajar/cetak-kelas/' . $selectedKelasId . '?tahun_ajar_id=' . $selectedTahunId) ?>" 
                           target="_blank" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                            <i class="fas fa-print mr-1"></i> Cetak Jadwal Kelas
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!$selectedKelasId || empty($kelases)): ?>
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-info-circle fa-2x mb-2 text-info"></i>
                        <p class="mb-0">Belum ada kelas yang terdaftar pada tahun ajaran ini. Silakan tambahkan data kelas terlebih dahulu.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 text-center align-middle" style="min-width: 900px;">
                            <thead class="bg-gradient-primary text-white">
                                <tr>
                                    <th style="width: 140px;" class="py-2">Jam Ke / Waktu</th>
                                    <?php 
                                    $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    foreach ($hariList as $h): ?>
                                        <th class="py-2" style="width: 14%;"><?= $h ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sesiJam as $jamKe => $sesi): ?>
                                    <tr>
                                        <td class="bg-light font-weight-bold small py-3 align-middle text-left">
                                            <div class="text-primary font-weight-bold">Jam Ke-<?= $jamKe ?></div>
                                            <div class="text-muted" style="font-size: 0.78rem;">
                                                <i class="far fa-clock mr-1"></i><?= $sesi['mulai'] ?> - <?= $sesi['selesai'] ?>
                                            </div>
                                        </td>
                                        <?php foreach ($hariList as $h): 
                                            $cell = $matriksKelas[$h][$jamKe] ?? null;
                                        ?>
                                            <?php if ($cell !== null): ?>
                                                <?php if (!empty($cell['is_start'])): ?>
                                                    <td rowspan="<?= $cell['durasi'] ?>" class="p-2 align-middle bg-white" style="border: 2px solid #bce8f1; background-color: #f8fbff !important;">
                                                        <div class="card border-0 shadow-sm rounded p-2 text-left" style="background: linear-gradient(135deg, #eef4fc 0%, #e3edfa 100%); border-left: 4px solid #4e73df !important;">
                                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                                <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 0.75rem;">
                                                                    <?= esc($cell['item']['nama_mapel'] ?? 'Mapel') ?>
                                                                </span>
                                                                <div class="dropdown no-arrow">
                                                                    <button class="btn btn-link btn-sm text-secondary p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                                                        <a class="dropdown-item small" href="javascript:void(0)" onclick='openEditModal(<?= json_encode($cell['item']) ?>)'>
                                                                            <i class="fas fa-edit text-warning mr-2"></i>Edit Plotting
                                                                        </a>
                                                                        <a class="dropdown-item small text-danger" href="javascript:void(0)" onclick="confirmDelete(<?= $cell['item']['id'] ?>, 'kelas')">
                                                                            <i class="fas fa-trash-alt mr-2"></i>Hapus Jadwal
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="font-weight-bold text-dark mb-1" style="font-size: 0.85rem;">
                                                                <i class="fas fa-user-circle text-gray-500 mr-1"></i><?= esc($cell['item']['nama_guru'] ?? '-') ?>
                                                            </div>
                                                            <div class="text-muted small d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                                                                <span><i class="fas fa-hourglass-half mr-1"></i><?= $cell['durasi'] ?> JP (Jam <?= $cell['item']['jam_mulai_ke'] ?>-<?= $cell['item']['jam_selesai_ke'] ?>)</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                                <!-- Jika overlap (> is_start), abaikan rendering cell karena sudah dicover oleh rowspan -->
                                            <?php else: ?>
                                                <!-- Slot Kosong dengan Quick Add Button -->
                                                <td class="p-1 align-middle slot-hover" style="height: 65px;">
                                                    <button type="button" class="btn btn-sm btn-light border btn-block text-muted quick-add-btn py-2" 
                                                            title="Klik untuk plotting jadwal di slot ini"
                                                            onclick="quickAddSchedule('<?= $h ?>', <?= $jamKe ?>, 'kelas', <?= $selectedKelasId ?>)">
                                                        <i class="fas fa-plus text-gray-400"></i>
                                                        <span class="d-none d-lg-inline small ml-1">Isi</span>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light py-2 text-muted small">
                <i class="fas fa-info-circle mr-1 text-primary"></i> 
                Tips: Klik pada tombol <strong>"+"</strong> di slot kosong untuk langsung menambahkan jadwal pelajaran pada hari dan jam tersebut secara instan.
            </div>
        </div>
    <?php endif; ?>

    <!-- TAB 2: MATRIKS JADWAL PER GURU -->
    <?php if ($currentTab === 'guru'): ?>
        <div class="card shadow border-0 mb-4">
            <div class="card-header py-3 bg-white d-flex flex-wrap justify-content-between align-items-center">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <span class="mr-2 font-weight-bold text-gray-700 small"><i class="fas fa-user-tie text-primary mr-1"></i> Pilih Guru Pengajar:</span>
                    <form method="get" action="<?= base_url('admin/akademik/mengajar') ?>" class="form-inline">
                        <input type="hidden" name="tab" value="guru">
                        <input type="hidden" name="tahun_ajar_id" value="<?= esc($selectedTahunId) ?>">
                        <select name="guru_id" class="custom-select custom-select-sm font-weight-bold" onchange="this.form.submit()">
                            <?php foreach ($gurus as $g): ?>
                                <option value="<?= $g['id'] ?>" <?= $selectedGuruId == $g['id'] ? 'selected' : '' ?>>
                                    <?= esc($g['nama']) ?> <?= !empty($g['nip']) ? '('.esc($g['nip']).')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <div>
                    <?php if ($selectedGuruId): ?>
                        <a href="<?= base_url('admin/akademik/mengajar/cetak-guru/' . $selectedGuruId . '?tahun_ajar_id=' . $selectedTahunId) ?>" 
                           target="_blank" class="btn btn-outline-secondary btn-sm shadow-sm font-weight-bold">
                            <i class="fas fa-print mr-1"></i> Cetak Jadwal Guru
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!$selectedGuruId || empty($gurus)): ?>
                    <div class="p-5 text-center text-muted">
                        <i class="fas fa-info-circle fa-2x mb-2 text-info"></i>
                        <p class="mb-0">Belum ada data guru yang dipilih atau terdaftar di sistem.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 text-center align-middle" style="min-width: 900px;">
                            <thead class="bg-gradient-success text-white">
                                <tr>
                                    <th style="width: 140px;" class="py-2">Jam Ke / Waktu</th>
                                    <?php 
                                    $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                    foreach ($hariList as $h): ?>
                                        <th class="py-2" style="width: 14%;"><?= $h ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sesiJam as $jamKe => $sesi): ?>
                                    <tr>
                                        <td class="bg-light font-weight-bold small py-3 align-middle text-left">
                                            <div class="text-success font-weight-bold">Jam Ke-<?= $jamKe ?></div>
                                            <div class="text-muted" style="font-size: 0.78rem;">
                                                <i class="far fa-clock mr-1"></i><?= $sesi['mulai'] ?> - <?= $sesi['selesai'] ?>
                                            </div>
                                        </td>
                                        <?php foreach ($hariList as $h): 
                                            $cell = $matriksGuru[$h][$jamKe] ?? null;
                                        ?>
                                            <?php if ($cell !== null): ?>
                                                <?php if (!empty($cell['is_start'])): ?>
                                                    <td rowspan="<?= $cell['durasi'] ?>" class="p-2 align-middle bg-white" style="border: 2px solid #d4edda; background-color: #f7fbf8 !important;">
                                                        <div class="card border-0 shadow-sm rounded p-2 text-left" style="background: linear-gradient(135deg, #f0f8f1 0%, #e3f2e6 100%); border-left: 4px solid #1cc88a !important;">
                                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                                <span class="badge badge-success px-2 py-1 font-weight-bold" style="font-size: 0.75rem;">
                                                                    Kelas <?= esc($cell['item']['nama_kelas'] ?? '-') ?>
                                                                </span>
                                                                <div class="dropdown no-arrow">
                                                                    <button class="btn btn-link btn-sm text-secondary p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fas fa-ellipsis-v"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                                                        <a class="dropdown-item small" href="javascript:void(0)" onclick='openEditModal(<?= json_encode($cell['item']) ?>)'>
                                                                            <i class="fas fa-edit text-warning mr-2"></i>Edit Plotting
                                                                        </a>
                                                                        <a class="dropdown-item small text-danger" href="javascript:void(0)" onclick="confirmDelete(<?= $cell['item']['id'] ?>, 'guru')">
                                                                            <i class="fas fa-trash-alt mr-2"></i>Hapus Jadwal
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="font-weight-bold text-dark mb-1" style="font-size: 0.85rem;">
                                                                <i class="fas fa-book-reader text-success mr-1"></i><?= esc($cell['item']['nama_mapel'] ?? '-') ?>
                                                            </div>
                                                            <div class="text-muted small" style="font-size: 0.75rem;">
                                                                <i class="fas fa-clock mr-1"></i><?= $cell['durasi'] ?> JP (Jam Ke-<?= $cell['item']['jam_mulai_ke'] ?> s/d <?= $cell['item']['jam_selesai_ke'] ?>)
                                                            </div>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <!-- Slot Kosong dengan Quick Add Button untuk Guru -->
                                                <td class="p-1 align-middle slot-hover" style="height: 65px;">
                                                    <button type="button" class="btn btn-sm btn-light border btn-block text-muted quick-add-btn py-2" 
                                                            title="Plotting jam mengajar untuk guru ini"
                                                            onclick="quickAddSchedule('<?= $h ?>', <?= $jamKe ?>, 'guru', <?= $selectedGuruId ?>)">
                                                        <i class="fas fa-plus text-gray-400"></i>
                                                        <span class="d-none d-lg-inline small ml-1">Isi</span>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light py-2 text-muted small">
                <i class="fas fa-info-circle mr-1 text-success"></i> 
                Tampilan jadwal mengajar per guru membantu mendeteksi jam istirahat/jeda serta memastikan tidak ada jam bentrok antar kelas yang diajar.
            </div>
        </div>
    <?php endif; ?>

    <!-- TAB 3: REKAP BEBAN MENGAJAR (JTM) -->
    <?php if ($currentTab === 'rekap'): ?>
        <div class="card shadow border-0 mb-4">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-bar mr-1"></i> Rekapitulasi Jam Tatap Muka (JTM) Mingguan Guru
                </h6>
                <span class="badge badge-info px-3 py-2 font-weight-bold">
                    Standar Sertifikasi: Min. 24 JP / Pekan
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="dataTableRekap" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr class="text-center font-weight-bold">
                                <th style="width: 50px;">No</th>
                                <th>Nama Guru & NIP</th>
                                <th>Mata Pelajaran yang Diampu</th>
                                <th>Kelas / Rombel</th>
                                <th style="width: 120px;">Total JTM (JP)</th>
                                <th style="width: 140px;">Status Beban</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($rekapBeban as $rb): ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?= esc($rb['nama_guru']) ?></div>
                                        <div class="small text-muted"><?= !empty($rb['nip']) ? 'NIP. ' . esc($rb['nip']) : 'NIP: -' ?></div>
                                    </td>
                                    <td>
                                        <?php if (!empty($rb['mapel_list'])): ?>
                                            <?php foreach ($rb['mapel_list'] as $mp): ?>
                                                <span class="badge badge-light border text-dark mr-1 mb-1 px-2 py-1"><?= esc($mp) ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted small font-italic">Belum ada mapel diplot</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($rb['kelas_list'])): ?>
                                            <?php foreach ($rb['kelas_list'] as $kl): ?>
                                                <span class="badge badge-primary mr-1 mb-1 px-2 py-1"><?= esc($kl) ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted small font-italic">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="h5 font-weight-bold <?= $rb['total_jtm'] >= 24 ? 'text-success' : ($rb['total_jtm'] > 0 ? 'text-primary' : 'text-danger') ?>">
                                            <?= $rb['total_jtm'] ?>
                                        </span>
                                        <small class="text-muted d-block">Jam Pelajaran</small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($rb['total_jtm'] >= 24): ?>
                                            <span class="badge badge-success px-2 py-1 font-weight-bold"><i class="fas fa-check mr-1"></i>Memenuhi (≥24 JP)</span>
                                        <?php elseif ($rb['total_jtm'] > 0): ?>
                                            <span class="badge badge-warning px-2 py-1 font-weight-bold"><i class="fas fa-exclamation-circle mr-1"></i>Kurang (<?= $rb['total_jtm'] ?>/24)</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary px-2 py-1 font-weight-bold">0 JP (Kosong)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/akademik/mengajar?tab=guru&tahun_ajar_id=' . $selectedTahunId . '&guru_id=' . $rb['guru_id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Lihat Jadwal Guru Ini">
                                            <i class="fas fa-calendar-alt mr-1"></i> Jadwal
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- TAB 4: SEMUA DATA PLOTTING (TABEL) -->
    <?php if ($currentTab === 'tabel'): ?>
        <div class="card shadow border-0 mb-4">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-database mr-1"></i> Seluruh Daftar Plotting Mengajar
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="dataTableSemua" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr class="text-center font-weight-bold">
                                <th style="width: 40px;">No</th>
                                <th>Hari</th>
                                <th>Jam Ke / Waktu</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru Pengajar</th>
                                <th>Tahun Ajaran</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($allJadwals as $jadwal): 
                                $durasi = max(1, (int)$jadwal['jam_selesai_ke'] - (int)$jadwal['jam_mulai_ke'] + 1);
                            ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?= $no++ ?></td>
                                    <td class="text-center font-weight-bold">
                                        <span class="badge badge-info px-2 py-1"><?= esc($jadwal['hari']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="font-weight-bold text-dark">
                                            Jam Ke-<?= $jadwal['jam_mulai_ke'] ?> s/d Ke-<?= $jadwal['jam_selesai_ke'] ?>
                                        </div>
                                        <small class="text-muted font-weight-bold">(<?= $durasi ?> JP)</small>
                                    </td>
                                    <td class="text-center font-weight-bold text-primary">
                                        Kelas <?= esc($jadwal['nama_kelas'] ?? '-') ?>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?= esc($jadwal['nama_mapel'] ?? '-') ?></div>
                                        <small class="text-muted"><?= esc($jadwal['kode_mapel'] ?? '') ?> • Kel. <?= esc($jadwal['kelompok'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark"><?= esc($jadwal['nama_guru'] ?? '-') ?></div>
                                        <small class="text-muted"><?= !empty($jadwal['nip']) ? 'NIP. '.esc($jadwal['nip']) : 'NIP: -' ?></small>
                                    </td>
                                    <td class="text-center small">
                                        <?= esc($jadwal['tahun_ajar'] ?? '-') ?> (<?= esc($jadwal['semester'] ?? '-') ?>)
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-warning" title="Edit Jadwal" onclick='openEditModal(<?= json_encode($jadwal) ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" title="Hapus Jadwal" onclick="confirmDelete(<?= $jadwal['id'] ?>, 'tabel')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<!-- MODAL TAMBAH PLOTTING MENGAJAR -->
<div class="modal fade" id="modalAddJadwal" tabindex="-1" role="dialog" aria-labelledby="modalAddJadwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action="<?= base_url('admin/akademik/mengajar/create') ?>" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="redirect_tab" id="add_redirect_tab" value="<?= esc($currentTab) ?>">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalAddJadwalLabel">
                        <i class="fas fa-calendar-plus mr-2"></i>Tambah Plotting Mengajar
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small text-gray-700">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select name="tahun_ajar_id" id="add_tahun_ajar_id" class="form-control" required>
                                <?php foreach ($tahun_ajars as $th): ?>
                                    <option value="<?= $th['id'] ?>" <?= (string)$selectedTahunId === (string)$th['id'] ? 'selected' : '' ?>>
                                        TP <?= esc($th['tahun_ajar']) ?> (Semester <?= esc($th['semester']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small text-gray-700">Rombel / Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="add_kelas_id" class="form-control" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelases as $kls): ?>
                                    <option value="<?= $kls['id'] ?>" <?= $selectedKelasId == $kls['id'] ? 'selected' : '' ?>>
                                        Kelas <?= esc($kls['nama_kelas']) ?> (Tingkat <?= esc($kls['tingkat'] ?? '-') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small text-gray-700">Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" id="add_guru_id" class="form-control" required>
                                <option value="">-- Pilih Guru --</option>
                                <?php foreach ($gurus as $g): ?>
                                    <option value="<?= $g['id'] ?>" <?= $selectedGuruId == $g['id'] ? 'selected' : '' ?>>
                                        <?= esc($g['nama']) ?> <?= !empty($g['nip']) ? '('.esc($g['nip']).')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small text-gray-700">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="mapel_id" id="add_mapel_id" class="form-control" required>
                                <option value="">-- Pilih Mata Pelajaran --</option>
                                <?php foreach ($mapels as $mp): ?>
                                    <option value="<?= $mp['id'] ?>">
                                        <?= esc($mp['nama_mapel']) ?> (Kelompok <?= esc($mp['kelompok'] ?? '-') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="card bg-light border-0 p-3 mb-2 rounded">
                        <label class="font-weight-bold small text-gray-700 mb-2">
                            <i class="far fa-clock text-primary mr-1"></i> Penjadwalan Hari & Sesi Jam Pelajaran:
                        </label>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="small text-muted font-weight-bold">Hari <span class="text-danger">*</span></label>
                                <select name="hari" id="add_hari" class="form-control" required>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small text-muted font-weight-bold">Jam Mulai <span class="text-danger">*</span></label>
                                <select name="jam_mulai_ke" id="add_jam_mulai_ke" class="form-control" required onchange="calculateDuration('add')">
                                    <?php foreach ($sesiJam as $j => $s): ?>
                                        <option value="<?= $j ?>"><?= $s['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small text-muted font-weight-bold">Jam Selesai <span class="text-danger">*</span></label>
                                <select name="jam_selesai_ke" id="add_jam_selesai_ke" class="form-control" required onchange="calculateDuration('add')">
                                    <?php foreach ($sesiJam as $j => $s): ?>
                                        <option value="<?= $j ?>" <?= $j == 2 ? 'selected' : '' ?>><?= $s['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div id="add_duration_badge" class="badge badge-info p-2 font-weight-bold" style="font-size: 0.82rem;">
                            Durasi: 2 Jam Pelajaran (80 Menit)
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3 font-weight-bold text-white" style="color: #ffffff !important;">
                        <i class="fas fa-save mr-1 text-white"></i> Simpan Plotting
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PLOTTING MENGAJAR -->
<div class="modal fade" id="modalEditJadwal" tabindex="-1" role="dialog" aria-labelledby="modalEditJadwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="formEditJadwal" action="" method="post">
            <?= csrf_field(); ?>
            <input type="hidden" name="redirect_tab" id="edit_redirect_tab" value="<?= esc($currentTab) ?>">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title font-weight-bold" id="modalEditJadwalLabel">
                        <i class="fas fa-edit mr-2"></i>Edit Plotting Mengajar
                    </h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small text-gray-700">Tahun Ajaran <span class="text-danger">*</span></label>
                            <select name="tahun_ajar_id" id="edit_tahun_ajar_id" class="form-control" required>
                                <?php foreach ($tahun_ajars as $th): ?>
                                    <option value="<?= $th['id'] ?>">
                                        TP <?= esc($th['tahun_ajar']) ?> (Semester <?= esc($th['semester']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small text-gray-700">Rombel / Kelas <span class="text-danger">*</span></label>
                            <select name="kelas_id" id="edit_kelas_id" class="form-control" required>
                                <?php foreach ($kelases as $kls): ?>
                                    <option value="<?= $kls['id'] ?>">
                                        Kelas <?= esc($kls['nama_kelas']) ?> (Tingkat <?= esc($kls['tingkat'] ?? '-') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small text-gray-700">Guru Pengajar <span class="text-danger">*</span></label>
                            <select name="guru_id" id="edit_guru_id" class="form-control" required>
                                <?php foreach ($gurus as $g): ?>
                                    <option value="<?= $g['id'] ?>">
                                        <?= esc($g['nama']) ?> <?= !empty($g['nip']) ? '('.esc($g['nip']).')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold small text-gray-700">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="mapel_id" id="edit_mapel_id" class="form-control" required>
                                <?php foreach ($mapels as $mp): ?>
                                    <option value="<?= $mp['id'] ?>">
                                        <?= esc($mp['nama_mapel']) ?> (Kelompok <?= esc($mp['kelompok'] ?? '-') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="card bg-light border-0 p-3 mb-2 rounded">
                        <label class="font-weight-bold small text-gray-700 mb-2">
                            <i class="far fa-clock text-primary mr-1"></i> Penjadwalan Hari & Sesi Jam Pelajaran:
                        </label>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="small text-muted font-weight-bold">Hari <span class="text-danger">*</span></label>
                                <select name="hari" id="edit_hari" class="form-control" required>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small text-muted font-weight-bold">Jam Mulai <span class="text-danger">*</span></label>
                                <select name="jam_mulai_ke" id="edit_jam_mulai_ke" class="form-control" required onchange="calculateDuration('edit')">
                                    <?php foreach ($sesiJam as $j => $s): ?>
                                        <option value="<?= $j ?>"><?= $s['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="small text-muted font-weight-bold">Jam Selesai <span class="text-danger">*</span></label>
                                <select name="jam_selesai_ke" id="edit_jam_selesai_ke" class="form-control" required onchange="calculateDuration('edit')">
                                    <?php foreach ($sesiJam as $j => $s): ?>
                                        <option value="<?= $j ?>"><?= $s['label'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div id="edit_duration_badge" class="badge badge-info p-2 font-weight-bold" style="font-size: 0.82rem;">
                            Durasi: 1 Jam Pelajaran (40 Menit)
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm px-3 font-weight-bold text-dark">
                        <i class="fas fa-save mr-1"></i> Perbarui Plotting
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('styles'); ?>
<style>
    .slot-hover {
        background-color: #fafbfc;
        transition: all 0.2s ease-in-out;
    }
    .slot-hover:hover {
        background-color: #eaf3ff;
    }
    .quick-add-btn {
        opacity: 0.4;
        transition: all 0.2s ease-in-out;
        border-style: dashed !important;
    }
    .slot-hover:hover .quick-add-btn {
        opacity: 1;
        border-color: #4e73df !important;
        background-color: #fff;
        color: #4e73df !important;
        box-shadow: 0 2px 5px rgba(78, 115, 223, 0.2);
    }
    .btn-primary, .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
        color: #ffffff !important;
    }
    .btn-primary i {
        color: #ffffff !important;
    }
</style>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    if ($('#dataTableSemua').length) {
        $('#dataTableSemua').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    }
    if ($('#dataTableRekap').length) {
        $('#dataTableRekap').DataTable({
            pageLength: 25,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    }
    calculateDuration('add');
});

// Quick Add Schedule dari klik slot kosong di matriks
function quickAddSchedule(hari, jamKe, context, contextId) {
    resetFormAdd();
    $('#add_hari').val(hari);
    $('#add_jam_mulai_ke').val(jamKe);
    // Default durasi 2 jam pelajaran jika jam <= 7, atau 1 jam jika jam 8
    let jamSelesai = (jamKe < 8) ? (jamKe + 1) : jamKe;
    $('#add_jam_selesai_ke').val(jamSelesai);

    if (context === 'kelas' && contextId) {
        $('#add_kelas_id').val(contextId);
    } else if (context === 'guru' && contextId) {
        $('#add_guru_id').val(contextId);
    }

    calculateDuration('add');
    $('#modalAddJadwal').modal('show');
}

function calculateDuration(prefix) {
    let mulai = parseInt($('#' + prefix + '_jam_mulai_ke').val()) || 1;
    let selesai = parseInt($('#' + prefix + '_jam_selesai_ke').val()) || 1;

    if (selesai < mulai) {
        $('#' + prefix + '_jam_selesai_ke').val(mulai);
        selesai = mulai;
    }

    let jp = selesai - mulai + 1;
    let menit = jp * 40;
    let text = 'Durasi: ' + jp + ' Jam Pelajaran (' + menit + ' Menit)';
    $('#' + prefix + '_duration_badge').text(text);
}

function resetFormAdd() {
    $('#add_jam_mulai_ke').val(1);
    $('#add_jam_selesai_ke').val(2);
    calculateDuration('add');
}

function openEditModal(data) {
    $('#formEditJadwal').attr('action', '<?= base_url('admin/akademik/mengajar') ?>/' + data.id + '/update');
    $('#edit_tahun_ajar_id').val(data.tahun_ajar_id);
    $('#edit_kelas_id').val(data.kelas_id);
    $('#edit_guru_id').val(data.guru_id);
    $('#edit_mapel_id').val(data.mapel_id);
    $('#edit_hari').val(data.hari);
    $('#edit_jam_mulai_ke').val(data.jam_mulai_ke);
    $('#edit_jam_selesai_ke').val(data.jam_selesai_ke);

    calculateDuration('edit');
    $('#modalEditJadwal').modal('show');
}

function confirmDelete(id, tab) {
    let tahunId = '<?= esc($selectedTahunId) ?>';
    let kelasId = '<?= esc($selectedKelasId) ?>';
    let guruId = '<?= esc($selectedGuruId) ?>';
    let targetUrl = '<?= base_url('admin/akademik/mengajar') ?>/' + id + '/delete?tab=' + tab + '&tahun_ajar_id=' + tahunId + '&kelas_id=' + kelasId + '&guru_id=' + guruId;

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus jadwal mengajar ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#858796',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = targetUrl;
            }
        });
    } else {
        if (confirm('Apakah Anda yakin ingin menghapus jadwal mengajar ini?')) {
            window.location.href = targetUrl;
        }
    }
}
</script>
<?= $this->endSection(); ?>
