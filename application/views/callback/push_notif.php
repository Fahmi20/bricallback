<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Backend">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data Push Notifikasi BRIVA</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Data Notifikasi BRIVA</h6>
                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_push_notifications" class="table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Partner Service ID</th>
                                <th>Customer No</th>
                                <th>Virtual Account No</th>
                                <th>Payment Request ID</th>
                                <th>Transaction Date/Time</th>
                                <th>Payment Amount</th>
                                <th>Terminal ID</th>
                                <th>Bank ID</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan diisi oleh DataTables via AJAX -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
<!-- End core:js -->

<!-- Plugin js for this page -->
<script src="<?= base_url() ?>assets/vendors/datatables.net/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
<script src="<?= base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.js"></script>
<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/jquery-validation/jquery.validate.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<script src="<?= base_url() ?>assets/public/js/departement.js"></script>