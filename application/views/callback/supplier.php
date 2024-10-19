<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data Supplier</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Data Supplier</h6>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah"><i data-feather="plus"></i> Tambah Data</button>

                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_supplier" style="width: 100%;" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="text-align: center;">Nama Supplier</th>
                                <th style="text-align: center;">Alamat</th>
                                <th style="text-align: center;">No Telp</th>
                                <th style="text-align: center;">Email</th>
                                <th style="text-align: center;">PIC</th>
                                <th style="text-align: center;">No Telp PIC</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->

<div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <form action="" id="form_tambah">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Nama Supplier</label>
                        <input type="text" name="nama_supplier" id="nama_supplier" class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control"></textarea>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">No Telp</label>
                        <input type="text" class="form-control mb-4 mb-md-0" name="no_telp" id="no_telp" data-inputmask-alias="(+99) 9999-9999-999" />
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Email</label>
                        <input type="text" class="form-control mb-4 mb-md-0" name="email" id="email" data-inputmask="'alias': 'email'" />
                    </div>
                    <div class="form-group">
                        <label for="">PIC</label>
                        <input type="text" name="pic" id="pic" class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">No Telp PIC</label>
                        <input type="text" class="form-control mb-4 mb-md-0" name="no_telp_pic" id="no_telp_pic" data-inputmask-alias="(+99) 9999-9999-999" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="save_tambah" class="btn btn-primary">Save</button>
                </div>
            </form>
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
                    <div class="form-group">
                        <label for="">Nama Supplier</label>
                        <input type="hidden" name="id" id="id_supplier_edit">
                        <input type="text" name="nama_supplier" id="nama_supplier_edit" class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Alamat</label>
                        <textarea name="alamat" id="alamat_edit" class="form-control"></textarea>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">No Telp</label>
                        <input type="text" class="form-control mb-4 mb-md-0" name="no_telp" id="no_telp_edit" data-inputmask-alias="(+99) 9999-9999-999" />
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">Email</label>
                        <input type="text" class="form-control mb-4 mb-md-0" name="email" id="email_edit" data-inputmask="'alias': 'email'" />
                    </div>
                    <div class="form-group">
                        <label for="">PIC</label>
                        <input type="text" name="pic" id="pic_edit" class="form-control">
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <label for="">No Telp PIC</label>
                        <input type="text" class="form-control mb-4 mb-md-0" name="no_telp_pic" id="no_telp_pic_edit" data-inputmask-alias="(+99) 9999-9999-999" />
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
                    <input type="hidden" name="id" id="id_supplier_hapus">
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
<script src="<?= base_url() ?>assets/public/js/supplier.js"></script>