<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Review Formulir A & Coaching Pra-Observasi</h1>
            <p class="text-muted small mb-0">Pendampingan dialogis berbasis Alur TIRTA (Tujuan, Identifikasi, Rencana Aksi, Tanggung Jawab)</p>
        </div>
        <div class="d-flex gap-2">
            <?php if (!empty($coachingLog)): ?>
                <a href="<?= base_url('supervisor/hasil/cetak-coaching/' . $jadwal['id']) ?>" target="_blank" class="btn btn-info btn-pill shadow-sm" title="Cetak Berita Acara Coaching TIRTA">
                    <i class="fas fa-file-pdf fa-sm text-white-50 mr-1"></i> Berita Acara Coaching (TIRTA)
                </a>
            <?php endif; ?>
            <a href="<?= base_url('supervisor/jadwal') ?>" class="btn btn-secondary btn-pill shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali ke Jadwal
            </a>
        </div>
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

    <!-- Identitas Supervisi -->
    <div class="card shadow-sm mb-4 border-left-primary">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <span class="text-muted small d-block">Guru yang Diobservasi</span>
                    <strong class="text-dark font-weight-bold text-lg"><?= esc($jadwal['nama_guru']) ?></strong>
                    <span class="text-muted small d-block">NIP: <?= esc($jadwal['nip'] ?? '-') ?></span>
                </div>
                <div class="col-md-3 mt-2 mt-md-0">
                    <span class="text-muted small d-block">Mata Pelajaran & Kelas</span>
                    <span class="text-dark font-weight-bold"><?= esc($jadwal['mata_pelajaran']) ?></span>
                    <span class="badge badge-info ml-1"><?= esc($jadwal['nama_kelas'] ?? $jadwal['kelas']) ?></span>
                    <?php if (!empty($jadwal['kelas_fase'])): ?>
                        <span class="badge badge-primary">Fase <?= esc($jadwal['kelas_fase']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 mt-2 mt-md-0">
                    <span class="text-muted small d-block">Jadwal Observasi</span>
                    <span class="text-dark font-weight-bold"><?= date('d F Y', strtotime($jadwal['tanggal_supervisi'])) ?></span>
                    <span class="text-muted small d-block">Jam Ke: <?= esc($jadwal['jam_ke'] ?? '-') ?></span>
                </div>
                <div class="col-md-3 text-md-right mt-2 mt-md-0">
                    <span class="text-muted small d-block">Status Kesepakatan</span>
                    <?php 
                        $statusA = $praObservasi['status_kesepakatan'] ?? 'draft_guru';
                    ?>
                    <?php if ($statusA === 'disepakati'): ?>
                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i> Disepakati</span>
                    <?php elseif ($statusA === 'diajukan'): ?>
                        <span class="badge badge-warning px-2 py-1"><i class="fas fa-envelope mr-1"></i> Diajukan Guru</span>
                    <?php else: ?>
                        <span class="badge badge-secondary px-2 py-1">Draf Guru</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri: Rangkuman Pengajuan Guru -->
        <div class="col-lg-6 mb-4">
            <!-- Indikator & Fokus Perilaku Dipilih -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bullseye mr-1"></i> Target Indikator & Fokus Perilaku (Formulir A)
                    </h6>
                </div>
                <div class="card-body">
                    <?php if ($indikatorTerpilih): ?>
                        <div class="alert alert-primary mb-3">
                            <span class="badge badge-light text-primary text-uppercase float-right">
                                <?= str_replace('_', ' ', $indikatorTerpilih['dimensi_pmm'] ?? '') ?>
                            </span>
                            <h6 class="font-weight-bold mb-1"><?= esc($indikatorTerpilih['nama']) ?></h6>
                            <small class="mb-0 d-block"><?= esc($indikatorTerpilih['deskripsi']) ?></small>
                        </div>

                        <h6 class="font-weight-bold text-dark mb-2">Fokus Perilaku yang Disepakati untuk Diobservasi:</h6>
                        <?php if (!empty($fokusList)): ?>
                            <?php foreach ($fokusList as $idx => $f): ?>
                                <div class="card mb-2 border-left-success">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success mr-2"><?= $idx + 1 ?></span>
                                            <strong class="text-dark"><?= esc($f['fokus_perilaku']) ?></strong>
                                        </div>
                                        <div class="mt-2 row small">
                                            <div class="col-md-6 text-success">
                                                <strong class="d-block mb-1">Dianjurkan:</strong>
                                                <div class="bg-light p-2 rounded"><?= nl2br(esc($f['perilaku_dianjurkan'] ?? '-')) ?></div>
                                            </div>
                                            <div class="col-md-6 text-danger mt-2 mt-md-0">
                                                <strong class="d-block mb-1">Dihindari:</strong>
                                                <div class="bg-light p-2 rounded"><?= nl2br(esc($f['perilaku_dihindari'] ?? '-')) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning small">Guru belum memilih fokus perilaku spesifik.</div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-secondary">Guru belum menentukan target indikator kinerja.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Persiapan Guru -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-edit mr-1"></i> Rencana Aksi & Narasi Persiapan Guru
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted font-weight-bold d-block">Upaya Mempelajari Target Perilaku:</small>
                        <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($praObservasi['upaya_mempelajari'] ?? '-')) ?></p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted font-weight-bold d-block">Tujuan Pembelajaran Sesi Ini:</small>
                        <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($praObservasi['tujuan_pembelajaran_sesi'] ?? '-')) ?></p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted font-weight-bold d-block">Area Pengembangan Diri:</small>
                        <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($praObservasi['area_pengembangan'] ?? '-')) ?></p>
                    </div>
                    <div>
                        <small class="text-muted font-weight-bold d-block">Strategi / Diferensiasi yang Disiapkan:</small>
                        <p class="text-dark bg-light p-2 rounded mb-0"><?= nl2br(esc($praObservasi['strategi_persiapan'] ?? '-')) ?></p>
                    </div>
                </div>
            </div>

            <!-- Dokumen Ajar Terlampir -->
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-folder mr-1"></i> Perangkat Ajar yang Diunggah Guru
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($dokumenList)): ?>
                        <p class="text-muted small mb-0">Belum ada perangkat ajar yang ditautkan oleh guru.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($dokumenList as $doc): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge badge-primary mr-1 text-uppercase"><?= str_replace('_', ' ', $doc['kategori_dokumen'] ?? 'modul_ajar') ?></span>
                                        <?php if (!empty($doc['fase'])): ?>
                                            <span class="badge badge-info">Fase <?= $doc['fase'] ?></span>
                                        <?php endif; ?>
                                        <strong class="d-block mt-1"><?= esc($doc['nama_dokumen']) ?></strong>
                                        <?php if (!empty($doc['tujuan_pembelajaran'])): ?>
                                            <small class="text-muted d-block">TP: <?= esc($doc['tujuan_pembelajaran']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= esc($doc['link_drive']) ?>" target="_blank" class="btn btn-outline-primary btn-pill">
                                        <i class="fas fa-external-link-alt mr-1"></i> Buka Tautan
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Lembar Percakapan Coaching TIRTA & Kesepakatan -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm mb-4 border-top-primary">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-comments mr-1"></i> Lembar Coaching Pra-Observasi (Alur TIRTA)
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Gunakan panduan pertanyaan alur <strong>TIRTA</strong> saat berdialog dengan guru untuk menyepakati target observasi secara bermakna:
                    </p>

                    <form action="<?= base_url('supervisor/pra-observasi/agree/' . $jadwal['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- T: Tujuan -->
                        <div class="form-group mb-3">
                            <label for="tujuan_coaching" class="font-weight-bold text-primary">
                                <span class="badge badge-primary mr-1">T</span> Tujuan Percakapan
                            </label>
                            <small class="form-text text-muted mb-1">Apa yang ingin dicapai guru dari sesi percakapan pra-observasi ini?</small>
                            <textarea class="form-control" id="tujuan_coaching" name="tujuan_coaching" rows="2" placeholder="Contoh: Menyepakati fokus penerapan disiplin positif dan kesiapan modul ajar berdiferensiasi..."><?= esc($coachingLog['tujuan_coaching'] ?? '') ?></textarea>
                        </div>

                        <!-- I: Identifikasi -->
                        <div class="form-group mb-3">
                            <label for="identifikasi_kondisi" class="font-weight-bold text-info">
                                <span class="badge badge-info mr-1">I</span> Identifikasi Situasi
                            </label>
                            <small class="form-text text-muted mb-1">Bagaimana pemetaan kekuatan guru, situasi murid di kelas, dan potensi tantangan?</small>
                            <textarea class="form-control" id="identifikasi_kondisi" name="identifikasi_kondisi" rows="2" placeholder="Contoh: Guru telah memiliki kesepakatan kelas, namun 4 murid di baris belakang masih sering pasif saat kerja kelompok..."><?= esc($coachingLog['identifikasi_kondisi'] ?? '') ?></textarea>
                        </div>

                        <!-- R: Rencana Aksi -->
                        <div class="form-group mb-3">
                            <label for="rencana_aksi" class="font-weight-bold text-warning">
                                <span class="badge badge-warning text-dark mr-1">R</span> Rencana Aksi
                            </label>
                            <small class="form-text text-muted mb-1">Apa ide, gagasan, dan inovasi yang digali dari guru untuk mengatasi tantangan tersebut?</small>
                            <textarea class="form-control" id="rencana_aksi" name="rencana_aksi" rows="2" placeholder="Contoh: Guru akan membagi peran spesifik (pencatat, juru bicara, pengatur waktu) dalam kelompok..."><?= esc($coachingLog['rencana_aksi'] ?? '') ?></textarea>
                        </div>

                        <!-- TA: Tanggung Jawab -->
                        <div class="form-group mb-3">
                            <label for="tanggung_jawab" class="font-weight-bold text-success">
                                <span class="badge badge-success mr-1">TA</span> Tanggung Jawab & Komitmen
                            </label>
                            <small class="form-text text-muted mb-1">Kapan rencana dijalankan dan siapa yang membantu memantau komitmen ini?</small>
                            <textarea class="form-control" id="tanggung_jawab" name="tanggung_jawab" rows="2" placeholder="Contoh: Dilaksanakan saat observasi kelas tanggal yang telah dijadwalkan bersama rekan sejawat..."><?= esc($coachingLog['tanggung_jawab'] ?? '') ?></textarea>
                        </div>

                        <hr class="my-3">

                        <!-- Catatan Kesepakatan Supervisor -->
                        <div class="form-group mb-4">
                            <label for="catatan_supervisor_pra" class="font-weight-bold text-dark">
                                <i class="fas fa-clipboard-check mr-1"></i> Catatan Kesepakatan Pra-Observasi (Formulir A)
                            </label>
                            <textarea class="form-control" id="catatan_supervisor_pra" name="catatan_supervisor_pra" rows="3" placeholder="Tuliskan catatan apresiasi, rekomendasi persiapan, dan persetujuan fokus observasi..."><?= esc($praObservasi['catatan_supervisor_pra'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-block btn-lg shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menyepakati Formulir A ini dan melanjutkan ke Observasi Kelas?')">
                            <i class="fas fa-check-circle mr-1"></i> Sepakati Formulir A & Mulai Observasi Kelas
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>
