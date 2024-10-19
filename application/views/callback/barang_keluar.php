<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data Barang Keluar</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Barang Keluar</h4>
                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="barang_keluar" style="width: 100%;" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="text-align: center;">Tgl Barang Keluar</th>
                                <th style="text-align: center;">Batch Number</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Departement</th>
                                <th style="text-align: center;">Kuantiti</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                    </table>
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
<script src="<?= base_url() ?>assets/vendors/datatables.net/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>
<!-- End custom js for this page -->
<script src="<?= base_url() ?>assets/public/js/barang_keluar.js"></script>