$(function() {
    if (performance.navigation.type == performance.navigation.TYPE_BACK_FORWARD) {
        $.ajax({
            url: "../Backend/DeleteBatchNumber",
            typeData: "JSON",
            success: function(value) {
                console.log(value)
            }
        })
    }
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    var table = $('#table_barang_masuk').DataTable({
        processing: true,
        ajax: {
            type: "GET",
            url: "../Backend/get_barang_masuk",
        },
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: 0,
        }, ],
        columns: [{
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.id_barang_masuk}</div>`;
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
                return `<div style="text-align:center;">${row.qty_masuk}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.satuan}</div>`;
            },
        }, {
            render: function(data, type, row) {
                var date = row.tgl_datang
                return `<div style="text-align:center;">${getFormattedDate(date)}</div>`;
            },
        }, {
            render: function(data, type, row) {
                var user = row.id_barang_masuk;
                return `<div style="text-align:center;"><a href="../Inventory/Detail_Barang_Masuk/${row.id_barang_masuk}"><button class="btn btn-secondary btn-sm"><i class="fa fa-search"></i></button></a>
                    <button class="btn btn-primary btn-sm" id="btn_edit" data-id="${user}"><i class="fa fa-pencil-square-o"></i></button>
                    <button class="btn btn-danger btn-sm" id="btn_hapus" data-id="${user}"><i class="fa fa-trash-o"></i></button></div>`
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

    $('#table_barang_masuk').on('click', '#btn_edit', function() {
        var id_barang_masuk = $(this).data("id");
        $.ajax({
            type: "GET",
            url: "../Backend/get_edit_barang_masuk",
            dataType: "JSON",
            data: {
                id_barang_masuk: id_barang_masuk
            },
            success: function(value) {
                $('#tgl_datang').val(getFormattedDate(value.data.tgl_datang))
                $('#id_barang_masuk_edit').val(value.data.id_barang_masuk)
                select2supplier(value.data.id_supplier)
                $('#batch_number_edit').val(value.data.batch_number)
                select2barang(value.data.id_barang)
                select2satuan(value.data.satuan)
                $('#qty_old').val(value.data.qty_masuk)
                $('#qty_edit').val(value.data.qty_masuk)
                exp(value.data.exp)
                $('#edit').modal('show')
            }
        })
    })

    $('#table_barang_masuk').on('click', '#btn_hapus', function() {
        var id_barang_masuk = $(this).data("id");
        $.ajax({
            type: "GET",
            url: "../Backend/get_edit_barang_masuk",
            dataType: "JSON",
            data: {
                id_barang_masuk: id_barang_masuk
            },
            success: function(value) {
                $('#id_barang_masuk_hapus').val(value.data.id_barang_masuk)
                $('#hapus').modal('show')
            }
        })
    })

    $('#save_edit').click(function() {
        $("#form_edit").validate({
            rules: {
                tgl_datang: {
                    required: true,
                },
                id_supplier: {
                    required: true,
                },
                id_barang: {
                    required: true,
                },
                satuan: {
                    required: true,
                },
                qty: {
                    required: true,
                },
                expired_date: {
                    required: true,
                }
            },
            messages: {
                tgl_datang: "Please enter a Tanggal Datang",
                id_supplier: "Please select a Nama Supplier",
                id_barang: "Please select a Nama Barang",
                satuan: "Please select a Satuan",
                qty: "Please enter a QTY",
                expired_date: "Please enter a Tanggal Expired",
            },
            errorPlacement: function(error, element) {
                error.addClass( "invalid-feedback" );
        
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                }
                else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function () {
                $.ajax({
                    url: '../Backend/edit_barang_masuk',
                    type: 'GET',
                    typeData: 'JSON',
                    data: $('#form_edit').serialize(),
                    beforeSend: function() {
                        $("#save_edit").prop("disabled", true).html('...Loading');
                    },
                    success: function(value) {
                        $('#edit').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: 'Data Barang Masuk Berhasil Di Update'
                        })
                        table.ajax.reload();
                    },
                    complete: function() {
                        $("#save_edit").prop("disabled", false).html("Save");
                        $("#select2supplier").val([]).trigger("change");
                        $("#select2barang").val([]).trigger("change");
                        $("#select2satuan").val([]).trigger("change");
                        $("#form_edit").trigger("reset");
                    }
                })
            }
        })
    })

    $('#save_delete').click(function() {
        $.ajax({
            url: '../Backend/hapus_barang_masuk',
            type: 'GET',
            typeData: 'JSON',
            data: $('#form_delete').serialize(),
            beforeSend: function() {
                $("#save_delete").prop("disabled", true).html('...Loading');
            },
            success: function(value) {
                $('#hapus').modal('hide');
                Toast.fire({
                    icon: 'success',
                    title: 'Data Barang Masuk Berhasil Di Hapus'
                })
                table.ajax.reload();
            },
            complete: function() {
                $("#save_delete").prop("disabled", false).html("Save");
                $("#form_delete").trigger("reset");
            }
        })
    })
})

function select2supplier(value) {
    $("#select2supplier").select2({
        allowClear: true,
        placeholder: "Select a Supplier",
        dropdownParent: $('#edit .modal-content')
    });
    show_supplier(value)
}

function show_supplier(value) {
    $.ajax({
        url: "../Backend/get_supplier",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#select2supplier').html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    var option = '<option value="' + v.id_supplier + '">' + v.nama_supplier + ' </option>'
                    $('#select2supplier').append(option).val(value).trigger("change");
                });
            }
        }
    });
}

function select2barang(value) {
    $("#select2barang").select2({
        allowClear: true,
        placeholder: "Select a Supplier",
        dropdownParent: $('#edit .modal-content')
    });
    show_barang(value)
}

function show_barang(value) {
    $.ajax({
        url: "../Backend/get_barang",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#select2barang').html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    var option = '<option value="' + v.id_barang + '">' + v.nama_barang + ' </option>'
                    $('#select2barang').append(option).val(value).trigger("change");
                });
            }
        }
    });
}

function select2satuan(value) {
    $("#select2satuan").select2({
        allowClear: true,
        placeholder: "Select a Satuan",
        dropdownParent: $('#edit .modal-content')
    });

    var option = `
                    <option value="Pcs">Pcs</option>
                    <option value="Box">Box</option>
                    <option value="Kg">Kg</option>
                    <option value="Ball">Ball</option>
                    <option value="Karung">Karung</option>
                    <option value="Liter">Liter</option>
                    <option value="Kaleng">Kaleng</option>
                    <option value="Palet">Palet</option>
                    <option value="Ikat">Ikat</option>`;
    $("#select2satuan").html('<option value=""></option>')
    $('#select2satuan').append(option).val(value).trigger("change")
}

function exp(value) {
    if ($('#exp_date').length) {
        var date = new Date(value);
        var tgl = new Date(date.getFullYear(), date.getMonth(), date.getDate());

        $('#exp_date').datepicker({
            format: "dd-mm-yyyy",
            todayHighlight: true,
            autoclose: true
        });
        $('#exp_date').datepicker('setDate', tgl);
    }
}

function getFormattedDate(date) {
    var date = new Date(date);
    let year = date.getFullYear();
    let month = (1 + date.getMonth()).toString().padStart(2, '0');
    let day = date.getDate().toString().padStart(2, '0');

    return day + '-' + month + '-' + year;
}