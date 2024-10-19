<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
    <div>
        <h4 class="mb-3 mb-md-0">Welcome to Dashboard Manager</h4>
    </div>
</div>

<div class="row">
    <div class="col-12 col-xl-12 stretch-card">
        <div class="row flex-grow-1">
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Barang Masuk</h6>
                        </div>
                        <div class="row">
                            <div class="mt-5 col-6 col-md-12 col-xl-5" style="text-align: center;">
                                <div id="t_barang_masuk"></div>
                            </div>
                            <div class="mt-4 col-6 col-md-12 col-xl-7">
                                <img src="<?= base_url() ?>assets/in-stock.png" style="width: 60px;" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Barang Keluar</h6>
                        </div>
                        <div class="row">
                            <div class="mt-5 col-6 col-md-12 col-xl-5" style="text-align: center;">
                                <div id="t_barang_keluar"></div>
                            </div>
                            <div class="mt-4 col-6 col-md-12 col-xl-7">
                                <img src="<?= base_url() ?>assets/out-of-stock.png" style="width: 60px;" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Permintaan Barang</h6>
                        </div>
                        <div class="row">
                            <div class="mt-5 col-6 col-md-12 col-xl-5" style="text-align: center;">
                                <div id="t_permintaan_barang"></div>
                            </div>
                            <div class="mt-4 col-6 col-md-12 col-xl-7">
                                <img src="<?= base_url() ?>assets/request.png" style="width: 60px;" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <h6 class="card-title mb-0">Purchase Order</h6>
                        </div>
                        <div class="row">
                            <div class="mt-5 col-6 col-md-12 col-xl-5" style="text-align: center;">
                                <div id="t_po"></div>
                            </div>
                            <div class="mt-4 col-6 col-md-12 col-xl-7">
                                <img src="<?= base_url() ?>assets/po.png" style="width: 60px;" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- row -->


<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
<!-- endinject -->

<!-- Plugin js for this page -->
<script src="<?= base_url() ?>assets/vendors/chartjs/Chart.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/jquery.flot/jquery.flot.js"></script>
<script src="<?= base_url() ?>assets/vendors/jquery.flot/jquery.flot.resize.js"></script>
<script src="<?= base_url() ?>assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/apexcharts/apexcharts.min.js"></script>
<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>
<!-- End custom js for this page -->

<script src="<?= base_url() ?>assets/public/js/dashboard.js"></script>