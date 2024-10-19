<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory/PO">Data PO</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail PO</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <!-- <h4 class="card-title">Detail PO</h4> -->
                <hr>
                <h4 style="text-align: center;" class="card-title">DETAIL PURCHASE ORDER</h4>
                <input type="hidden" id="id" value="<?= $id ?>">
                <hr>

                <div class="row" style="height: 50px;">
                    <div class="col-12">
                        <div id="table_head"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h4 style="text-align: Center;">Detail Barang</h4>
                        <br>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="text-align: center;color:black">No</th>
                                    <th style="text-align: center;color:black">Nama Barang</th>
                                    <th style="text-align: center;color:black">Kuantiti</th>
                                    <th style="text-align: center;color:black">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="detail_barang">
                            </tbody>
                            <tbody id="total"></tbody>
                        </table>

                        <a href="<?= base_url() ?>Inventory/PO"><button class="mt-3 btn btn-secondary"><i class="fa fa-arrow-circle-left"></i> Back</button></a>
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
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>
<!-- End custom js for this page -->
<script src="<?= base_url() ?>assets/public/js/po_detail.js"></script>