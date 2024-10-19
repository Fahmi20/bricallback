$(function () { 
    var id = $('#id').val()
    show_detail(id)
})

function show_detail(id) { 
    $.ajax({
        url: '../../Backend/get_detail_po',
        type: 'GET',
        dataType: 'JSON',
        data: {
            id: id,
        },

        success: function(x) {
            console.log(x)
            var table_head = `
            <table>
                <tbody>
                    <tr>
                        <td>Nama</td>
                        <td>: ${x.data.nama_user}</td>
                    </tr>
                    <tr>
                        <td>Tanggal PO</td>
                        <td>: ${getFormattedDate(x.data.tgl_po)}</td>
                    </tr>
                </tbody>
            </table>
            `

            $('#table_head').html(table_head)
            var no = 1
            $.each(x.detail_po, function (k, v) { 
                var detail_po = `
                <tr>
                    <td style="text-align: center;">${no}</td>
                    <td style="text-align: center;">${v.nama_barang}</td>
                    <td style="text-align: center;">${v.qty_po}</td>
                    <td style="text-align: center;">${v.ket_po}</td>
                </tr>
                `

                $('#detail_barang').append(detail_po).html()

                no++
            })

            var total = `
            <tr>
                <th colspan="2" style="text-align: center;">Total</th>
                <th style="text-align: center;">${x.total.Total}</th>
                <th></th>
            </tr>
            `

            $('#total').append(total).html()

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