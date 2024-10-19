$(function() {
    var id_user = $('#id_user').val();
    var no = 2;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    select2barang()

    get_departement(id_user)

    $('thead').on('click', '#add_row', function() {
        var tr = `<tr id='loop_row'>
                    <td>
                        <div class="mb-3">
                            <button id="delete_row" class="btn btn-danger btn-sm" style="float: right;"><i class="fa fa-trash"></i></button>
                        </div>
                        <div class="mb-3">
                            <label for="barang" class="form-label">Barang</label>
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
        $('#row').append(tr).html();

        $('#row').on('click', '#delete_row', function() {
            $(this).parent().parent().remove();
        })

        $("#select2barang" + no).select2({
            allowClear: true,
            placeholder: "Select a Barang",
        })
        showbarang_loop(no)

        no++
    })

    $("#form_tambah").validate({
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
            var form_data = new FormData(document.getElementById("form_tambah"));
            $.ajax({
                    url: '../Backend/tambahpermintaan_barang',
                    type: 'POST',
                    dataType: 'JSON',
                    data: form_data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    success: function(x) {
                        if(x.status == 'Cek Stok Barang Anda!'){
                            Toast.fire({
                                icon: 'error',
                                title: x.message
                            })
                        } else {
                            Toast.fire({
                                icon: 'success',
                                title: 'Data Berhasil Di Simpan'
                            })
                            $("#form_tambah").trigger("reset")
                            get_departement(id_user)
                            $("#loop_row").empty().append()
                            $("#select2barang").val([]).trigger("change")
                        }
                    },
                })
        }
    })
})

function select2barang() {
    $("#select2barang").select2({
        allowClear: true,
        placeholder: "Select a Barang",
    });
    show_barang()
}

function show_barang() {
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

function showbarang_loop(no) {
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

function get_departement(value) {
    $.ajax({
        url: '../Backend/get_detail_profile',
        type: 'GET',
        dataType: 'JSON',
        data: {
            id: value
        },
        success: function(x) {
            console.log(x)
            $('#departement').val(x.data.nama_departement)
            $('#id_departement').val(x.data.id_departement)
        }
    })
}