<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan Permintaan Barang</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Laporan Permintaan Barang</h4>
                <div class="row">
                    <div class="col-6">
                        <div class="input-group date datepicker" id="datePickerStart">
                            <input type="text" class="form-control" id="start_date" placeholder="Start Date">
                            <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group date datepicker" id="datePickerEnd">
                            <input type="text" class="form-control" id="end_date" placeholder="End Date">
                            <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <button id="search" class="btn btn-primary" style="width: 100%;"> Search</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="permintaan_barang">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Permintaan Barang</h4>
                    <div class="table-responsive mt-3">
                        <table id="table_po" style="width: 100%;" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="text-align: center;">Tanggal Permintaan</th>
                                    <th style="text-align: center;">Departement</th>
                                    <th style="text-align: center;">Status Permintaan</th>
                                    <th style="text-align: center;">Qty</th>
                                </tr>
                            </thead>
                            <tbody id="table_permintaan_barang">
                            </tbody>
                        </table>
                    </div>
                    <button id="print" class="mt-3 btn btn-secondary btn-sm"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
        </div>
    </div>

    <div id="doc_print">
        <style>
            .table-bordered thead th {
                border: 2px solid;
            }

            .table-bordered tbody td {
                border: 2px solid;
            }

            .table-bordered {
                border-color: black;
                width: 100%;
                border-collapse: collapse;
            }
        </style>
        <div class="col-lg-12">
            <hr>
            <h4 style="text-align: center;">Data Permintaan Barang</h4>
            <hr>
            <div class="table-responsive mt-3">
                <table id="table_po" style="width: 100%;" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th style="text-align: center;">Tanggal Permintaan</th>
                            <th style="text-align: center;">Departement</th>
                            <th style="text-align: center;">Status Permintaan</th>
                            <th style="text-align: center;">Qty</th>
                        </tr>
                    </thead>
                    <tbody id="print_table_permintaan_barang">
                    </tbody>
                </table>
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

<script src="<?= base_url() ?>assets/public/js/laporan_permintaan_barang.js"></script>