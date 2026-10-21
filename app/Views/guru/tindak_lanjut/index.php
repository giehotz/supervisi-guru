<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Formulir C & D: Tindak Lanjut & Refleksi</h1>
            <p class="text-muted small mb-0">Siklus Pembinaan Berkelanjutan Pasca Observasi Kelas (Perdirjen GTK No. 7607/2023)</p>
        </div>
        <a href="<?= base_url('guru/jadwal') ?>" class="btn btn-secondary btn-sm shadow-sm">
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

    <!-- Header Info Card -->
    <div class="card shadow-sm mb-4 border-left-info">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <small class="text-muted d-block">Mata Pelajaran & Kelas</small>
                    <strong class="text-dark font-weight-bold"><?= esc($jadwal['mata_pelajaran']) ?></strong>
                    <span class="badge badge-info ml-1"><?= esc($jadwal['nama_kelas'] ?? $jadwal['kelas']) ?></span>
                </div>
                <div class="col-md-3 mt-2 mt-md-0">
                    <small class="text-muted d-block">Tanggal Observasi Kelas</small>
                    <span class="text-dark font-weight-bold"><?= date('d F Y', strtotime($jadwal['tanggal_supervisi'])) ?></span>
                </div>
                <div class="col-md-3 mt-2 mt-md-0">
                    <small class="text-muted d-block">Supervisor</small>
                    <strong class="text-dark"><?= esc($jadwal['supervisor_name'] ?? 'Kepala Sekolah') ?></strong>
                </div>
                <div class="col-md-3 text-md-right mt-2 mt-md-0">
                    <small class="text-muted d-block">Status Tindak Lanjut</small>
                    <?php 
                        $statusTL = $tindakLanjut['status_tindak_lanjut'] ?? 'belum_dimulai';
                    ?>
                    <?php if ($statusTL === 'selesai'): ?>
                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Selesai Seluruh Siklus</span>
                    <?php elseif ($statusTL === 'form_d_diisi'): ?>
                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-spinner fa-spin mr-1"></i> Menunggu Penilaian Form D</span>
                    <?php elseif ($statusTL === 'form_c_disepakati'): ?>
                        <span class="badge badge-info px-2 py-1"><i class="fas fa-tasks mr-1"></i> Menjalankan RTL (Form C Disepakati)</span>
                    <?php elseif ($statusTL === 'form_c_diisi'): ?>
                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Menunggu Telaah Form C</span>
                    <?php else: ?>
                        <span class="badge badge-secondary px-2 py-1">Belum Dimulai</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active font-weight-bold" id="pills-form-c-tab" data-toggle="pill" href="#pills-form-c" role="tab" aria-controls="pills-form-c" aria-selected="true">
                <i class="fas fa-clipboard-list mr-1"></i> Formulir C: Rencana Tindak Lanjut (RTL)
            </a>
        </li>
        <li class="nav-item ml-2">
            <a class="nav-link font-weight-bold" id="pills-form-d-tab" data-toggle="pill" href="#pills-form-d" role="tab" aria-controls="pills-form-d" aria-selected="false">
                <i class="fas fa-lightbulb mr-1"></i> Formulir D: Refleksi Tindak Lanjut
            </a>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="pills-tabContent">
        <!-- ========================================== -->
        <!-- TAB 1: FORMULIR C (TINDAK LANJUT)         -->
        <!-- ========================================== -->
        <div class="tab-pane fade show active" id="pills-form-c" role="tabpanel" aria-labelledby="pills-form-c-tab">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-edit mr-1"></i> Isian Formulir C: Refleksi & Rencana Tindak Lanjut Guru
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('guru/tindak-lanjut/save-c') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">

                                <!-- Refleksi Pasca Observasi -->
                                <div class="form-group mb-4">
                                    <label for="refleksi_guru" class="font-weight-bold text-dark">
                                        1. Refleksi Guru: Apa saja tantangan, kendala, atau hal baik yang Anda alami saat pelaksanaan observasi kelas?
                                    </label>
                                    <textarea class="form-control" id="refleksi_guru" name="refleksi_guru" rows="3" placeholder="Contoh: Saya merasa kesepakatan kelas sudah dipahami sebagian murid, namun saat diskusi kelompok ada murid yang masih pasif dan alokasi waktu terburu-buru..."><?= esc($tindakLanjut['refleksi_guru'] ?? '') ?></textarea>
                                </div>

                                <hr class="my-3">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-graduation-cap mr-1"></i> Pilihan Aktivitas Belajar / Pengembangan Diri:</h6>

                                <!-- Pilihan Belajar PMM -->
                                <div class="form-group mb-3">
                                    <label for="pilihan_belajar_pmm" class="font-weight-bold text-dark">
                                        2. Topik Pelatihan Mandiri di PINTAR KEMENAG, Ruang GTK atau Platform Merdeka Mengajar (PMM):
                                    </label>
                                    <input type="text" class="form-control" id="pilihan_belajar_pmm" name="pilihan_belajar_pmm" value="<?= esc($tindakLanjut['pilihan_belajar_pmm'] ?? '') ?>" placeholder="Contoh: Modul Disiplin Positif / Pembelajaran Berdiferensiasi / Fasilitasi Diskusi HOTS">
                                    <small class="form-text text-muted">Tuliskan nama modul atau aksi nyata di Ruang GTK yang relevan dengan fokus perbaikan Anda.</small>
                                </div>

                                <!-- Pilihan Belajar Lainnya -->
                                <div class="form-group mb-4">
                                    <label for="pilihan_belajar_lainnya" class="font-weight-bold text-dark">
                                        3. Aktivitas di Komunitas Belajar (Kombel) / MGMP / Berbagi Praktik Baik:
                                    </label>
                                    <textarea class="form-control" id="pilihan_belajar_lainnya" name="pilihan_belajar_lainnya" rows="2" placeholder="Contoh: Mendiskusikan rubrik asesmen formatif dalam pertemuan rutin Kombel sekolah setiap Kamis."><?= esc($tindakLanjut['pilihan_belajar_lainnya'] ?? '') ?></textarea>
                                </div>

                                <hr class="my-3">
                                <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-bullseye mr-1"></i> Target Perubahan & Dukungan:</h6>

                                <!-- Tujuan Tindak Lanjut -->
                                <div class="form-group mb-3">
                                    <label for="tujuan_tindak_lanjut" class="font-weight-bold text-dark">
                                        4. Apa perubahan konkret pada praktik pembelajaran di kelas yang ingin Anda capai?
                                    </label>
                                    <textarea class="form-control" id="tujuan_tindak_lanjut" name="tujuan_tindak_lanjut" rows="2" placeholder="Contoh: Meningkatkan partisipasi aktif 100% murid dalam kelompok kecil dengan pembagian peran yang terstruktur."><?= esc($tindakLanjut['tujuan_tindak_lanjut'] ?? '') ?></textarea>
                                </div>

                                <!-- Upaya Tindak Lanjut -->
                                <div class="form-group mb-3">
                                    <label for="upaya_tindak_lanjut" class="font-weight-bold text-dark">
                                        5. Langkah nyata apa yang akan Anda lakukan untuk mencapai perubahan tersebut?
                                    </label>
                                    <textarea class="form-control" id="upaya_tindak_lanjut" name="upaya_tindak_lanjut" rows="2" placeholder="Contoh: Mengembangkan lembar kerja (LKPD) kolaboratif dan kartu peran murid."><?= esc($tindakLanjut['upaya_tindak_lanjut'] ?? '') ?></textarea>
                                </div>

                                <div class="form-row mb-3">
                                    <!-- Periode Tindak Lanjut -->
                                    <div class="form-group col-md-6">
                                        <label for="periode_tindak_lanjut" class="font-weight-bold text-dark">Periode Waktu Pelaksanaan</label>
                                        <input type="text" class="form-control" id="periode_tindak_lanjut" name="periode_tindak_lanjut" value="<?= esc($tindakLanjut['periode_tindak_lanjut'] ?? '') ?>" placeholder="Contoh: Mei - Juni Semester Genap">
                                    </div>

                                    <!-- Kebutuhan Dukungan -->
                                    <div class="form-group col-md-6">
                                        <label for="kebutuhan_dukungan" class="font-weight-bold text-dark">Jenis Kebutuhan Dukungan</label>
                                        <select class="form-control" id="kebutuhan_dukungan" name="kebutuhan_dukungan">
                                            <option value="">-- Pilih Jenis Dukungan --</option>
                                            <option value="sumber_daya" <?= ($tindakLanjut['kebutuhan_dukungan'] ?? '') === 'sumber_daya' ? 'selected' : '' ?>>Penyediaan Sumber / Buku Ajar</option>
                                            <option value="peralatan_media" <?= ($tindakLanjut['kebutuhan_dukungan'] ?? '') === 'peralatan_media' ? 'selected' : '' ?>>Peralatan / Sarana Media TIK</option>
                                            <option value="pelatihan_kombel" <?= ($tindakLanjut['kebutuhan_dukungan'] ?? '') === 'pelatihan_kombel' ? 'selected' : '' ?>>Sesi Berbagi di Komunitas Belajar</option>
                                            <option value="pendampingan_rekan_sejawat" <?= ($tindakLanjut['kebutuhan_dukungan'] ?? '') === 'pendampingan_rekan_sejawat' ? 'selected' : '' ?>>Pendampingan Rekan Sejawat (Peer Coaching)</option>
                                            <option value="lainnya" <?= ($tindakLanjut['kebutuhan_dukungan'] ?? '') === 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="detail_dukungan" class="font-weight-bold text-dark">Deskripsi Rinci Kebutuhan Dukungan:</label>
                                    <textarea class="form-control" id="detail_dukungan" name="detail_dukungan" rows="2" placeholder="Jelaskan bentuk bantuan spesifik yang diharapkan dari Kepala Sekolah / Satuan Pendidikan..."><?= esc($tindakLanjut['detail_dukungan'] ?? '') ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block py-2 shadow-sm">
                                    <i class="fas fa-save mr-1"></i> Simpan & Ajukan Formulir C (Tindak Lanjut)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Umpan Balik Supervisor Form C -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4 border-left-success">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-success">
                                <i class="fas fa-user-check mr-1"></i> Respon & Umpan Balik Supervisor
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($tindakLanjut['respon_supervisor_c'])): ?>
                                <div class="mb-3">
                                    <small class="text-muted font-weight-bold d-block">Kualitas Refleksi Guru (Rubrik PMM):</small>
                                    <span class="badge badge-success px-2 py-1 text-wrap text-left mt-1">
                                        <?= ucwords(str_replace('_', ' ', $tindakLanjut['respon_supervisor_c'])) ?>
                                    </span>
                                </div>
                                <div>
                                    <small class="text-muted font-weight-bold d-block">Catatan Supervisor:</small>
                                    <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['catatan_supervisor_c'] ?? '-')) ?></p>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-hourglass-half fa-2x text-gray-300 mb-2 d-block"></i>
                                    <p class="small mb-0">Supervisor belum memberikan penilaian atas refleksi Formulir C Anda.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: FORMULIR D (REFLEKSI TINDAK LANJUT) -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="pills-form-d" role="tabpanel" aria-labelledby="pills-form-d-tab">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-lightbulb mr-1"></i> Isian Formulir D: Refleksi Perubahan Praktik Pasca RTL
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('guru/tindak-lanjut/save-d') ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">

                                <div class="form-group mb-3">
                                    <label for="inspirasi_baru" class="font-weight-bold text-dark">
                                        1. Apa inspirasi baru yang Anda dapatkan dari upaya tindak lanjut yang telah dilakukan?
                                    </label>
                                    <textarea class="form-control" id="inspirasi_baru" name="inspirasi_baru" rows="3" placeholder="Contoh: Saya menyadari bahwa kesepakatan kelas harus direfleksikan secara berkala tiap awal pelajaran, bukan hanya dibuat sekali di awal semester..."><?= esc($tindakLanjut['inspirasi_baru'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="perubahan_praktik" class="font-weight-bold text-dark">
                                        2. Perubahan praktik pembelajaran konkret apa yang telah berhasil Anda terapkan di kelas?
                                    </label>
                                    <textarea class="form-control" id="perubahan_praktik" name="perubahan_praktik" rows="3" placeholder="Contoh: Murid-murid kini aktif mengambil peran dalam kelompok dan saling mengingatkan aturan kelas tanpa perlu intimidasi guru..."><?= esc($tindakLanjut['perubahan_praktik'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="kendala_tindak_lanjut" class="font-weight-bold text-dark">
                                        3. Apa 3 tantangan/kendala tersulit yang Anda hadapi saat melakukan perubahan tersebut?
                                    </label>
                                    <textarea class="form-control" id="kendala_tindak_lanjut" name="kendala_tindak_lanjut" rows="2" placeholder="Tuliskan kendala teknis atau dinamika murid yang dialami..."><?= esc($tindakLanjut['kendala_tindak_lanjut'] ?? '') ?></textarea>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="rencana_perbaikan_berkelanjutan" class="font-weight-bold text-dark">
                                        4. Rencana perbaikan berkelanjutan Anda ke depan:
                                    </label>
                                    <textarea class="form-control" id="rencana_perbaikan_berkelanjutan" name="rencana_perbaikan_berkelanjutan" rows="2" placeholder="Bagaimana komitmen Anda menjaga keberlanjutan perubahan ini pada semester berikutnya..."><?= esc($tindakLanjut['rencana_perbaikan_berkelanjutan'] ?? '') ?></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block py-2 shadow-sm">
                                    <i class="fas fa-save mr-1"></i> Simpan & Ajukan Formulir D (Refleksi)
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Evaluasi Capaian Form D -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4 border-left-primary">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-award mr-1"></i> Penilaian Capaian Tindak Lanjut Supervisor
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($tindakLanjut['penilaian_supervisor_d'])): ?>
                                <div class="mb-3">
                                    <small class="text-muted font-weight-bold d-block">Capaian Perubahan (Rubrik PMM):</small>
                                    <span class="badge badge-primary px-2 py-1 text-wrap text-left mt-1">
                                        <?= ucwords(str_replace('_', ' ', $tindakLanjut['penilaian_supervisor_d'])) ?>
                                    </span>
                                </div>
                                <div>
                                    <small class="text-muted font-weight-bold d-block">Catatan Rekomendasi Akhir:</small>
                                    <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($tindakLanjut['catatan_supervisor_d'] ?? '-')) ?></p>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-hourglass-half fa-2x text-gray-300 mb-2 d-block"></i>
                                    <p class="small mb-0">Supervisor belum melakukan evaluasi akhir capaian tindak lanjut Anda.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
