<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Simulator</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title text-center">SIMULATOR</h6>

                <!-- Stepper -->
                <div class="stepper d-flex justify-content-between mb-4">
                    <!-- Step 1: Inquiry -->
                    <div class="step text-center active">
                        <div class="circle bg-white border border-success rounded-circle p-3 mb-2 d-flex justify-content-center align-items-center"
                            style="width: 50px; height: 50px;">
                            <i class="link-icon" data-feather="log-in" style="color: #28a745;"></i>
                        </div>
                        <p class="fw-bold" style="color: #28a745;">INQUIRY</p>
                    </div>

                    <!-- Step 2: Pembayaran -->
                    <div class="step text-center d-flex flex-column align-items-center justify-content-center">
                        <div class="circle bg-white border border-secondary rounded-circle p-3 mb-2 d-flex justify-content-center align-items-center"
                            id="step-pembayaran" style="width: 50px; height: 50px;">
                            <i id="credit-card-icon" class="credit-card-icon" data-feather="credit-card"
                                style="color: #6c757d;"></i>
                        </div>
                        <p id="pembayaran-step-text" style="color: #6c757d;">PEMBAYARAN</p>
                    </div>

                    <!-- Step 3: Selesai -->
                    <div class="step text-center" id="step-selesai">
                        <div class="circle bg-white border border-secondary rounded-circle p-3 mb-2 d-flex justify-content-center align-items-center"
                            style="width: 50px; height: 50px;">
                            <i data-feather="check-circle" id="success-icon"
                                style="font-size: 1.5rem; color: #6c757d;"></i>
                        </div>
                        <p id="text-selesai" style="color: #6c757d;">SELESAI</p>
                    </div>

                </div>

                <!-- Inquiry Form -->
                <form id="form_inquiry">
                    <div class="mb-3">
                        <label for="virtualAccountNo" class="form-label" style="font-size: 14px; color: grey;">
                            NOMOR VIRTUAL ACCOUNT
                        </label>
                        <input type="text" id="virtualAccountNo" class="form-control" placeholder="1234"
                            style="height: 50px; font-size: 18px; border-radius: 10px;" required>
                    </div>
                    <button type="button" id="inquiryButton" class="btn btn-success w-100"
                        style="height: 50px; font-size: 18px; border-radius: 10px;">
                        INQUIRY
                    </button>
                </form>

                <div id="detail_information" class="card p-3 shadow-sm"
                    style="border-radius: 15px; background-color: #f8f9fa; display: none;">
                    <h5 class="mt-4 mb-3 text-center text-uppercase fw-bold"
                        style="letter-spacing: 1px; color: #343a40;">Tagihan</h5>

                    <!-- NOMOR CUSTOMER -->
                    <p><i class="bi bi-person me-2" style="color: #007bff;"></i><strong>NOMOR CUSTOMER:</strong> <span
                            id="customer_number" class="text-muted"></span></p>

                    <!-- NAMA CUSTOMER -->
                    <p><i class="bi bi-person-badge me-2" style="color: #007bff;"></i><strong>NAMA:</strong> <span
                            id="customer_name" class="text-muted"></span></p>
                    <!-- NOMOR VIRTUAL ACCOUNT -->
                    <p><i class="bi bi-credit-card me-2" style="color: #007bff;"></i><strong>NOMOR VIRTUAL
                            ACCOUNT:</strong> <span id="va_number" class="text-muted"></span></p>



                    <hr>

                    <!-- TOTAL NOMINAL -->
                    <p><strong>TOTAL NOMINAL:</strong>
                        <span id="total_amount" style="font-size: 24px; font-weight: bold; color: #28a745;"></span>
                    </p>

                    <div class="mb-4">
                        <label class="form-label fw-bold">METODE PEMBAYARAN</label>
                        <div class="d-flex gap-3">
                            <button class="btn btn-outline-secondary w-50"
                                style="border-radius: 10px; transition: all 0.3s;">NONE</button>
                            <button class="btn btn-outline-primary w-50"
                                style="border-radius: 10px; transition: all 0.3s;">M-BANKING</button>
                        </div>
                    </div>

                    <!-- REFERENSI -->
                    <div class="mb-4">
                        <label for="trxId" class="form-label fw-bold">REFERENSI</label>
                        <input type="text" id="trxId" class="form-control" value=""
                            style="height: 50px; font-size: 18px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                    </div>

                    <div class="d-flex justify-content-between">
                        <button id="kembaliButton" class="btn btn-secondary w-50 me-2"
                            style="height: 50px; font-size: 18px; border-radius: 10px; transition: all 0.3s;">KEMBALI</button>
                        <button class="btn btn-success w-50"
                            style="height: 50px; font-size: 18px; border-radius: 10px; transition: all 0.3s;">BAYAR</button>
                    </div>
                </div>


                <!-- Spinner Loading -->
                <div id="loadingSpinner" style="display: none;">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
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
<!-- End custom js for this page -->
<script src="<?= base_url() ?>assets/public/js/supplier.js"></script>