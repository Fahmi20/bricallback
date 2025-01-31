<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Backend">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Buat Tagihan</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">DAFTAR TAGIHAN</h6>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambah">
                    <i data-feather="plus"></i> BUAT TAGIHAN
                </button>
                <div class="table-responsive" style="margin-top: 10px;">
                    <table id="table_va" style="width: 100%;" class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Partner</th>
                                <th>Customer No</th>
                                <th>Nomor Partner Reference</th>
                                <th>Nomor Virtual Account</th>
                                <th>Status Pembayaran</th>
                                <th>Nama</th>
                                <th>Total Amount</th>
                                <th>Expire Date</th>
                                <th>Trx ID</th>
                                <th>Deskripsi</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Virtual Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_create_va" action="<?php echo base_url('backend/create_virtual_account_manual'); ?>"
                method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="customerNo">Nomor Customer</label>
                        <input type="text" id="customerNo" name="customerNo" class="form-control" minlength="6"
                            maxlength="8" required title="Nomor Customer Max 8 Digit">
                    </div>
                    <div class="form-group">
                        <label for="virtualAccountName">Virtual Account Name</label>
                        <input type="text" id="virtualAccountName" name="virtualAccountName" class="form-control"
                            maxlength="40" required title="Virtual Account Name should not exceed 40 characters">
                    </div>
                    <div class="form-group">
                        <label for="totalAmount">Total Amount (Value)</label>
                        <input type="number" id="totalAmount" name="totalAmount" class="form-control" min="1" required
                            title="Please enter a valid total amount">
                    </div>
                    <div class="form-group" style="display: none;">
                        <label for="trxDateTime">Tanggal Transaksi</label>
                        <input type="hidden" id="trxDateTime" name="trxDateTime" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="trxId">Transaction ID</label>
                        <input type="text" id="trxId" name="trxId" class="form-control" maxlength="64" required
                            title="Transaction ID should not exceed 64 characters">
                    </div>
                    <div class="form-group">
                        <label for="additionalInfo">Additional Info (Optional)</label>
                        <textarea id="additionalInfo" name="additionalInfo" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Virtual Account</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Edit -->
<div class="modal fade" id="edit" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Update Virtual Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form_edit_va">
                    <div class="mb-3">
                        <label for="edit_customerNo" class="form-label">Customer Number</label>
                        <input type="text" class="form-control" id="edit_customerNo" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_virtualAccountName" class="form-label">Virtual Account Name</label>
                        <input type="text" class="form-control" id="edit_virtualAccountName">
                    </div>
                    <div class="mb-3">
                        <label for="edit_totalAmount" class="form-label">Total Amount</label>
                        <input type="number" class="form-control" id="edit_totalAmount">
                    </div>
                    <div class="mb-3">
                        <label for="edit_totalAmountCurrency" class="form-label">Currency</label>
                        <input type="text" class="form-control" id="edit_totalAmountCurrency" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_trxId" class="form-label">Transaction ID</label>
                        <input type="text" class="form-control" id="edit_trxId">
                    </div>
                    <div class="mb-3">
                        <label for="edit_additionalInfo" class="form-label">Additional Info</label>
                        <textarea class="form-control" id="edit_additionalInfo"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="save_edit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>



<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Virtual Account Update</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Customer Number: <span id="confirmCustomerNo"></span></p>
                <p>Virtual Account Name: <span id="confirmVirtualAccountName"></span></p>
                <p>Total Amount: <span id="confirmTotalAmount"></span></p>
                <p>Expired Date: <span id="confirmExpiredDate"></span></p>
                <p>Transaction ID: <span id="confirmTrxId"></span></p>
                <p>Additional Info: <span id="confirmAdditionalInfo"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirm_submit" class="btn btn-primary">Submit</button>
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
<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/jquery-validation/jquery.validate.min.js"></script>
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/data-table.js"></script>
<script src="<?= base_url() ?>assets/public/js/kategori.js"></script>
<!-- End custom js for this page -->