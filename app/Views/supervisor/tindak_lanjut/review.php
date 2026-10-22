<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Evaluasi Tindak Lanjut & Refleksi Guru (Form C & D)</h1>
            <p class="text-muted small mb-0">Penilaian pembinaan berkelanjutan mengacu Perdirjen GTK No. 7607/B.B1/HK.03/2023</p>
        </div>
        <a href="<?= base_url('supervisor/jadwal') ?>" class="btn btn-secondary btn-pill shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali ke Jadwal
        </a>
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

    <!-- Identitas Guru -->
    <div class="card shadow-sm mb-4 border-left-primary">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <small class="text-muted d-block">Guru Binaan</small>
                    <strong class="text-dark font-weight-bold text-lg"><?= esc($jadwal['nama_guru']) ?></strong>
                    <span class="text-muted small d-block">NIP: <?= esc($jadwal['nip'] ?? '-') ?></span>
                </div>
                <div class="col-md-4 mt-2 mt-md-0">
                    <small class="text-muted d-block">Mata Pelajaran & Kelas</small>
                    <span class="text-dark font-weight-bold"><?= esc($jadwal['mata_pelajaran']) ?></span>
                    <span class="badge badge-info ml-1"><?= esc($jadwal['nama_kelas'] ?? $jadwal['kelas']) ?></span>
                    <?php if (!empty($jadwal['kelas_fase'])): ?>
                        <span class="badge badge-primary">Fase <?= esc($jadwal['kelas_fase']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-md-right mt-2 mt-md-0">
                    <small class="text-muted d-block">Status Tindak Lanjut</small>
                    <?php 
                        $statusTL = $tindakLanjut['status_tindak_lanjut'] ?? 'belum_dimulai';
                    ?>
                    <?php if ($statusTL === 'selesai'): ?>
                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i> Selesai Seluruh Siklus</span>
                    <?php elseif ($statusTL === 'form_d_diisi'): ?>
                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-bell mr-1"></i> Form D Siap Dinilai</span>
                    <?php elseif ($statusTL === 'form_c_disepakati'): ?>
                        <span class="badge badge-info px-2 py-1">Form C Telah Disepakati</span>
                    <?php elseif ($statusTL === 'form_c_diisi'): ?>
                        <span class="badge badge-warning px-2 py-1">Form C Menunggu Telaah</span>
                    <?php else: ?>
                        <span class="badge badge-secondary px-2 py-1">Belum Diisi Guru</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active font-weight-bold" id="pills-c-tab" data-toggle="pill" href="#pills-c" role="tab" aria-controls="pills-c" aria-selected="true">
                <i class="fas fa-clipboard-check mr-1"></i> Formulir C: Telaah Rencana Tindak Lanjut
            </a>
        </li>
        <li class="nav-item ml-2">
            <a class="nav-link font-weight-bold" id="pills-d-tab" data-toggle="pill" href="#pills-d" role="tab" aria-controls="pills-d" aria-selected="false">
                <i class="fas fa-award mr-1"></i> Formulir D: Evaluasi Akhir Refleksi
            </a>
        </li>
    </ul>

    <div class="tab-content" id="pills-tabContent">
        <!-- ============================================== -->
        <!-- TAB 1: FORMULIR C (REVIEW RTL)                 -->
        <!-- ============================================== -->
        <div class="tab-pane fade show active" id="pills-c" role="tabpanel" aria-labelledby="pills-c-tab">
            <div class="row">
                <!-- Kolom Kiri: Rangkuman Isian Guru -->
                <div class="col-lg-7 mb-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-user-edit mr-1"></i> Rencana Tindak Lanjut Guru (Formulir C)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted font-weight-bold d-block">1. Refleksi Guru Pasca Observasi:</small>
                                <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['refleksi_guru'] ?? 'Belum diisi')) ?></p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted font-weight-bold d-block">2. Pilihan Pelatihan Mandiri di Ruang GTK / PMM:</small>
                                <p class="text-dark bg-light p-2 rounded mb-0 font-weight-bold"><?= esc($tindakLanjut['pilihan_belajar_pmm'] ?? '-') ?></p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted font-weight-bold d-block">3. Pilihan Belajar di Komunitas Belajar / Kombel:</small>
                                <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['pilihan_belajar_lainnya'] ?? '-')) ?></p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted font-weight-bold d-block">4. Tujuan Perubahan Praktik di Kelas:</small>
                                <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['tujuan_tindak_lanjut'] ?? '-')) ?></p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted font-weight-bold d-block">5. Upaya Tindak Lanjut Konkret:</small>
                                <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['upaya_tindak_lanjut'] ?? '-')) ?></p>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted font-weight-bold d-block">Periode Pelaksanaan:</small>
                                    <span class="badge badge-light border text-dark p-2 d-block text-left"><?= esc($tindakLanjut['periode_tindak_lanjut'] ?? '-') ?></span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted font-weight-bold d-block">Jenis Dukungan Dibutuhkan:</small>
                                    <span class="badge badge-info p-2 d-block text-left"><?= ucwords(str_replace('_', ' ', $tindakLanjut['kebutuhan_dukungan'] ?? '-')) ?></span>
                                </div>
                            </div>

                            <div class="mt-2">
                                <small class="text-muted font-weight-bold d-block">Rincian Kebutuhan Dukungan dari Sekolah:</small>
                                <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['detail_dukungan'] ?? '-')) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Form Penilaian Supervisor C -->
                <div class="col-lg-5 mb-4">
                    <div class="card shadow-sm mb-4 border-top-primary">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-check-square mr-1"></i> Umpan Balik Supervisor (Rubrik Form C)
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('supervisor/tindak-lanjut/feedback-c/' . $jadwal['id']) ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="form-group mb-3">
                                    <label for="respon_supervisor_c" class="font-weight-bold text-dark">
                                        Kualitas Refleksi Guru (Sesuai Regulasi PMM):
                                    </label>
                                    <select class="form-control" id="respon_supervisor_c" name="respon_supervisor_c" required>
                                        <option value="">-- Pilih Rubrik Penilaian --</option>
                                        <option value="sadar_tantangan_luar_diri" <?= ($tindakLanjut['respon_supervisor_c'] ?? '') === 'sadar_tantangan_luar_diri' ? 'selected' : '' ?>>
                                            1. Sadar tantangan dari faktor di luar dirinya (situasi kelas/murid)
                                        </option>
                                        <option value="sadar_tantangan_dalam_diri" <?= ($tindakLanjut['respon_supervisor_c'] ?? '') === 'sadar_tantangan_dalam_diri' ? 'selected' : '' ?>>
                                            2. Sadar tantangan yang bersumber dari dalam dirinya (metode/pemahaman guru)
                                        </option>
                                        <option value="sadar_tantangan_dan_dampak_pembelajaran" <?= ($tindakLanjut['respon_supervisor_c'] ?? '') === 'sadar_tantangan_dan_dampak_pembelajaran' ? 'selected' : '' ?>>
                                            3. Sadar tantangan dan dampak langsungnya terhadap pembelajaran murid (Optimal)
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">Berdasarkan kesadaran guru atas akar penyebab tantangan pembelajaran.</small>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="catatan_supervisor_c" class="font-weight-bold text-dark">Catatan Umpan Balik & Rekomendasi:</label>
                                    <textarea class="form-control" id="catatan_supervisor_c" name="catatan_supervisor_c" rows="4" placeholder="Tuliskan umpan balik apresiatif dan saran penguatan untuk pelaksanaan rencana tindak lanjut..."><?= esc($tindakLanjut['catatan_supervisor_c'] ?? '') ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                    <i class="fas fa-save mr-1"></i> Simpan Penilaian & Sepakati Form C
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: FORMULIR D (EVALUASI REFLEKSI)         -->
        <!-- ============================================== -->
        <div class="tab-pane fade" id="pills-d" role="tabpanel" aria-labelledby="pills-d-tab">
            <div class="row">
                <!-- Kolom Kiri: Rangkuman Isian Guru D -->
                <div class="col-lg-7 mb-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-lightbulb mr-1"></i> Refleksi Perubahan Praktik Guru (Formulir D)
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($tindakLanjut['inspirasi_baru'])): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle mr-1"></i> Guru belum mengisi Formulir D (Refleksi Tindak Lanjut). Anda dapat mengevaluasi bagian ini setelah guru menyelesaikan pelaksanaan RTL di kelas.
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <small class="text-muted font-weight-bold d-block">1. Inspirasi Baru yang Didapat Guru:</small>
                                    <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['inspirasi_baru'])) ?></p>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted font-weight-bold d-block">2. Perubahan Praktik yang Diterapkan di Kelas:</small>
                                    <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['perubahan_praktik'])) ?></p>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted font-weight-bold d-block">3. Kendala Paling Sulit:</small>
                                    <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['kendala_tindak_lanjut'] ?? '-')) ?></p>
                                </div>

                                <div>
                                    <small class="text-muted font-weight-bold d-block">4. Rencana Perbaikan Berkelanjutan:</small>
                                    <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['rencana_perbaikan_berkelanjutan'] ?? '-')) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Evaluasi Capaian D -->
                <div class="col-lg-5 mb-4">
                    <div class="card shadow-sm mb-4 border-top-success">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-award mr-1"></i> Evaluasi Capaian Perubahan (Rubrik Form D)
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('supervisor/tindak-lanjut/evaluate-d/' . $jadwal['id']) ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="form-group mb-3">
                                    <label for="penilaian_supervisor_d" class="font-weight-bold text-dark">
                                        Capaian Praktik Kinerja (Rubrik PMM):
                                    </label>
                                    <select class="form-control" id="penilaian_supervisor_d" name="penilaian_supervisor_d" required>
                                        <option value="">-- Pilih Capaian --</option>
                                        <option value="belajar_upaya_perubahan" <?= ($tindakLanjut['penilaian_supervisor_d'] ?? '') === 'belajar_upaya_perubahan' ? 'selected' : '' ?>>
                                            1. Melakukan upaya belajar dan mengeksplorasi perubahan
                                        </option>
                                        <option value="menerapkan_perubahan_di_kelas" <?= ($tindakLanjut['penilaian_supervisor_d'] ?? '') === 'menerapkan_perubahan_di_kelas' ? 'selected' : '' ?>>
                                            2. Berhasil menerapkan perubahan nyata pada pembelajaran di kelas
                                        </option>
                                        <option value="membagikan_praktik_baik" <?= ($tindakLanjut['penilaian_supervisor_d'] ?? '') === 'membagikan_praktik_baik' ? 'selected' : '' ?>>
                                            3. Menerapkan perubahan dan aktif membagikan praktik baik ke rekan sejawat (Istimewa)
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="catatan_supervisor_d" class="font-weight-bold text-dark">Catatan Rekomendasi Akhir Siklus:</label>
                                    <textarea class="form-control" id="catatan_supervisor_d" name="catatan_supervisor_d" rows="4" placeholder="Tuliskan rekomendasi apresiatif untuk pengembangan keprofesian berkelanjutan guru..."><?= esc($tindakLanjut['catatan_supervisor_d'] ?? '') ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-success btn-block shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan dan mengesahkan seluruh siklus supervisi guru ini?')">
                                    <i class="fas fa-check-double mr-1"></i> Sahkan & Selesaikan Seluruh Siklus Supervisi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
