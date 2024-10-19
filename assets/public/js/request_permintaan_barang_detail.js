$(function() {
    var id_permintaan = $('#id').val();
    show_permintaan(id_permintaan)
})

function show_permintaan(value) {
    $.ajax({
        url: '../../Backend/get_detail_permintaan',
        type: 'GET',
        dataType: 'JSON',
        data: {
            id: value
        },
        success: function(x) {
            var permintaan = `
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td>Departement</td>
                        <td>: ${x.data.nama_departement}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Permintaan</td>
                        <td>: ${getFormattedDate(x.data.tgl_permintaan)}</td>
                    </tr>
                </tbody>
            </table>
            `;
            $('#permintaan_barang').html(permintaan);
            var table = $('#table_barang').DataTable({
                processing: true,
                data: x.barang,
                columnDefs: [{
                    searchable: false,
                    orderable: false,
                    targets: 0,
                }, ],
                order: [
                    [1, 'asc']
                ],
                columns: [{
                    render: function(data, type, row) {
                        return `<div style="text-align:center;">${row.id_detail_permintaan}</div>`;
                    },
                }, {
                    render: function(data, type, row) {
                        return `<div style="text-align:center;">${row.nama_barang}</div>`;
                    },
                }, {
                    render: function(data, type, row) {
                        return `<div style="text-align:center;">${row.qty_permintaan}</div>`;
                    },
                }, {
                    render: function(data, type, row) {
                        return `<div style="text-align:center;">${row.ket_permintaan}</div>`;
                    },
                }],
            })

            table.on('order.dt search.dt', function() {
                table.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                    table.cell(cell).invalidate('dom');
                });
            }).draw();
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