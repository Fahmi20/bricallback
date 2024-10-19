<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Data User</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Data User</h6>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah"><i data-feather="plus"></i> Tambah Data</button>

                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_user" style="width: 100%;" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="text-align: center;">Nama</th>
                                <th style="text-align: center;">Jenis Kelamin</th>
                                <th style="text-align: center;">No Telp</th>
                                <th style="text-align: center;">Email</th>
                                <th style="text-align: center;">Role</th>
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

<div class="modal fade" id="tambah" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <form action="" id="form_tambah" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="">Nama</label>
                        <input type="text" name="nama_user" id="nama_user" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Jenis Kelamin</label>
                        <select name="jk" id="jk" class="form-control" style="width: 100%;" required></select>
                    </div>
                    <div class="form-group">
                        <label for="">No Telp</label>
                        <input type="text" class="form-control mb-4 mb-md-0" name="no_telp" id="no_telp" data-inputmask-alias="(+99) 9999-9999-999" required />
                    </div>
                    <div class="form-group">
                        <label for="">Departement</label>
                        <select name="id_departement" id="departement" class="form-control" style="width: 100%;" required></select>
                    </div>
                    <div class="form-group">
                        <label for="">Role</label>
                        <select name="role" id="role" class="form-control" style="width: 100%;" required></select>
                    </div>
                    <div class="form-group">
                        <label for="">Email</label>
                        <input type="text" class="form-control mb-4 mb-md-0" name="email" id="email" data-inputmask="'alias': 'email'" required />
                    </div>
                    <div class="form-group">
                        <label for="">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Confirm Password <span id="message"></span></label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Foto</label>
                        <input type="file" name="foto" id="foto" accept=".jpg, .png, .jpeg" class="form-control">
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
                    <input type="hidden" name="id" id="id_user_hapus">
                    <input type="hidden" name="foto" id="foto_hapus">
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
<script src="<?= base_url() ?>assets/public/js/user.js"></script>