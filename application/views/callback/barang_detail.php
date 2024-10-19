<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url() ?>Inventory/Barang">Barang</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Barang</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Detail Barang</h4>
                <input type="hidden" id="id" value="<?= $id ?>">
                <hr>
                <div id="detail_barang"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Detail Supplier</h4>
                <hr>
                <div id="detail_supplier"></div>
            </div>
        </div>
    </div>

    <a href="<?= base_url() ?>Inventory/Barang"><button class="btn btn-secondary"><i data-feather="arrow-left-circle"></i> Back</button></a>
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
<!-- End plugin js for this page -->

<!-- inject:js -->
<script src="<?= base_url() ?>assets/vendors/feather-icons/feather.min.js"></script>
<script src="<?= base_url() ?>assets/js/template.js"></script>
<!-- endinject -->

<!-- Custom js for this page -->
<script src="<?= base_url() ?>assets/js/dashboard-light.js"></script>
<script src="<?= base_url() ?>assets/js/datepicker.js"></script>
<script src="<?= base_url() ?>assets/public/js/barang_detail.js"></script>
<!-- End custom js for this page -->

<script>
    $(function() {
        show_barang();
    })

    function show_barang() {
        var id = <?= $id ?>;
        $.ajax({
            url: "<?= base_url() ?>Backend/get_detail_barang",
            type: "GET",
            dataType: "JSON",
            data: {
                id: id
            },
            success: function(value) {
                var data = value.data;
                var barang = `
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td>Nama Barang</td>
                            <td>: ${data.nama_barang}</td>
                        </tr>
                        <tr>
                            <td>Kategori Barang</td>
                            <td>: ${data.nama_kategori}</td>
                        </tr>
                        <tr>
                            <td>Satuan</td>
                            <td>: ${data.satuan}</td>
                        </tr>
                        <tr>
                            <td>Berat Barang</td>
                            <td>: ${data.berat_barang}</td>
                        </tr>
                        <tr>
                            <td>Keterangan</td>
                            <td>: ${data.keterangan}</td>
                        </tr>
                    </tbody>
                </table>
                `;

                var supplier = `
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td>Nama Supplier</td>
                            <td>: ${data.nama_supplier}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: ${data.alamat}</td>
                        </tr>
                        <tr>
                            <td>No Telp</td>
                            <td>: ${data.no_telp_supplier}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>: ${data.email_supplier}</td>
                        </tr>
                        <tr>
                            <td>Nama PIC</td>
                            <td>: ${data.nama_pic_supplier}</td>
                        </tr>
                        <tr>
                            <td>No Telp PIC</td>
                            <td>: ${data.no_telp_pic_supplier}</td>
                        </tr>
                    </tbody>
                </table>
                `

                $('#detail_barang').html(barang);
                $('#detail_supplier').html(supplier);
            }
        })
    }
</script>