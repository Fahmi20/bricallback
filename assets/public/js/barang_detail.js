$(function() {
    show_barang();
})

function show_barang() {
    var id = $('#id').val();
    $.ajax({
        url: "../Backend/get_detail_barang",
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