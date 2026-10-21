<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Supervisi</div>
                <h2 class="page-title">Manajemen Aspek Penilaian</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="<?= base_url('/admin/instrumen') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Instrumen
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <div><i class="fas fa-check-circle me-2"></i></div>
                    <div><?= session()->getFlashdata('success') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <div><i class="fas fa-exclamation-circle me-2"></i></div>
                    <div><?= session()->getFlashdata('error') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?= $this->include('admin/instrumen/partials/tab_aspek_penilaian') ?>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    if (!$.fn.DataTable.isDataTable('#dataTableAspek')) {
        $('#dataTableAspek').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ entri keseluruhan)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    }

    var maxUrutanData = <?= json_encode($max_urutan ?? []) ?>;
    $('#jenisPenilaianCreate').on('change', function() {
        var selectedJenisId = $(this).val();
        if (selectedJenisId) {
            var maxUrutan = maxUrutanData[selectedJenisId] || 0;
            $('#urutanCreate').val(parseInt(maxUrutan) + 1);
        } else {
            $('#urutanCreate').val('');
        }
    });
});
</script>
<?= $this->endSection(); ?>