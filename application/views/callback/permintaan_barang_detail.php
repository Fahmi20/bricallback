<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory/Data_Permintaan_Barang">Data Permintaan Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Permintaan Barang Proses</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Permintaan Barang Proses</h4>
                <hr>
                <input type="hidden" id="id" value="<?= $id ?>">
                <div id="permintaan_barang"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Detail Barang</h4>
                <hr>
                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_barang" style="width: 100%;" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Kuantiti</th>
                                <th style="text-align: center;">Keterangan Permintaan</th>
                                <th style="text-align: center;">Kuantiti Diserahkan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="barang_keluar"></div>


    <a href="<?= base_url() ?>Inventory/Data_Permintaan_Barang"><button class="btn btn-secondary btn-sm"><i data-feather="arrow-left-circle"></i> Back</button></a>
</div>

<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
<!-- endinject -->


<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/datatables.net/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>
<!-- End custom js for this page -->
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="<?= base_url() ?>assets/public/js/permintaan_barang_detail.js"></script>