$(function() {
    var htmlPrint = document.getElementById("doc_print");
    htmlPrint.style.display = "none";


    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    select2supplier();
    select2satuan();
    select2barang1();

    // KONDISI RELOAD DATA DELETE

    $('#back').click(function() {
        refresh_batch()
        window.location.replace('../Inventory/Data_Barang_Masuk')

    })

    if (performance.navigation.type == performance.navigation.TYPE_RELOAD) {
        refresh_batch()
    }

    if ($('#exp_date').length) {
        var date = new Date();
        $('#exp_date').datepicker({
            format: "dd-mm-yyyy",
            todayHighlight: true,
            autoclose: true
        });
        $('#exp_date').datepicker('setDate');
    }

    var no = 2;
    $('thead').on('click', '#tambah_barang', function() {
        var tr = `<tr>
                    <td>
                        <input type="text" class="form-control" name="batch_number[]" id="batch_number${no}" readonly>
                        <input type="hidden" id="id_batch${no}" name="id_batch">
                        <input type="hidden" id="id_barang${no}" name="id_barang[]">
                    </td>
                    <td>
                        <select class="form-select" id="select2barang${no}" data-width="100%" name="selectbarang_val[]"  required></select>
                    </td>
                    <td>
                        <select class="form-select" id="select2satuan${no}" name="satuan[]" data-width="100%" required>
                            <option value=""></option>
                            <option value="Pcs">Pcs</option>
                            <option value="Box">Box</option>
                            <option value="Kg">Kg</option>
                            <option value="Ball">Ball</option>
                            <option value="Karung">Karung</option>
                            <option value="Liter">Liter</option>
                            <option value="Kaleng">Kaleng</option>
                            <option value="Palet">Palet</option>
                            <option value="Ikat">Ikat</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" class="form-control" name="qty[]" min="0" id="qty" required>
                    </td>
                    <td>
                        <div class="input-group date datepicker" id="exp_date${no}">
                            <input type="text" class="form-control" name="expired_date[]" autocomplete="off" required>
                            <span class="input-group-text input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <button type="button" id="hapus_barang${no}" data-id="${no}" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
                    </td>
                </tr>`
        var print = `
                <tr id="row${no}">
                    <td>
                        <h2 id="batch_print${no}"></h2>
                        <p id="tgl_print${no}"></p>
                        <hr>
                    </td>
                </tr>
                `
        $('#body_barang').append(tr).html();
        $('#doc_print').append(print).html();

        $("#select2satuan" + no).select2({
            allowClear: true,
            placeholder: "Select a Satuan",
        });

        $("#select2barang" + no).select2({
            allowClear: true,
            placeholder: "Select a Barang",
        });
        show_barang_loop(no)

        if ($('#exp_date' + no).length) {
            var date = new Date();
            $('#exp_date' + no).datepicker({
                format: "dd-mm-yyyy",
                todayHighlight: true,
                autoclose: true
            });
            $('#exp_date' + no).datepicker('setDate');
        }

        $('#select2barang' + no).change(function() {
            var id_barang = $(this).val()
            var no_batch = no - 1;
            var tgl_datang = $('#tanggal_kedatangan').val()
            $.ajax({
                url: "../Backend/SearchBatchNumber",
                type: "GET",
                typeData: "JSON",
                data: {
                    id_barang: id_barang,
                },
                success: function(value) {
                    let x = JSON.parse(value);

                    if (x.data != null) {
                        var split = x.data.batch_number.split("_")
                        var new_batch_number = parseInt(split[1]) + 1

                        var length_batch = new_batch_number.toString().length

                        if (length_batch == 1) {
                            var hasil = split[0] + '_00' + new_batch_number;
                        } else if (length_batch == 2) {
                            var hasil = split[0] + '_0' + new_batch_number;
                        } else {
                            var hasil = split[0] + '_' + new_batch_number;
                        }
                        $.ajax({
                            url: '../Backend/SessionBatchNumber',
                            type: 'GET',
                            typeData: 'JSON',
                            data: {
                                id_barang: x.data.id_barang,
                                batch_number: hasil
                            },
                            success: function(value) {
                                let z = JSON.parse(value);
                                $('#id_batch' + no_batch).val(z.data.id_batch_number);
                            }
                        })
                        $('#batch_number' + no_batch).val(hasil);
                        console.log(hasil)
                        $('#batch_print' + no_batch).html(hasil);
                        $('#tgl_print' + no_batch).html(tgl_datang);
                        $('#id_barang' + no_batch).val(id_barang);
                        readOnlySelect2(no_batch);

                    } else {
                        $.ajax({
                            url: '../Backend/BatchNumber',
                            type: 'GET',
                            typeData: 'JSON',
                            data: {
                                id: id_barang,
                            },
                            success: function(value) {
                                let y = JSON.parse(value);
                                if (y.status == true) {
                                    $.ajax({
                                        url: '../Backend/SessionBatchNumber',
                                        type: 'GET',
                                        typeData: 'JSON',
                                        data: {
                                            id_barang: id_barang,
                                            batch_number: y.data
                                        },
                                        success: function(value) {
                                            let z = JSON.parse(value);
                                            $('#id_batch' + no_batch).val(z.data.id_batch_number);
                                        }
                                    })
                                    $('#batch_number' + no_batch).val(y.data);
                                    $('#id_barang' + no_batch).val(id_barang);
                                    $('#batch_print' + no_batch).html(y.data);
                                    $('#tgl_print' + no_batch).html(tgl_datang);
                                    readOnlySelect2(no_batch);
                                }
                            }
                        })
                    }
                }
            })
        })

        $('#body_barang').on('click', '#hapus_barang' + no, function() {
            var x = $(this).data("id")
            var id = $('#id_batch' + x).val()
            if (id) {
                $.ajax({
                    url: "../Backend/hapusBatchNumber",
                    type: "GET",
                    typeData: "JSON",
                    data: {
                        id: id
                    },
                    success: function(val) {
                        console.log(val)
                    }
                })
            }
            $(this).parent().parent().remove();
            document.getElementById('row' + x).remove();
        });
        no++;

    });


    $('#body_barang').on('click', '#hapus_barang1', function() {
        var id = $('#id_batch1').val()
        if (id) {
            $.ajax({
                url: "../Backend/hapusBatchNumber",
                type: "GET",
                typeData: "JSON",
                data: {
                    id: id
                },
                success: function(val) {
                    console.log(val)
                }
            })
        }
        $(this).parent().parent().remove();
        document.getElementById("row1").remove();
    });


    $('#form_barang').submit(function(e) {
        e.preventDefault();
        var form_data = new FormData(document.getElementById("form_barang"));
        $.ajax({
            url: '../Backend/tambahBarangMasuk',
            data: form_data,
            type: 'POST',
            dataType: 'JSON',
            processData: false,
            contentType: false,
            cache: false,
            async: false,
            success: function(value) {
                console.log(value)
                if(value.status == 'error validation') {
                    Toast.fire({
                        icon: 'error',
                        title: 'Pastikan semua data terisi!'
                    })
                } else {
                    Toast.fire({
                        icon: 'success',
                        title: 'Data Berhasil Di Simpan'
                    })
                    $("#form_barang").trigger("reset");
                    $("#body_barang").empty().append();
                    $("#select2supplier").val([]).trigger("change");
                    refresh_batch()
                }
            },
        })
    })

    $('#select2barang1').change(function() {
        var id_barang = $(this).val()
        var tgl_datang = $('#tanggal_kedatangan').val()
        $.ajax({
            url: '../Backend/BatchNumber',
            type: 'GET',
            typeData: 'JSON',
            data: {
                id: id_barang,
            },
            success: function(value) {
                let x = JSON.parse(value);
                console.log(x)
                if (x.status == true) {
                    $.ajax({
                        url: '../Backend/SessionBatchNumber',
                        type: 'GET',
                        typeData: 'JSON',
                        data: {
                            id_barang: id_barang,
                            batch_number: x.data
                        },
                        success: function(value) {
                            let y = JSON.parse(value);
                            $('#id_batch1').val(y.data.id_batch_number);
                        }
                    })
                    $('#batch_number1').val(x.data);
                    $('#id_barang1').val(id_barang);
                    $('#batch_print1').html(x.data);
                    $('#tgl_print1').html(tgl_datang);
                    readOnlySelect2(1);
                }
            }
        })
    })

    $('#data_barang_masuk').click(function() {
        refresh_batch()
        window.location.replace('../Inventory/Data_Barang_Masuk')
    })

    $("#print").click(function() {
        htmlPrint.style.display = "block";
        var divToPrint = document.getElementById('doc_print');

        var newWin = window.open('', 'Print-Window');

        newWin.document.open();

        newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

        newWin.document.close();

        setTimeout(function() {
            newWin.close();
            htmlPrint.style.display = "none";
        }, 10);
    });
})

