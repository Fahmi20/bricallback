<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data Barang Masuk</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Data Barang Masuk</h6>
                <a href="<?= base_url() ?>Inventory/Tambah_Data_Barang_Masuk"><button type="button" class="btn btn-primary"><i data-feather="plus"></i> Tambah Data</button></a>

                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_barang_masuk" style="width: 100%;" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="text-align: center;">Batch Number</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: center;">Satuan</th>
                                <th style="text-align: center;">Tanggal Kedatangan</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <form action="" id="form_edit">
                <div class="modal-body">
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Tanggal Datang</label>
                        <input type="hidden" name="id" id="id_barang_masuk_edit">
                        <input type="text" name="tgl_datang" id="tgl_datang" readonly class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Nama Supplier</label>
                        <select class="form-select" id="select2supplier" name="id_supplier" data-width="100%" required></select>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Batch Number</label>
                        <input type="text" name="batch_number" id="batch_number_edit" readonly class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Nama Barang</label>
                        <select class="form-select" id="select2barang" name="id_barang" data-width="100%" required></select>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Satuan</label>
                        <select class="form-select" id="select2satuan" name="satuan" data-width="100%" required></select>
                    </div>
                    <div class="form-group">
                        <label for="">Qty</label>
                        <input type="hidden" class="form-control" name="qty_old" id="qty_old" required>
                        <input type="number" class="form-control" name="qty" min="0" id="qty_edit" required>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Tanggal Expired</label>
                        <div class="input-group date datepicker" id="exp_date">
                            <input type="text" class="form-control" name="expired_date" autocomplete="off" required>
                            <span class="input-group-text input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="save_edit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="hapus" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Hapus Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <form action="" id="form_delete">
                <div class="modal-body">
                    <h5><i data-feather="trash-2" style="color:red"></i> Anda Yakin Untuk Menghapus Data?</h5>
                    <input type="hidden" name="id" id="id_barang_masuk_hapus">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No!</button>
                    <button type="button" id="save_delete" class="btn btn-primary">Yes, Delete It!</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
<!-- endinject -->

<!-- Plugin js for this page -->
<script src="<?= base_url() ?>assets/vendors/datatables.net/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
<script src="<?= base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.js"></script>

<script src="<?= base_url() ?>assets/vendors/jquery-validation/jquery.validate.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/inputmask/jquery.inputmask.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/select2/select2.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/typeahead.js/typeahead.bundle.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/jquery-tags-input/jquery.tagsinput.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/dropzone/dropzone.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/dropify/dist/dropify.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/bootstrap-colorpicker/bootstrap-colorpicker.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/moment/moment.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/tempusdominus-bootstrap-4/tempusdominus-bootstrap-4.js"></script>
<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/data-table.js"></script>
<!-- End custom js for this page -->
<script src="<?= base_url() ?>assets/public/js/barang_masuk.js"></script>