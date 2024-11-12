<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dasbor</a></li>
        <li class="breadcrumb-item active" aria-current="page">Riwayat Create VA</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Riwayat Status Create VA</h6>
                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_va" style="width: 100%;" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="text-align: center;"></th>
                                <th style="text-align: center;"></th>
                                <th style="text-align: center;"></th>
                                <th style="text-align: center;"></th>
                                <th style="text-align: center;"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
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
<script src="<?= base_url() ?>assets/public/js/riwayat_create_va.js"></script>
<!-- End custom js for this page -->