<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data Transaksi</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Data Transaksi</h4>
                <div class="row g-3">
                    <!-- Input Start Date -->
                    <div class="col-md-6">
                        <label for="startDate" class="form-label fw-bold">Start Date</label>
                        <input type="text" class="form-control" id="startDate" placeholder="Select Start Date"
                            autocomplete="off">
                    </div>
                    <!-- Button Search -->
                    <div class="col-md-12 mt-4">
                        <button id="search" class="btn btn-primary btn-lg w-100">
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="transaksi_masuk">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Transaksi Masuk</h4>
                    <div class="table-responsive mt-3">
                        <table id="table_po" class="table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID PARTNER</th>
                                    <th>NO CUSTOMER</th>
                                    <th>NOMOR VIRTUAL ACCOUNT</th>
                                    <th>TRX ID</th>
                                    <th>TOTAL AMOUNT</th>
                                    <th>TANGGAL TRANSAKSI</th>
                                    <th>ADDITIONAL INFO</th>
                                    <th>CHANNEL</th>
                                    <th>DESKRIPSI</th>
                                    <th>SUMBER AKUN VA</th>
                                    <th>TELLER ID</th>
                                </tr>
                            </thead>
                            <tbody id="table_transaksi_masuk"></tbody>
                        </table>
                    </div>
                    <button id="print" class="mt-3 btn btn-secondary btn-sm"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
<!-- endinject -->


<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>

<script src="<?= base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>
<!-- End custom js for this page -->

<script src="<?= base_url() ?>assets/public/js/laporan_barang_masuk.js"></script>