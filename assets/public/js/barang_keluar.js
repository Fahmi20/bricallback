$(function () { 

    var table = $('#barang_keluar').DataTable({
        processing: true,
        ajax: {
            type: "GET",
            url: "../Backend/get_barang_keluar",
        },
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: 0,
        }, ],
        columns: [{
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.id_barang_keluar}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${getFormattedDate(row.tgl_keluar)}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.batch_number}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.nama_barang}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.nama_departement}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.qty_diserahkan}</div>`;
            },
        }, {
            render: function(data, type, row) {
                var barang = row.id_barang_keluar;
                return `<div style="text-align:center;">
                    <a href="../Inventory/Detail_Barang_Keluar/${barang}"><button class="btn btn-secondary btn-sm"><i class="fa fa-search"></i> Detail</button></a>
                    </div>`
            },
        }],

    });

    table.on('order.dt search.dt', function() {
        table.column(0, {
            search: 'applied',
            order: 'applied'
        }).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1;
            table.cell(cell).invalidate('dom');
        });
    }).draw();

})

function getFormattedDate(date) {
    var date = new Date(date);
    let year = date.getFullYear();
    let month = (1 + date.getMonth()).toString().padStart(2, '0');
    let day = date.getDate().toString().padStart(2, '0');

    return day + '-' + month + '-' + year;
}