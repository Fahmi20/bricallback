<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Produksi">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Produksi/Request_Permintaan_Barang">Request Permintaan Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Permintaan Barang Detail</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Permintaan Barang Detail</h4>
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
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <a href="<?= base_url() ?>Produksi/Permintaan_Barang"><button class="btn btn-secondary btn-sm"><i data-feather="arrow-left-circle"></i> Back</button></a>
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
<script src="<?= base_url() ?>assets/public/js/request_permintaan_barang_detail.js"></script>