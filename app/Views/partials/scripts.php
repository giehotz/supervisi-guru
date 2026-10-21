<!-- Core Tabler JS -->
<script src="<?= base_url('assets/tabler/js/tabler.min.js') ?>"></script>

<!-- jQuery (Required by DataTables & Existing View Scripts) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<!-- DataTables Core & Bootstrap 5 Integration -->
<script src="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/tabler/vendor/datatables/dataTables.bootstrap5.min.js') ?>"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Compatibility Shim JS -->
<script src="<?= base_url('assets/js/compat-shim.js') ?>"></script>

<!-- Custom scripts per-page -->
<?= $this->renderSection('scripts') ?>
