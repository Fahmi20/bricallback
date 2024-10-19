<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="position-relative">
                <figure class="overflow-hidden mb-0 d-flex justify-content-center">
                    <img src="<?= base_url() ?>assets/inven2.jpg" width="1560px" class="rounded-top" alt="profile cover">
                </figure>
                <div class="d-flex justify-content-between align-items-center position-absolute top-90 w-100 px-2 px-md-4 mt-n4">
                    <div>
                        <img id="profile" class="wd-70 rounded-circle" src="" alt="profile">
                        <span class="h4 ms-3 text-dark" id="nama_user"></span>
                    </div>
                    <div class="d-none d-md-block">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center p-3 rounded-bottom">
                <ul class="d-flex align-items-center m-0 p-0">
                    <li class="d-flex align-items-center active">
                    </li>
                    <li class="ms-3 ps-3 border-start d-flex align-items-center">
                    </li>
                    <li class="ms-3 ps-3 border-start d-flex align-items-center">
                    </li>
                    <li class="ms-3 ps-3 border-start d-flex align-items-center">
                    </li>
                    <li class="ms-3 ps-3 border-start d-flex align-items-center">
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="row profile-body">
    <!-- left wrapper start -->
    <div class="d-none d-md-block col-md-4 col-xl-3 left-wrapper">
        <div class="card rounded">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="card-title mb-0">Nama</h6>
                    <input type="hidden" id="id_user" value="<?= $id ?>">
                </div>
                <p id="nama_user_profile"></p>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Email:</label>
                    <p class="text-muted" id="email"></p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Jenis Kelamin:</label>
                    <p class="text-muted" id="jk"></p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">No Telp:</label>
                    <p class="text-muted" id="no_telp"></p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Departement:</label>
                    <p class="text-muted" id="departement"></p>
                </div>
                <div class="mt-3">
                    <label class="tx-11 fw-bolder mb-0 text-uppercase">Role:</label>
                    <p class="text-muted" id="role"></p>
                </div>
            </div>
        </div>
    </div>
    <!-- left wrapper end -->
    <!-- middle wrapper start -->
    <div class="col-md-8 col-xl-6 middle-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="card rounded">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <h4>Update User</h4>
                            </div>
                        </div>
                    </div>
                    <form action="" id="form_edit" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="">Nama <span style="color: red;">*</span></label>
                                <input type="hidden" name="id" id="id">
                                <input type="hidden" name="old_foto" id="old_foto">
                                <input type="text" name="nama_user" id="nama_user_form" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="">Jenis Kelamin <span style="color: red;">*</span></label>
                                <select name="jk" class="form-control" id="jk_form" style="width: 100%;" required></select>
                            </div>
                            <div class="mb-3">
                                <label for="">No Telp <span style="color: red;">*</span></label>
                                <input type="text" class="form-control mb-4 mb-md-0" name="no_telp" id="no_telp_form" data-inputmask-alias="(+99) 9999-9999-999" required />
                            </div>
                            <div class="mb-3">
                                <label for="">Departement <span style="color: red;">*</span></label>
                                <select name="id_departement" class="form-control" id="departement_form" style="width: 100%;" required></select>
                            </div>
                            <div class="mb-3">
                                <label for="">Role <span style="color: red;">*</span></label>
                                <select name="role" class="form-control" id="role_form" style="width: 100%;" required></select>
                            </div>
                            <div class="mb-3">
                                <label for="">Email <span style="color: red;">*</span></label>
                                <input type="text" class="form-control mb-4 mb-md-0" id="email_form" name="email" data-inputmask="'alias': 'email'" required />
                            </div>
                            <div class="mb-3">
                                <label for="">Password <span style="color: red;">*</span></label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="">Confirm Password <span style="color: red;">*</span> <span id="message"></span> </label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="">Update Foto</label>
                                <input type="file" id="foto" name="foto" accept=".jpg, .png, .jpeg" class="form-control">
                            </div>
                        </div>

                        <div class="card-footer">
                            <!-- <div class="d-flex post-actions"> -->
                            <button id="save" type="submit" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
                            <button id="back" class="btn btn-secondary"><i class="fa fa-arrow-circle-o-left"></i> Back</button>
                            <!-- </div> -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- middle wrapper end -->
</div>

<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>

<script src="<?= base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.js"></script>

<script src="<?= base_url() ?>assets/vendors/select2/select2.min.js"></script>
<!-- endinject -->

<!-- Plugin js for this page -->
<script src="<?= base_url() ?>assets/vendors/jquery-validation/jquery.validate.min.js"></script>
<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<!-- endinject -->
<script src="<?= base_url() ?>assets/public/js/user_detail.js"></script>