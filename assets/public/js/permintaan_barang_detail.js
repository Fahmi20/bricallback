$(function() {
    var id_permintaan = $('#id').val();
    show_permintaan(id_permintaan)
})

function show_permintaan(value) {
    $.ajax({
        url: '../../Backend/get_proses_permintaan',
        type: 'GET',
        dataType: 'JSON',
        data: {
            id: value
        },
        success: function(x) {
            console.log(x)
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
                    <tr>
                        <td>Status</td>
                        <td>: ${x.data.status_permintaan}</td>
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
                },{
                    render: function(data, type, row) {
                        return `<div style="text-align:center;">${row.qty_keluar_permintaan}</div>`;
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

            if(x.barang_keluar != null) {
                var data_barang_keluar = `
                    <div class="col-lg-12 grid-margin">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Data Barang Keluar</h4>
                                <hr>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th> Tanggal Keluar </th>
                                            <th> Batch Number </th>
                                            <th> Nama Barang </th>
                                            <th> Kuantiti </th>
                                        </tr>
                                    </thead>
                                    <tbody id="loop_barang_keluar">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `

                $('#barang_keluar').html(data_barang_keluar)

                $.each(x.barang_keluar, function (k, v) { 
                    var table_barang_keluar = `
                        <tr>
                            <td>${getFormattedDate(v.tgl_keluar)}</td>
                            <td>${v.batch_number}</td>
                            <td>${v.nama_barang}</td>
                            <td>${v.qty_diserahkan}</td>
                        </tr>
                        `
                        $('#loop_barang_keluar').append(table_barang_keluar).html();
                 })
            }
            
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