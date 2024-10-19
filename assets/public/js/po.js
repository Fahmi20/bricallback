$(function () { 

    var table = $('#table_po').DataTable({
        processing: true,
        ajax: {
            type: "GET",
            url: "../Backend/get_po",
        },
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: 0,
        }, ],
        columns: [{
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.id_po}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.nama_user}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${getFormattedDate(row.tgl_po)}</div>`;
            },
        }, {
            render: function(data, type, row) {
                var po = row.id_po;
                return `<div style="text-align:center;">
                    <a href="../Inventory/Detail_PO/${po}"><button class="btn btn-secondary btn-sm"><i class="fa fa-search"></i> Detail</button></a>
                    <a href="../Inventory/Print_PO/${po}" target="_blank"><button class="btn btn-primary btn-sm"><i class="fa fa-print"></i></button></a>
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