<!-- Teacher & Schedule Info Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-info-circle mr-1"></i> Informasi Guru & Pelaksanaan Supervisi
        </h6>
        <span class="badge badge-<?= $schedule['status'] === 'Selesai' ? 'success' : 'info' ?> px-3 py-2">
            Status: <?= esc($schedule['status']) ?>
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td width="35%"><strong>Nama Guru</strong></td>
                        <td width="5%">:</td>
                        <td><strong><?= isset($schedule['nama_guru']) ? esc($schedule['nama_guru']) : '-' ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong>NIP Guru</strong></td>
                        <td>:</td>
                        <td><?= !empty($schedule['nip_guru']) ? esc($schedule['nip_guru']) : '-' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Mata Pelajaran</strong></td>
                        <td>:</td>
                        <td><?= isset($schedule['mata_pelajaran']) && $schedule['mata_pelajaran'] !== '' ? esc($schedule['mata_pelajaran']) : (isset($schedule['guru_mata_pelajaran']) ? esc($schedule['guru_mata_pelajaran']) : '-') ?></td>
                    </tr>
                    <tr>
                        <td><strong>Materi Supervisi</strong></td>
                        <td>:</td>
                        <td><?= !empty($schedule['materi_supervisi']) ? esc($schedule['materi_supervisi']) : '-' ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td width="35%"><strong>Tahun Ajaran</strong></td>
                        <td width="5%">:</td>
                        <td><?= esc($schedule['tahun_ajar'] ?? '-') ?> (<?= esc($schedule['semester'] ?? '-') ?>)</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Supervisi</strong></td>
                        <td>:</td>
                        <td><?= !empty($schedule['tanggal_supervisi']) ? date('d M Y', strtotime($schedule['tanggal_supervisi'])) : '-' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Kelas</strong></td>
                        <td>:</td>
                        <td><?= isset($schedule['kelas']) ? esc($schedule['kelas']) : '-' ?></td>
                    </tr>
                    <tr>
                        <td><strong>Supervisor / Penilai</strong></td>
                        <td>:</td>
                        <td>
                            <span class="badge badge-light border text-dark">
                                <i class="fas fa-user-tie mr-1"></i> <?= !empty($schedule['nama_supervisor']) ? esc($schedule['nama_supervisor']) : 'Supervisor Pembina' ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
