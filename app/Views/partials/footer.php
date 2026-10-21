<footer class="footer footer-transparent d-print-none mt-auto">
    <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-lg-auto ms-lg-auto">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">Sistem Supervisi Guru</li>
                    <li class="list-inline-item">v1.0.0</li>
                </ul>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        Copyright &copy; <?= date('Y') ?>
                        <a href="<?= base_url() ?>" class="link-secondary">Sistem Supervisi</a>. All rights reserved.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!-- Global Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                <h3 class="modal-title mb-1" id="logoutModalLabel">Konfirmasi Logout</h3>
                <div class="text-secondary">Apakah Anda yakin ingin mengakhiri sesi login saat ini?</div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">Batal</button>
                        </div>
                        <div class="col">
                            <a href="<?= base_url('/auth/logout') ?>" class="btn btn-danger w-100">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