function select2supplier() {
    $("#select2supplier").select2({
        allowClear: true,
        placeholder: "Select a Supplier",
    });
    show_supplier()
}

function show_supplier() {
    $.ajax({
        url: "../Backend/get_supplier",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#select2supplier').html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    $('#select2supplier').append(`<option value="${v.id_supplier}">${v.nama_supplier}</option>`).html();
                });
            }
        }
    });
}

function select2satuan() {
    $("#select2satuan").select2({
        allowClear: true,
        placeholder: "Select a Satuan",
    });
}

function select2barang1() {
    $("#select2barang1").select2({
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
                $('#select2barang1').html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    $('#select2barang1').append(`<option value="${v.id_barang}">${v.nama_barang}</option>`).html();
                });
            }
        }
    });
}

function show_barang_loop(no) {
    $.ajax({
        url: "../Backend/get_barang",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#select2barang' + no).html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    $('#select2barang' + no).append(`<option value="${v.id_barang}">${v.nama_barang}</option>`).html();
                });
            }
        }
    });
}

function readOnlySelect2(no) {
    $("#select2barang" + no).select2({
        disabled: true
    });
}

function refresh_batch() {
    $.ajax({
        url: "../Backend/DeleteBatchNumber",
        typeData: "JSON",
        success: function(value) {
            console.log(value)
        }
    })
}