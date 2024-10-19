<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory/Request_Permintaan_Barang">Data Permintaan Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Request Permintaan Barang</li>
    </ol>
</nav>

<div class="row">
    <form action="" id="form_tambah">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Request Permintaan Barang</h4>
                    <p><small>Field Departement ini Automatis Terisi Berdasarkan Akun Login <span style="color: red;">*</span></small></p>
                    <hr>
                    <input type="hidden" id="id_user" value="<?= $_SESSION['id_user'] ?>">
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Departement</label>
                        <input type="hidden" name="id_departement" id="id_departement">
                        <input type="text" class="form-control" readonly id="departement">
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Permintaan</label>
                        <input type="text" class="form-control" name="tgl_permintaan" readonly value="<?= date('d-m-Y') ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Data Barang <button id="add_row" type="button" class="btn btn-primary btn-sm" style="float: right;"><i class="fa fa-plus-square-o"></i></button></th>
                            </tr>
                        </thead>
                        <tbody id="row">
                            <tr>
                                <td>
                                    <div class="mb-3">
                                        <label for="barang" class="form-label">Barang</label>
                                        <select class="form-select" name="id_barang[]" id="select2barang" data-width="100%"></select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="qty" class="form-label">Qty</label>
                                        <input type="number" class="form-control" name="qty_permintaan[]" min='0'>
                                    </div>
                                    <div class="mb-3">
                                        <label for="qty" class="form-label">Keterangan Permintaan</label>
                                        <textarea name="ket_permintaan[]" class="form-control"></textarea>
                                    </div>
                                    <hr>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12 grid-margin">
            <button type="submit" id="simpan" class="btn btn-primary" style="width: 10%;"><i class="fa fa-save"></i> Simpan</button>
            <a href="<?= base_url() ?>Inventory/Request_Permintaan_Barang"><button type="button" class="btn btn-secondary"><i data-feather="arrow-left-circle"></i> Back</button></a>
        </div>
    </form>
</div>

<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
<!-- endinject -->

<!-- Plugin js for this page -->
<script src="<?= base_url() ?>assets/vendors/jquery-validation/jquery.validate.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/select2/select2.min.js"></script>
<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>
<!-- End custom js for this page -->
<script src="<?= base_url() ?>assets/public/js/request_permintaan_barang_tambah.js"></script>