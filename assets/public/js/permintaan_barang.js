$(function() {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    var table = $('#table_permintaan').DataTable({
        processing: true,
        ajax: {
            type: "GET",
            url: "../Backend/get_permintaan",
        },
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: 0,
        }, ],
        columns: [{
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.id_permintaan}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${getFormattedDate(row.tgl_permintaan)}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.nama_departement}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;"><span class="badge bg-info">${row.status_permintaan}</span></div>`;
            },
        }, {
            render: function(data, type, row) {
                var permintaan = row.id_permintaan;
                if(row.status_permintaan == 'Selesai') {
                    return `<div style="text-align:center;"><a href="../Inventory/Detail_Permintaan_Barang/${row.id_permintaan}"><button class="btn btn-secondary btn-sm"><i class="fa fa-search"></i></button></a></div>`
                } else {
                    return `<div style="text-align:center;">
                    <a href="../Inventory/Proses_Permintaan_Barang/${row.id_permintaan}"><button class="btn btn-success btn-sm"><i class="fa fa-check-circle"></i> Proses</button></a>
                    <button class="btn btn-danger btn-sm" id="btn_hapus" data-id="${permintaan}"><i class="fa fa-trash-o"></i></button></div>`
                }
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

    $('#table_permintaan').on('click', '#btn_hapus', function() {
        var id_permintaan = $(this).data("id");
        $.ajax({
            type: "GET",
            url: "../Backend/get_detail_permintaan",
            dataType: "JSON",
            data: {
                id: id_permintaan
            },
            success: function(value) {
                $("#id_permintaan").val(value.data.id_permintaan);
                $('#hapus').modal('show');
            }
        })
    })

    $('#save_delete').click(function() {
        $.ajax({
            url: "../Backend/deletePermintaanBarang",
            type: "GET",
            dataType: "JSON",
            data: $('#form_delete').serialize(),
            cache: false,
            processData: false,
            beforeSend: function() {
                $("#save_delete").prop("disabled", true).html('...Loading');
            },
            success: function() {
                $('#save_delete').removeAttr('disabled');
                $('#hapus').modal('hide');
                table.ajax.reload();
                Toast.fire({
                    icon: 'success',
                    title: 'Data Permintaan Barang Berhasil Di Hapus'
                })
            },
            complete: function() {
                $("#save_delete").prop("disabled", false).html("Yes, Delete It!");
                $("#form_delete").trigger("reset");
            }
        })
    })
})

function getFormattedDate(date) {
    var date = new Date(date);
    let year = date.getFullYear();
    let month = (1 + date.getMonth()).toString().padStart(2, '0');
    let day = date.getDate().toString().padStart(2, '0');

    return day + '-' + month + '-' + year;
}