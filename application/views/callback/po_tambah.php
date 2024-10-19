<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory/PO">Data PO</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Data PO</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Data PO</h4>
                <hr>
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <td>Tanggal</td>
                            <td>: <?= date('d-m-Y') ?></td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>: <?= $_SESSION['nama_user'] ?></td>
                        </tr>
                    </tbody>
                </table>
                <h5 class="mt-3" style="text-align: center; background-color:#3affe0;">Stok Barang</h5>
                <hr>
                <p>Jika stok barang dibawah 10 bertanda warna merah yang artinya barang harus di restock ulang <span style="color:red;">*</span></p>
                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_stok" style="width: 100%;" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Kuantiti</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data PO</h4>
                <hr>
                <div class="table-responsive" style="margin-top: 10px;">
                    <form action="" id="form_tambah">
                        <table id="table_tambah" style="width: 100%;" class="table">
                            <thead>
                                <tr style="background-color: aqua;">
                                    <th style="text-align: center; color:black;">Nama Barang</th>
                                    <th style="text-align: center; color:black;">Kuantiti</th>
                                    <th style="text-align: center; color:black;">Keterangan</th>
                                    <th style="text-align: center;"><button id="add_row" type="button" class="btn btn-primary"><i class="fa fa-plus-square"></i></button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="id_barang[]" id="select2barang" class="form-control" style="width: 100%;" required></select>
                                    </td>
                                    <td>
                                        <input type="number" name="qty_po[]" min="0" class="form-control" required>
                                    </td>
                                    <td>
                                        <textarea name="ket_po[]" cols="30" rows="10" class="form-control" required></textarea>
                                    </td>
                                    <td style="text-align: center;">
                                    </td>
                                </tr>
                            </tbody>
                            <tbody id="table_tambah_loop">

                            </tbody>
                        </table>
                        <button type="submit" class="mt-2 btn btn-success" id="simpan"><i class="fa fa-save"></i> Simpan</button>
                        <a href="<?= base_url() ?>Inventory/PO"><button class="mt-2 btn btn-secondary" type="button"><i class="fa fa-arrow-circle-left"></i> Back</button></a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
<!-- endinject -->


<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/jquery-validation/jquery.validate.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/datatables.net/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>

<script src="<?= base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/select2/select2.min.js"></script>
<!-- End custom js for this page -->
<script src="<?= base_url() ?>assets/public/js/po_tambah.js"></script>