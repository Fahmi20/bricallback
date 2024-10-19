<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Produksi">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Produksi/Request_Permintaan_Barang">Request Permintaan Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Permintaan Barang</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Edit Permintaan Barang</h4>
                <hr>
                <input type="hidden" id="id" value="<?= $id ?>">
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Departement</label>
                    <input type="hidden" name="id_departement" id="id_departement">
                    <input type="text" class="form-control" readonly id="departement">
                </div>
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal Permintaan</label>
                    <input type="text" class="form-control" name="tgl_permintaan" id="tgl_permintaan" readonly>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <form action="" id="form_edit">
                <div class="card-body">
                    <table style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Data Barang <button id="add_row" type="button" class="btn btn-primary btn-sm" style="float: right;"><i class="fa fa-plus-square-o"></i></button></th>
                            </tr>
                        </thead>
                        <tbody id="row">

                        </tbody>

                        <tbody id="tambah_row">
                        </tbody>

                    </table>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-12 grid-margin">
        <button id="simpan" type="button" class="btn btn-primary btn-sm" style="width: 10%;"><i class="fa fa-save"></i> Update</button>
        <a href="<?= base_url() ?>Produksi/Permintaan_Barang"><button type="button" class="btn btn-secondary btn-sm" style="width: 10%;"><i data-feather="arrow-left-circle"></i> Back</button></a>
    </div>
</div>

<div class="modal fade" id="edit" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Data Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <form action="" id="form_edit_modal">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="">Barang</label>
                        <input type="hidden" name="id" id="id_detail_permintaan_edit">
                        <select class="form-select" name="id_barang" id="select2barang_edit" data-width="100%" required></select>
                    </div>
                    <div class="mb-3">
                        <label for="">Kuantiti</label>
                        <input type="number" min='0' name="qty_permintaan" id="qty_permintaan_edit" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="">Keterangan Permintaan</label>
                        <textarea name="ket_permintaan" id="ket_permintaan_edit" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="save_edit" class="btn btn-primary">Save changes</button>
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
            <form action="" id="form_delete_modal">
                <div class="modal-body">
                    <h5><i data-feather="trash-2" style="color:red"></i> Anda Yakin Untuk Menghapus Data?</h5>
                    <input type="hidden" name="id" id="id_detail_permintaan_delete">
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
<script src="<?= base_url() ?>assets/public/js/request_permintaan_barang_edit.js"></script>