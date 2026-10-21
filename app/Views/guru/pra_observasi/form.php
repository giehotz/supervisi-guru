<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Formulir A: Diskusi Persiapan Observasi Kinerja</h1>
            <p class="text-muted small mb-0">Standar Pengelolaan Kinerja Guru Ruang GTK / PMM (Perdirjen GTK No. 7607/2023)</p>
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

    <!-- Status Banner -->
    <?php 
        $statusKesepakatan = $praObservasi['status_kesepakatan'] ?? 'draft_guru';
        $isDisepakati = ($statusKesepakatan === 'disepakati');
    ?>
    <div class="alert <?= $isDisepakati ? 'alert-success' : ($statusKesepakatan === 'diajukan' ? 'alert-warning' : 'alert-info') ?> shadow-sm mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <strong>Status Formulir A:</strong>
                <?php if ($statusKesepakatan === 'disepakati'): ?>
                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i> Telah Disepakati Bersama Supervisor</span>
                    <span class="small ml-2 text-muted">(Disepakati pada: <?= date('d M Y H:i', strtotime($praObservasi['disepakati_pada'])) ?>)</span>
                <?php elseif ($statusKesepakatan === 'diajukan'): ?>
                    <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i> Menunggu Telaah & Persetujuan Supervisor</span>
                <?php else: ?>
                    <span class="badge badge-secondary px-2 py-1"><i class="fas fa-edit mr-1"></i> Draf Guru (Belum Diajukan)</span>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('guru/dokumen-ajar/manage/' . $jadwal['id']) ?>" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-folder-open mr-1"></i> Kelola Perangkat Ajar
            </a>
        </div>
    </div>

    <!-- Supervisor Feedback Notice if Disepakati -->
    <?php if (!empty($praObservasi['catatan_supervisor_pra'])): ?>
        <div class="card border-left-info shadow-sm mb-4">
            <div class="card-body py-3">
                <h6 class="font-weight-bold text-info mb-1"><i class="fas fa-comment-dots mr-1"></i> Catatan Kesepakatan dari Supervisor:</h6>
                <p class="mb-0 text-dark"><?= nl2br(esc($praObservasi['catatan_supervisor_pra'])) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('guru/pra-observasi/save') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">

        <div class="row">
            <!-- Kolom Kiri: Informasi Supervisi & Pilihan 8 Indikator -->
            <div class="col-lg-5 mb-4">
                <!-- Info Jadwal -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-calendar-alt mr-1"></i> Rincian Jadwal Observasi</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <tr>
                                <th class="pl-3" style="width: 40%">Tanggal Observasi</th>
                                <td>: <?= date('d M Y', strtotime($jadwal['tanggal_supervisi'])) ?> (Jam Ke: <?= $jadwal['jam_ke'] ?? '-' ?>)</td>
                            </tr>
                            <tr>
                                <th class="pl-3">Mata Pelajaran</th>
                                <td>: <?= esc($jadwal['mata_pelajaran']) ?></td>
                            </tr>
                            <tr>
                                <th class="pl-3">Kelas / Fase</th>
                                <td>: <?= esc($jadwal['nama_kelas'] ?? $jadwal['kelas']) ?> <?= !empty($jadwal['kelas_fase']) ? '(Fase ' . $jadwal['kelas_fase'] . ')' : '' ?></td>
                            </tr>
                            <tr>
                                <th class="pl-3">Supervisor</th>
                                <td>: <strong><?= esc($jadwal['supervisor_name'] ?? 'Kepala Sekolah / Pengawas') ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Pilihan 1 dari 8 Indikator PMM -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <span class="badge badge-primary mr-1">Langkah 1</span> Pilih 1 Target Indikator Kinerja PMM
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Pilihlah <strong>1 indikator</strong> prioritas yang ingin diobservasi pada sesi kelas ini (mengacu pada Rapor Pendidikan / fokus peningkatan diri Anda).
                        </p>

                        <?php 
                            $selectedIndikator = $jadwal['indikator_pmm_id'] ?? ($indikatorPmm[0]['id'] ?? 0);
                        ?>

                        <div class="list-group">
                            <?php foreach ($indikatorPmm as $ind): ?>
                                <label class="list-group-item list-group-item-action d-flex align-items-start <?= ($selectedIndikator == $ind['id']) ? 'active text-white' : '' ?>" style="cursor: pointer;">
                                    <div class="custom-control custom-radio mt-1 mr-2">
                                        <input type="radio" id="ind_<?= $ind['id'] ?>" name="indikator_pmm_id" value="<?= $ind['id'] ?>" class="custom-control-input radio-indikator" <?= ($selectedIndikator == $ind['id']) ? 'checked' : '' ?> <?= $isDisepakati ? 'disabled' : '' ?>>
                                        <label class="custom-control-label" for="ind_<?= $ind['id'] ?>"></label>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="font-weight-bold mb-1"><?= esc($ind['nama']) ?></h6>
                                            <span class="badge <?= ($selectedIndikator == $ind['id']) ? 'badge-light text-primary' : 'badge-secondary' ?> text-uppercase small">
                                                <?= str_replace('_', ' ', $ind['dimensi_pmm'] ?? '') ?>
                                            </span>
                                        </div>
                                        <p class="small mb-0 opacity-75"><?= esc($ind['deskripsi']) ?></p>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Fokus Perilaku & Catatan Persiapan -->
            <div class="col-lg-7 mb-4">
                <!-- Target Fokus Perilaku -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <span class="badge badge-primary mr-1">Langkah 2</span> Pilih 1–3 Fokus Perilaku yang Diobservasi
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Centang 1 hingga 3 fokus perilaku yang akan Anda tampilkan di kelas. Supervisor hanya akan mengamati dan menilai fokus perilaku yang Anda centang.
                        </p>

                        <?php foreach ($indikatorPmm as $ind): ?>
                            <div class="fokus-container" id="fokus_group_<?= $ind['id'] ?>" style="<?= ($selectedIndikator == $ind['id']) ? '' : 'display: none;' ?>">
                                <?php if (!empty($fokusPerIndikator[$ind['id']])): ?>
                                    <?php foreach ($fokusPerIndikator[$ind['id']] as $fokus): ?>
                                        <?php 
                                            $isChecked = in_array($fokus['id'], $fokusDipilih);
                                        ?>
                                        <div class="card mb-3 border-left-primary shadow-xs">
                                            <div class="card-body p-3">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input checkbox-fokus" id="fokus_<?= $fokus['id'] ?>" name="fokus_perilaku_dipilih[]" value="<?= $fokus['id'] ?>" <?= $isChecked ? 'checked' : '' ?> <?= $isDisepakati ? 'disabled' : '' ?>>
                                                    <label class="custom-control-label font-weight-bold text-dark" for="fokus_<?= $fokus['id'] ?>">
                                                        <?= esc($fokus['fokus_perilaku']) ?>
                                                    </label>
                                                </div>

                                                <div class="row mt-2 pt-2 border-top small">
                                                    <div class="col-md-6 text-success">
                                                        <strong class="d-block mb-1"><i class="fas fa-check-circle mr-1"></i> Perilaku yang Dianjurkan:</strong>
                                                        <div class="bg-light p-2 rounded"><?= nl2br(esc($fokus['perilaku_dianjurkan'] ?? '-')) ?></div>
                                                    </div>
                                                    <div class="col-md-6 text-danger mt-2 mt-md-0">
                                                        <strong class="d-block mb-1"><i class="fas fa-times-circle mr-1"></i> Perilaku yang Dihindari:</strong>
                                                        <div class="bg-light p-2 rounded"><?= nl2br(esc($fokus['perilaku_dihindari'] ?? '-')) ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Formulir Isian Persiapan Guru -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <span class="badge badge-primary mr-1">Langkah 3</span> Rencana Pelaksanaan & Upaya Persiapan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="upaya_mempelajari" class="font-weight-bold text-dark">
                                1. Apa saja upaya yang telah/akan Anda lakukan untuk mempelajari target perilaku ini?
                            </label>
                            <textarea class="form-control" id="upaya_mempelajari" name="upaya_mempelajari" rows="3" placeholder="Contoh: Mempelajari modul Disiplin Positif di Pelatihan Mandiri PMM, berdiskusi dengan rekan MGMP, dan merancang kesepakatan kelas bersama murid." <?= $isDisepakati ? 'readonly' : '' ?>><?= esc($praObservasi['upaya_mempelajari'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="tujuan_pembelajaran_sesi" class="font-weight-bold text-dark">
                                2. Tujuan Pembelajaran (TP) spesifik yang akan disajikan saat observasi:
                            </label>
                            <textarea class="form-control" id="tujuan_pembelajaran_sesi" name="tujuan_pembelajaran_sesi" rows="2" placeholder="Tuliskan TP yang akan dicapai peserta didik pada pertemuan ini..." <?= $isDisepakati ? 'readonly' : '' ?>><?= esc($praObservasi['tujuan_pembelajaran_sesi'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="area_pengembangan" class="font-weight-bold text-dark">
                                3. Area kompetensi yang ingin Anda tingkatkan pada sesi ini:
                            </label>
                            <textarea class="form-control" id="area_pengembangan" name="area_pengembangan" rows="2" placeholder="Contoh: Pengelolaan interaksi kelompok kecil dan scaffolding untuk murid berkebutuhan khusus." <?= $isDisepakati ? 'readonly' : '' ?>><?= esc($praObservasi['area_pengembangan'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="strategi_persiapan" class="font-weight-bold text-dark">
                                4. Strategi / metode pembelajaran berdiferensiasi yang disiapkan:
                            </label>
                            <textarea class="form-control" id="strategi_persiapan" name="strategi_persiapan" rows="2" placeholder="Contoh: Problem-based learning dengan diferensiasi produk (infografis / rekaman suara / mind map)." <?= $isDisepakati ? 'readonly' : '' ?>><?= esc($praObservasi['strategi_persiapan'] ?? '') ?></textarea>
                        </div>

                        <?php if (!$isDisepakati): ?>
                            <hr class="my-4">
                            <div class="d-flex justify-content-between">
                                <button type="submit" name="aksi" value="draft" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-save mr-1"></i> Simpan Draf
                                </button>
                                <button type="submit" name="aksi" value="ajukan" class="btn btn-primary px-4 shadow-sm" onclick="return confirm('Apakah Anda yakin ingin mengajukan Formulir A ini ke Supervisor?')">
                                    <i class="fas fa-paper-plane mr-1"></i> Ajukan ke Supervisor
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary text-center mb-0 mt-3">
                                <i class="fas fa-lock mr-1"></i> Formulir A telah disepakati dan terkunci untuk proses observasi kelas.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // Toggle fokus perilaku saat radio indikator berubah
        $('.radio-indikator').on('change', function() {
            var selectedId = $(this).val();
            
            // Highlight visual active item di list group
            $('.list-group-item').removeClass('active text-white');
            $('.list-group-item').find('.badge').removeClass('badge-light text-primary').addClass('badge-secondary');
            
            var parentItem = $(this).closest('.list-group-item');
            parentItem.addClass('active text-white');
            parentItem.find('.badge').removeClass('badge-secondary').addClass('badge-light text-primary');

            // Sembunyikan semua grup fokus dan tampilkan yang sesuai
            $('.fokus-container').hide();
            $('#fokus_group_' + selectedId).fadeIn(200);
        });
    });
</script>
<?= $this->endSection() ?>
