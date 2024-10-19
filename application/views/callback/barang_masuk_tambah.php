<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="#" id="data_barang_masuk">Data Barang Masuk</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Data Barang Masuk</li>
    </ol>
</nav>

<div class="row">
    <form action="" id="form_barang">
        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Data Barang Masuk</h4>
                    <hr>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Kedatangan</label>
                        <div class="input-group date datepicker">
                            <input type="text" class="form-control" id="tanggal_kedatangan" name="tanggal_kedatangan" value="<?= date('d-m-Y') ?>" readonly>
                            <span class="input-group-text input-group-addon"><i data-feather="calendar"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Supplier</label>
                        <select class="form-select" id="select2supplier" name="id_supplier" data-width="100%" required></select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Barang</h4>
                    <p><small>Batch Number Akan Terisi Otomatis Jika Sudah Memilih Barang <i style="color: red;">*</i></small></p>
                    <hr>

                    <table id="table_barang" class="table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Batch Number</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Satuan</th>
                                <th style="text-align: center;">QTY</th>
                                <th style="text-align: center;">Expired Date</th>
                                <th style="text-align: center;"><button type="button" id="tambah_barang" class="btn btn-primary"><i data-feather="plus-circle"></i></button></th>
                            </tr>
                        </thead>
                        <tbody id="body_barang">
                            <tr>
                                <td>
                                    <input type="text" id="batch_number1" class="form-control" name="batch_number[]" readonly>
                                    <input type="hidden" id="id_batch1" name="id_batch">
                                    <input type="hidden" id="id_barang1" name="id_barang[]">
                                </td>
                                <td>
                                    <select class="form-select" id="select2barang1" name="selectbarang_val[]" data-width="100%" required></select>
                                </td>
                                <td>
                                    <select class="form-select" id="select2satuan" name="satuan[]" data-width="100%" required>
                                        <option value=""></option>
                                        <option value="Pcs">Pcs</option>
                                        <option value="Box">Box</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Ball">Ball</option>
                                        <option value="Karung">Karung</option>
                                        <option value="Liter">Liter</option>
                                        <option value="Kaleng">Kaleng</option>
                                        <option value="Palet">Palet</option>
                                        <option value="Ikat">Ikat</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="qty[]" min="0" id="qty" required>
                                </td>
                                <td>
                                    <div class="input-group date datepicker" id="exp_date">
                                        <input type="text" class="form-control" name="expired_date[]" autocomplete="off" required>
                                        <span class="input-group-text input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" id="hapus_barang1" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <button type="submit" id="simpan" style="width: 10%;" class="btn btn-success"><i class="fa fa-save"></i> Simpan</button>
            <button type="button" id="print" style="width: 20%;" class="btn btn-primary"><i class="fa fa-print"></i> Print Batch Number</button>
            <button type="button" class="btn btn-secondary" id="back" style="width: 10%;"><i class="fa fa-arrow-left"></i> Kembali</button>
        </div>
    </form>

</div>

<div id="doc_print">
    <table style="width: 100%;">
        <thead>

        </thead>
        <tbody id="label_print" class="table-bordered">
            <tr id="row1">
                <td>
                    <h2 id="batch_print1"></h2>
                    <p id="tgl_print1"></p>
                    <hr>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
<!-- endinject -->

<!-- Plugin js for this page -->
<script src="<?= base_url() ?>assets/vendors/chartjs/Chart.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/jquery.flot/jquery.flot.js"></script>
<script src="<?= base_url() ?>assets/vendors/jquery.flot/jquery.flot.resize.js"></script>
<script src="<?= base_url() ?>assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/apexcharts/apexcharts.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.js"></script>

<script src="<?= base_url() ?>assets/vendors/select2/select2.min.js"></script>
<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/jquery-validation/jquery.validate.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<!-- End custom js for this page -->
<script src="<?= base_url() ?>assets/public/js/barang_masuk_tambah.js"></script>