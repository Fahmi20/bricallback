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
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i data-feather="filter"></i> Filter Notifikasi BRIVA
                </button>
                <!-- Modal for Filtering Notifications -->
                <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="filterModalLabel">Filter Notifikasi BRIVA</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form id="filterForm">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="partnerServiceId">Partner Service ID</label>
                                        <input type="text" id="partnerServiceId" name="partnerServiceId"
                                            class="form-control" required placeholder="Masukkan Partner Service ID">
                                    </div>
                                    <div class="form-group">
                                        <label for="customerNo">Customer Number</label>
                                        <input type="text" id="customerNo" name="customerNo" class="form-control"
                                            required placeholder="Masukkan Customer Number">
                                    </div>
                                    <div class="form-group">
                                        <label for="virtualAccountNo">Virtual Account Number</label>
                                        <input type="text" id="virtualAccountNo" name="virtualAccountNo"
                                            class="form-control" required placeholder="Masukkan Virtual Account Number">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Cari Notifikasi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

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