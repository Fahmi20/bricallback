$(function() {
    var id = $('#id').val();
    var no = 100;
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    get_permintaan(id)

    select2barang()

    $('thead').on('click', '#add_row', function() {

        var tr = `<tr>
                    <td>
                        <div class="mb-3">
                            <button id="delete_row" class="btn btn-danger btn-sm" style="float: right;"><i class="fa fa-trash"></i></button>
                        </div>
                        <div class="mb-3">
                            <label for="barang" class="form-label">Barang</label>
                            <input type="hidden" name="id" id="id_detail_permintaan${no}">
                            <select class="form-select" name="id_barang[]" id="select2barang${no}" data-width="100%" required></select>
                        </div>
                        <div class="mb-3">
                            <label for="qty" class="form-label">Qty</label>
                            <input type="number" class="form-control" name="qty_permintaan[]" min='0' required>
                        </div>
                        <div class="mb-3">
                            <label for="qty" class="form-label">Keterangan Permintaan</label>
                            <textarea name="ket_permintaan[]" class="form-control"></textarea>
                        </div>
                        <hr>
                    </td>
                </tr>`
        $.ajax({
            url: '../../Backend/get_detail_permintaan',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: id
            },
            success: function(x) {
                var a = no - 1;
                $('#id_detail_permintaan' + a).val(x.data.id_detail_permintaan)
            }
        })

        $('#tambah_row').append(tr).html();

        $('#tambah_row').on('click', '#delete_row', function() {
            $(this).parent().parent().remove();
        })

        $("#select2barang" + no).select2({
            allowClear: true,
            placeholder: "Select a Barang",
        })
        showbarang_loops(no)
        no++
    })

    $("#form_edit").validate({
        onfocusout: false,
        rules: {
            'id_barang[]': {
                required: true,
            },
            'qty_permintaan[]': {
                required: true,
            }
        },
        messages: {
            'id_barang[]': "Please select a Barang",
            'qty_permintaan[]': "Please enter a Qty Permintaan",
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
            var form_data = new FormData(document.getElementById("form_edit"));
            $.ajax({
                url: '../../Backend/tambahDetail_Permintaan',
                type: 'POST',
                dataType: 'JSON',
                data: form_data,
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success: function(x) {
                    if (x.status == true) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Data Detail Permintaan Barang Berhasil Di Simpan'
                        })
                        $("#form_edit").trigger("reset")
                        $("#row").empty().append()
                        $("#tambah_row").empty().append()
                        get_permintaan(id)
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Data Detail Permintaan Barang Gagal Di Simpan'
                        })
                    }

                },
            })
        }
    })

    $('#save_edit').click(function() {
        $("#form_edit_modal").validate({
            rules: {
                id_barang: {
                    required: true,
                },
                qty_permintaan: {
                    required: true,
                }
            },
            messages: {
                id_barang: "Please select a Barang",
                qty_permintaan: "Please enter a Qty Permintaan",
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
                    url: '../../Backend/editBarang_Permintaan',
                    type: 'GET',
                    dataType: 'JSON',
                    data: $('#form_edit_modal').serialize(),
                    success: function(x) {
                        if (x.status == true) {
                            $('#edit').modal('hide')
                            Toast.fire({
                                icon: 'success',
                                title: 'Data Permintaan Barang Berhasil Di Update'
                            })
                            $("#row").empty().append()
                            get_permintaan(id)
                        } else if(x.status == 'Cek Stok Barang Anda!'){
                            Toast.fire({
                                icon: 'error',
                                title: x.message,
                            })
                            $("#row").empty().append()
                            get_permintaan(id)
                        } else {
                            $('#edit').modal('hide')
                            Toast.fire({
                                icon: 'error',
                                title: 'Data Permintaan Barang Gagal Di Update'
                            })
                            $("#row").empty().append()
                            get_permintaan(id)
                        }
                    }
                })
            }
        })
    })

    $('#save_delete').click(function() {
        $.ajax({
            url: '../../Backend/deleteBarang_Permintaan',
            type: 'GET',
            dataType: 'JSON',
            data: $('#form_delete_modal').serialize(),
            success: function(x) {
                if (x.status == true) {
                    $('#hapus').modal('hide')
                    Toast.fire({
                        icon: 'success',
                        title: 'Data Permintaan Barang Berhasil Di Hapus'
                    })
                    $("#row").empty().append()
                    get_permintaan(id)
                } else {
                    $('#hapus').modal('hide')
                    Toast.fire({
                        icon: 'error',
                        title: 'Data Permintaan Barang Gagal Di Hapus'
                    })
                    $("#row").empty().append()
                    get_permintaan(id)
                }
            }
        })
    })
})

