<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data Stok Barang</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Stok Barang</h4>
                <p class="mt-3">Search Berdasarkan Id Barang</p>
                <div class="input-group">
                    <select id="id_barang" class="form-control" multiple="multiple"></select>
                    <span class="input-group-text input-group-addon"><i data-feather="search"></i></span>
                </div>
                <button class="mt-3 btn btn-primary btn-sm" style="width: 100%;" id="view_all"><i data-feather="eye"></i> View All</button>
            </div>
        </div>
    </div>

    <div id="search">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Stok Barang</h4>
                    <div class="table-responsive" style="margin-top: 10px;">
                        <table id="table_po" style="width: 100%;" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="text-align: center;">Nama Barang</th>
                                    <th style="text-align: center;">Stok Barang</th>
                                </tr>
                            </thead>
                            <tbody id="table_stok_barang">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="search_detail">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Detail Stok Barang</h4>
                    <div class="table-responsive" style="margin-top: 10px;">
                        <table id="table_po" style="width: 100%;" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="text-align: center;">Batch Number</th>
                                    <th style="text-align: center;">Nama Barang</th>
                                    <th style="text-align: center;">Stok Barang</th>
                                </tr>
                            </thead>
                            <tbody id="table_detail_stok_barang">

                            </tbody>
                        </table>
                    </div>
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
<script src="<?= base_url() ?>assets/vendors/select2/select2.min.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>
<!-- End custom js for this page -->

<script src="<?= base_url() ?>assets/public/js/stok_barang.js"></script>