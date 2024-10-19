$(function () { 
    var no = 1;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    var table = $('#table_stok').DataTable({
        processing: true,
        ajax: {
            type: "GET",
            url: "../Backend/get_stok",
        },
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
                return `<div style="text-align:center;">${row.id_po}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.nama_barang}</div>`;
            },
        }, {
            render: function(data, type, row) {
                if(row.qty_stok < 10) {
                    return `<div style="text-align:center;background-color:red;">${row.qty_stok}</div>`;
                } else {
                    return `<div style="text-align:center;">${row.qty_stok}</div>`;
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

    select2barang()

    $('thead').on('click', '#add_row', function () { 
        var row = `
        <tr>
            <td>
                <select name="id_barang[]" id="select2barang${no}" class="form-control" style="width: 100%;"></select>
            </td>
            <td>
                <input type="number" name="qty_po[]" min="0" class="form-control">
            </td>
            <td>
                <textarea name="ket_po[]" cols="30" rows="10" class="form-control"></textarea>
            </td>
            <td style="text-align: center;">
                <button class="btn btn-danger" id="delete_row"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
        `
        $('#table_tambah_loop').append(row).html()

        $('#table_tambah_loop').on('click', '#delete_row', function() {
            $(this).parent().parent().remove();
        })

        $("#select2barang" + no).select2({
            allowClear: true,
            placeholder: "Select a Barang",
        })
        showbarang_loops(no)
        no++
    })

    $("#form_tambah").validate({
        onfocusout: false,    
        rules: {
            'id_barang[]': {
                required: true,
            },
            'qty_po[]': {
                required: true,
            },
            'ket_po[]': {
                required: true,
            }
        },
        messages: {
            'id_barang[]': "This field is required",
            'qty_po[]': "This field is required",
            'ket_po[]': "This field is required",
        },
        errorPlacement: function(error, element) {
            error.addClass( "invalid-feedback" );

            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            }
            else if (element.prop('type') === 'radio' && element.parent('.radio-inline').length) {
                error.insertAfter(element.parent().parent());
            }
            else if (element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                error.appendTo(element.parent().parent());
            }
            else {
                error.insertAfter(element);
            }
        },
        highlight: function(element, errorClass) {
        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') != 'radio') {
            $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
        }
        },
        unhighlight: function(element, errorClass) {
        if ($(element).prop('type') != 'checkbox' && $(element).prop('type') != 'radio') {
            $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
        }
        },
        submitHandler: function () {
            var form_data = new FormData(document.getElementById("form_tambah"));
            $.ajax({
                url: '../Backend/simpanPO',
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success: function (x) { 
                    console.log(x)
                    if(x.status == true)
                    {
                        Toast.fire({
                            icon: 'success',
                            title: x.message,
                        })
                        $("#form_tambah").trigger("reset")
                        $('#select2barang').val([]).trigger("change");
                        $("#table_tambah_loop").empty().append()
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: x.message,
                        })
                        $("#form_tambah").trigger("reset")
                        $('#select2barang').val([]).trigger("change");
                        $("#table_tambah_loop").empty().append()
                    }
                }
            })
        }
    })
})

function showbarang_loops(no) {

    $.ajax({
        url: "../Backend/get_barang",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#select2barang' + no).html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    $('#select2barang' + no).append(`<option value="${v.id_barang}">${v.nama_barang}</option>`).html()
                });
            }
        }
    });
}

function select2barang() { 
    $("#select2barang").select2({
        allowClear: true,
        placeholder: "Select a Barang",
    })
    showbarang()
}

function showbarang() { 
    $.ajax({
        url: "../Backend/get_barang",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#select2barang').html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    $('#select2barang').append(`<option value="${v.id_barang}">${v.nama_barang}</option>`).html()
                });
            }
        }
    });
}