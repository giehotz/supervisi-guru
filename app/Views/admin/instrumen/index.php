<?= $this->extend('layouts/admin'); ?>

<?= $this->section('content'); ?>
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">Supervisi</div>
                <h2 class="page-title">Manajemen Instrumen Supervisi</h2>
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
                    <div>
                        <i class="fas fa-check-circle me-2"></i>
                    </div>
                    <div>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex">
                    <div>
                        <i class="fas fa-exclamation-circle me-2"></i>
                    </div>
                    <div>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php $currentTab = $activeTab ?? 'jenis'; ?>

        <!-- Nav Tabs -->
        <ul class="nav nav-tabs mb-3" id="instrumenTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= ($currentTab === 'jenis') ? 'active' : '' ?>" id="jenis-tab" data-bs-toggle="tab" href="#jenis-pane" role="tab" aria-controls="jenis-pane" aria-selected="<?= ($currentTab === 'jenis') ? 'true' : 'false' ?>">
                    <i class="fas fa-layer-group me-2"></i> Jenis Penilaian
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= ($currentTab === 'aspek') ? 'active' : '' ?>" id="aspek-tab" data-bs-toggle="tab" href="#aspek-pane" role="tab" aria-controls="aspek-pane" aria-selected="<?= ($currentTab === 'aspek') ? 'true' : 'false' ?>">
                    <i class="fas fa-list-check me-2"></i> Aspek Penilaian
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="instrumenTabContent">
            <div class="tab-pane fade <?= ($currentTab === 'jenis') ? 'show active' : '' ?>" id="jenis-pane" role="tabpanel" aria-labelledby="jenis-tab">
                <?= $this->include('admin/instrumen/partials/tab_jenis_penilaian') ?>
            </div>
            <div class="tab-pane fade <?= ($currentTab === 'aspek') ? 'show active' : '' ?>" id="aspek-pane" role="tabpanel" aria-labelledby="aspek-tab">
                <?= $this->include('admin/instrumen/partials/tab_aspek_penilaian') ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
window.showTab = function(selector) {
    var triggerEl = document.querySelector(selector);
    if (triggerEl) {
        if (window.bootstrap && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(triggerEl).show();
        } else if (window.jQuery) {
            $(triggerEl).tab('show');
        }
    }
};

$(document).ready(function() {
    // Initialize DataTables for both tabs
    if (!$.fn.DataTable.isDataTable('#dataTableJenis')) {
        $('#dataTableJenis').DataTable({
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

    // Adjust columns on tab switch
    $('a[data-bs-toggle="tab"], a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        var targetId = $(e.target).attr('href');
        var tabName = (targetId === '#aspek-pane') ? 'aspek' : 'jenis';
        var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabName;
        window.history.replaceState({ path: newUrl }, '', newUrl);
    });

    // Check URL query param or hash on initial load
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var hashParam = window.location.hash;

    if (tabParam === 'aspek' || hashParam === '#aspek' || hashParam === '#aspek-pane') {
        window.showTab('#aspek-tab');
    } else if (tabParam === 'jenis' || hashParam === '#jenis' || hashParam === '#jenis-pane') {
        window.showTab('#jenis-tab');
    }

    // Auto-increment urutan for create modal
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

window.filterAspekByJenis = function(jenisName) {
    if ($.fn.DataTable.isDataTable('#dataTableAspek')) {
        $('#dataTableAspek').DataTable().column(1).search(jenisName ? '^' + $.fn.dataTable.util.escapeRegex(jenisName) + '$' : '', true, false).draw();
    }
};
</script>
<?= $this->endSection(); ?>
