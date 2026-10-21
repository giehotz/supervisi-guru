<?= $this->extend('layouts/supervisor') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Jadwal Supervisi</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">List Jadwal Saya</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Guru</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Jam Ke</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d M Y', strtotime($schedule['tanggal_supervisi'])) ?></td>
                                <td><?= $schedule['nama_guru'] ?></td>
                                <td><?= $schedule['mata_pelajaran'] ?></td>
                                <td><?= $schedule['kelas'] ?></td>
                                <td><?= $schedule['jam_ke'] ?></td>
                                <td>
                                    <?php if ($schedule['status'] == 'Terjadwal' || $schedule['status'] == 'dijadwalkan'): ?>
                                        <span class="badge badge-info">Terjadwal</span>
                                    <?php elseif ($schedule['status'] == 'pra_observasi'): ?>
                                        <span class="badge badge-warning">Pra-Observasi (Form A)</span>
                                    <?php elseif ($schedule['status'] == 'observasi'): ?>
                                        <span class="badge badge-primary">Observasi Kelas</span>
                                    <?php elseif ($schedule['status'] == 'pasca_observasi' || $schedule['status'] == 'tindak_lanjut'): ?>
                                        <span class="badge badge-info">Tindak Lanjut</span>
                                    <?php elseif ($schedule['status'] == 'Selesai' || $schedule['status'] == 'selesai'): ?>
                                        <span class="badge badge-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= esc($schedule['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('supervisor/pra-observasi/' . $schedule['id']) ?>"
                                            class="btn btn-outline-primary" title="Review Form A & Coaching TIRTA">
                                            <i class="fas fa-clipboard-check"></i> Form A
                                        </a>
                                        <a href="<?= base_url('supervisor/dokumen-ajar/view/' . $schedule['id']) ?>" 
                                            class="btn btn-outline-warning" title="Periksa Modul Ajar">
                                            <i class="fas fa-folder-open"></i> Modul
                                        </a>
                                        <a href="<?= base_url('supervisor/penilaian/form/' . $schedule['id']) ?>"
                                            class="btn btn-primary" title="Observasi Kelas (Form B)">
                                            <i class="fas fa-chalkboard-teacher"></i> Observasi
                                        </a>
                                        <a href="<?= base_url('supervisor/tindak-lanjut/' . $schedule['id']) ?>"
                                            class="btn btn-outline-info" title="Evaluasi Tindak Lanjut (Form C & D)">
                                            <i class="fas fa-tasks"></i> RTL
                                        </a>
                                        <?php if (in_array(strtolower($schedule['status']), ['selesai', 'pasca_observasi', 'tindak_lanjut'])): ?>
                                            <a href="<?= base_url('supervisor/penilaian/view/' . $schedule['id']) ?>"
                                                class="btn btn-info" title="Lihat Hasil Supervisi">
                                                <i class="fas fa-eye"></i> Hasil
                                            </a>
                                            <a href="<?= base_url('supervisor/foto-bukti/upload/' . $schedule['id']) ?>"
                                                class="btn btn-success" title="Unggah Foto Bukti">
                                                <i class="fas fa-camera"></i> Foto
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>


                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>