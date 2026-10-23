<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
<meta http-equiv="X-UA-Compatible" content="ie=edge"/>
<script>
    try {
        if (localStorage.getItem('sidebar_collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    } catch (e) {}
</script>

<!-- Inter Font (modern) -->
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<style>:root { --tblr-font-sans-serif: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }</style>

<!-- Tabler Core CSS -->
<link href="<?= base_url('assets/tabler/css/tabler.min.css') ?>" rel="stylesheet"/>
<link href="<?= base_url('assets/tabler/css/tabler-vendors.min.css') ?>" rel="stylesheet"/>

<!-- Font Awesome for Icon Compatibility across 117 views -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css"/>

<!-- DataTables BS5 CSS -->
<link href="<?= base_url('assets/tabler/vendor/datatables/dataTables.bootstrap5.min.css') ?>" rel="stylesheet"/>

<!-- Compatibility Shim CSS for SB Admin / Bootstrap 4 utilities -->
<link href="<?= base_url('assets/css/compat-shim.css') ?>" rel="stylesheet"/>

<!-- Sidebar polish -->
<link href="<?= base_url('assets/css/sidebar.css') ?>" rel="stylesheet"/>
