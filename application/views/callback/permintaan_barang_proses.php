<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory/Data_Permintaan_Barang">Data Permintaan Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Permintaan Barang Proses</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Permintaan Barang Proses</h4>
                <hr>
                <input type="hidden" id="id" value="<?= $id ?>">
                <div id="permintaan_barang"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Detail Barang</h4>
                <hr>
                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_barang" style="width: 100%;" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Kuantiti</th>
                                <th style="text-align: center;">Keterangan Permintaan</th>
                                <th style="text-align: center;">Kuantiti Diserahkan</th>
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
                <h4 class="card-title">Input Barang Keluar</h4>
                <hr>
                <label for="">Search Batch Number</label>
                <div class="input-group" data-target-input="nearest">
                    <input type="text" id="input_search" style="width: 30%;" class="form-control" />
                    <button class="input-group-text" id="btn_search"><i data-feather="search"></i> Search</button>
                    <button style="float: right;" id="btn_reset" class="btn btn-danger btn-sm">Reset</button>
                </div>
                <div id="show_search">
                    <div class="table-responsive" style="margin-top: 10px;">
                        <form action="" id="form_proses" name="form_proses">
                            <table id="table_batch" style="width: 100%;" class="table">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">Batch Number</th>
                                        <th style="text-align: center;">Nama Barang</th>
                                        <th style="text-align: center;">Ketersediaan Barang</th>
                                        <th style="text-align: center;">Kuantiti</th>
                                    </tr>
                                </thead>
                                <tbody id="body_batch"></tbody>
                            </table>
                            <button class="btn btn-primary" type="submit" id="btn_proses"><i class="fa fa-save"></i> Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="barang_keluar"></div>

    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Rekomendasi Barang Keluar</h4>
                <p><small>Data barang keluar automatis diurutkan berdasarkan tanggal masuk barang yang paling lama, dan yang mendekati tanggal kadaluarsa <span style="color: red;">*</span></small></p>
                <hr>

                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_proses" style="width: 100%;" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th style="text-align: center;">Batch Number</th>
                                <th style="text-align: center;">Nama Barang</th>
                                <th style="text-align: center;">Ketersediaan Barang</th>
                            </tr>
                        </thead>
                        <tbody id="proses_row"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <a href="<?= base_url() ?>Inventory/Data_Permintaan_Barang"><button class="btn btn-secondary btn-sm"><i data-feather="arrow-left-circle"></i> Back</button></a>
</div>

<!-- core:js -->
<script src="<?= base_url() ?>assets/vendors/core/core.js"></script>
<!-- endinject -->


<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/datatables.net/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js"></script>
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<script src="<?= base_url() ?>assets/vendors/sweetalert2/sweetalert2.min.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>
<!-- End custom js for this page -->
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script src="<?= base_url() ?>assets/public/js/permintaan_barang_proses.js"></script>