function get_permintaan(id) {
    $.ajax({
        url: '../../Backend/get_detail_permintaan',
        type: 'GET',
        dataType: 'JSON',
        data: {
            id: id
        },
        success: function(x) {
            console.log(x)
            var no = 1
            $('#id_departement').val(x.data.id_departement)
            $('#departement').val(x.data.nama_departement)
            $('#tgl_permintaan').val(getFormattedDate(x.data.tgl_permintaan))
            $.each(x.barang, function(k, v) {
                var table = `
                        <tr>
                            <td>
                                <div class="mb-3">
                                    <label for="barang" class="form-label">Barang</label>
                                    <select class="form-select" id="select2barang${no}" data-width="100%" required></select>
                                </div>
                                <div class="mb-3">
                                    <label for="qty" class="form-label">Qty</label>
                                    <input type="number" class="form-control" value="${v.qty_permintaan}" readonly min='0' required>
                                </div>
                                <div class="mb-3">
                                    <label for="qty" class="form-label">Keterangan Permintaan</label>
                                    <textarea class="form-control" readonly>${v.ket_permintaan}</textarea>
                                </div>
                                <div class="mb-3">
                                    <button id="btn_edit${no}" type="button" class="btn btn-warning btn-sm" data-id="${v.id_detail_permintaan}"><i class="fa fa-pencil-square-o"></i> Edit</button>
                                    <button id="btn_delete${no}" type="button" class="btn btn-danger btn-sm" data-id="${v.id_detail_permintaan}"><i class="fa fa-trash-o"></i> Hapus</button>
                                </div>
                                <hr>
                            </td>
                        </tr>
                `
                $('#row').append(table).html();

                $("#select2barang" + no).select2({
                    allowClear: true,
                    placeholder: "Select a Barang",
                    disabled: true
                })
                showbarang_loop(no, v.id_barang)

                $('#btn_edit' + no).click(function() {
                    var id_detail_permintaan = $(this).data("id")

                    $.ajax({
                        url: '../../Backend/get_permintaan_barang',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            id: id_detail_permintaan
                        },
                        success: function(x) {
                            $('#id_detail_permintaan_edit').val(x.data.id_detail_permintaan)
                            $('#qty_permintaan_edit').val(x.data.qty_permintaan)
                            $('#ket_permintaan_edit').val(x.data.ket_permintaan)
                            select2barang_edit(x.data.id_barang)
                            $('#edit').modal('show');
                        }
                    })
                })

                $('#btn_delete' + no).click(function() {
                    var id_detail_permintaan = $(this).data("id")

                    $.ajax({
                        url: '../../Backend/get_permintaan_barang',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            id: id_detail_permintaan
                        },
                        success: function(x) {
                            console.log(x)
                            $('#id_detail_permintaan_delete').val(x.data.id_detail_permintaan)
                            $('#hapus').modal('show');
                        }
                    })
                })
                no++
            })
        }
    })
}

function select2barang() {
    $("#select2barang").select2({
        allowClear: true,
        placeholder: "Select a Barang",
    });
    show_barang()
}

function show_barang() {
    $.ajax({
        url: "../../Backend/get_barang",
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

function showbarang_loops(no) {

    $.ajax({
        url: "../../Backend/get_barang",
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

function showbarang_loop(no, value) {

    $.ajax({
        url: "../../Backend/get_barang",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#select2barang' + no).html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    $('#select2barang' + no).append(`<option value="${v.id_barang}">${v.nama_barang}</option>`).val(value).trigger("change")
                });
            }
        }
    });
}

function select2barang_edit(value) {
    $("#select2barang_edit").select2({
        allowClear: true,
        placeholder: "Select a Barang",
        dropdownParent: $('#edit .modal-content')
    });
    show_barang_edit(value)
}

function show_barang_edit(value) {
    $.ajax({
        url: "../../Backend/get_barang",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#select2barang_edit').html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    $('#select2barang_edit').append(`<option value="${v.id_barang}">${v.nama_barang}</option>`).val(value).trigger('change')
                });
            }
        }
    });
}

function getFormattedDate(date) {
    var date = new Date(date);
    let year = date.getFullYear();
    let month = (1 + date.getMonth()).toString().padStart(2, '0');
    let day = date.getDate().toString().padStart(2, '0');

    return day + '-' + month + '-' + year;
}