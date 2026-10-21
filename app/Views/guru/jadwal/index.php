<?= $this->extend('layouts/guru') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Jadwal Supervisi</h1>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Jadwal Supervisi</h6>
        </div>
        <div class="card-body">
            <?php if (empty($jadwal)): ?>
                <div class="alert alert-info">
                    Tidak ada jadwal supervisi yang tersedia.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Hari</th>
                                <th>Tanggal Supervisi</th>
                                <th>Jam Ke-</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Tahun Ajaran</th>
                                <th>Supervisor</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            foreach ($jadwal as $item): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= format_hari_indonesia($item['tanggal_supervisi']) ?></td>
                                    <td><?= date('d M Y', strtotime($item['tanggal_supervisi'])) ?></td>
                                    <td><?= $item['jam_ke'] ?? '' ?></td>
                                    <td><?= $item['nama_kelas'] ?? '' ?></td>
                                    <td><?= $item['mata_pelajaran'] ?? '' ?></td>
                                    <td><?= $item['tahun_ajar'] ?? '' ?> - <?= $item['semester'] ?? '' ?></td>
                                    <td><?= $item['supervisor_name'] ?? '' ?></td>
                                    <td>
                                        <?php if ($item['status'] == 'Terjadwal' || $item['status'] == 'dijadwalkan'): ?>
                                            <span class="badge badge-info">Terjadwal</span>
                                        <?php elseif ($item['status'] == 'pra_observasi'): ?>
                                            <span class="badge badge-warning">Pra-Observasi (Form A)</span>
                                        <?php elseif ($item['status'] == 'observasi'): ?>
                                            <span class="badge badge-primary">Observasi Kelas</span>
                                        <?php elseif ($item['status'] == 'pasca_observasi' || $item['status'] == 'tindak_lanjut'): ?>
                                            <span class="badge badge-info">Tindak Lanjut</span>
                                        <?php elseif ($item['status'] == 'Selesai' || $item['status'] == 'selesai'): ?>
                                            <span class="badge badge-success">Selesai</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?= esc($item['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('guru/pra-observasi/' . $item['id']) ?>" class="btn btn-outline-primary" title="Formulir A: Diskusi Persiapan">
                                                <i class="fas fa-clipboard-check mr-1"></i> Form A
                                            </a>
                                            <a href="<?= base_url('guru/dokumen-ajar/manage/' . $item['id']) ?>" class="btn btn-outline-warning" title="Perangkat Ajar">
                                                <i class="fas fa-folder-open mr-1"></i> Modul
                                            </a>
                                            <a href="<?= base_url('guru/tindak-lanjut/' . $item['id']) ?>" class="btn btn-outline-info" title="Formulir C & D: Tindak Lanjut">
                                                <i class="fas fa-tasks mr-1"></i> RTL
                                            </a>
                                            <?php if (in_array(strtolower($item['status']), ['observasi', 'pasca_observasi', 'tindak_lanjut', 'selesai'])): ?>
                                                <a href="<?= base_url('guru/hasil/' . $item['id']) ?>" class="btn btn-primary" title="Lihat Hasil Observasi">
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