$(function() {
    show_barang();
})

function show_barang() {
    var id = $('#id').val();
    $.ajax({
        url: "../../Backend/get_detail_barang_masuk",
        type: "GET",
        dataType: "JSON",
        data: {
            id_barang_masuk: id
        },
        beforeSend: function() {
            Swal.fire({
                text: 'Loading...',
                imageUrl: '../../assets/loading_spinner.gif',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            })
        },
        success: function(value) {
            console.log(value)
            var data = value.data;
            var barang = `
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td>Tanggal Kedatangan</td>
                        <td>: ${getFormattedDate(data.tgl_datang)}</td>
                    </tr>
                    <tr>
                        <td>Batch Number</td>
                        <td>: ${data.batch_number}</td>
                    </tr>
                    <tr>
                        <td>Satuan</td>
                        <td>: ${data.satuan}</td>
                    </tr>
                    <tr>
                        <td>Kuantiti</td>
                        <td>: ${data.qty_masuk}</td>
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
        },
        complete: function() {
            Swal.close()
        }
    })
}

function getFormattedDate(date) {
    var date = new Date(date);
    let year = date.getFullYear();
    let month = (1 + date.getMonth()).toString().padStart(2, '0');
    let day = date.getDate().toString().padStart(2, '0');

    return day + '-' + month + '-' + year;
}