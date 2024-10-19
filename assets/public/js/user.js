$(document).ready(function() {
    jk();
    role();
    selec2departement()
    $("#no_telp").inputmask();
    $("#email").inputmask();

    $('#password, #confirm_password').on('keyup', function() {
        if ($('#confirm_password').val() != "") {
            if ($('#password').val() == $('#confirm_password').val()) {
                $('#message').html('Matching').css('color', 'green');
            } else {
                $('#message').html('Not Matching').css('color', 'red');
            }
        }

    });

    var table = $('#table_user').DataTable({
        processing: true,
        ajax: {
            type: "GET",
            url: "../Backend/get_user",
        },
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: 0,
        }, ],
        columns: [{
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.id_user}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.nama_user}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.jk}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.no_telp}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.email}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.role}</div>`;
            },
        }, {
            render: function(data, type, row) {
                var user = row.id_user;
                return `<div style="text-align:center;"><a href="../Inventory/Detail_User/${row.id_user}"><button class="btn btn-secondary">Detail</button></a>
                    <button class="btn btn-danger" id="btn_hapus" data-id="${user}">Hapus</button></div>`
            },
        }],

    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
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

    $('#table_user').on('click', '#btn_hapus', function() {
        var id_user = $(this).data("id");
        $.ajax({
            type: "GET",
            url: "../Backend/get_detail_profile",
            dataType: "JSON",
            data: {
                id: id_user
            },
            success: function(value) {
                $("#id_user_hapus").val(value.data.id_user);
                $('#foto_hapus').val(value.data.foto);
                $('#hapus').modal('show');
            }
        })
    })

    $('#form_tambah').submit(function(e) {
        e.preventDefault();
        var form_data = new FormData(document.getElementById("form_tambah"));
        $("#form_tambah").validate({
            rules: {
                nama_user: {
                    required: true,
                },
                jk: {
                    required: true,
                },
                no_telp: {
                    required: true,
                },
                id_departement: {
                    required: true,
                },
                role: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    minlength: 6
                }
            },
            messages: {
                nama_user: "Please enter a Nama",
                jk: "Please select a Jenis Kelamin",
                no_telp: "Please enter a No Telp",
                id_departement: "Please select a Departement",
                role: "Please enter a Role",
                email: "Please enter a valid email",
                password: {
                    required: "Please enter a Password",
                    minlength: "Your password must be at least 6 characters long",
                },
                confirm_password: {
                    required: "Please enter a Confirm Password",
                    minlength: "Your password must be at least 6 characters long",
                }
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
                    url: "../Backend/tambah_user",
                    data: form_data,
                    type: "POST",
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    cache: false,
                    async: false,
                    beforeSend: function() {
                        $("#save_tambah").prop("disabled", true).html('...Loading');
                    },
                    success: function(value) {
                        if (value.status == 'Password Tidak Match!') {
                            Toast.fire({
                                icon: 'error',
                                title: 'Password Tidak Match'
                            })
                        } else if (value.status == 'Gagal Upload Foto') {
                            Toast.fire({
                                icon: 'error',
                                title: 'Gagal Upload Foto'
                            })
                        } else if (value.status == false) {
                            Toast.fire({
                                icon: 'error',
                                title: 'Data User Gagal Disimpan'
                            })
                        } else if (value.status == 'Email Sudah Terdaftar!') {
                            Toast.fire({
                                icon: 'error',
                                title: 'Email Sudah Terdaftar!'
                            })
                        } else {
                            Toast.fire({
                                icon: 'success',
                                title: 'User Berhasil Ditambahkan'
                            })
                        }
                        $('#save_tambah').removeAttr('disabled');
                        $('#tambah').modal('hide');
                        table.ajax.reload();
                    },
                    complete: function() {
                        $("#save_tambah").prop("disabled", false).html("Save");
                        $("#form_tambah").trigger("reset");
                    }
                })
            }
        })
    })

    $('#save_delete').click(function() {
        $.ajax({
            url: "../Backend/deleteUser",
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
                    title: 'Data Berhasil Di Hapus'
                })
            },
            complete: function() {
                $("#save_delete").prop("disabled", false).html("Yes, Delete It!");
                $("#form_delete").trigger("reset");
            }
        })
    })
});

function jk() {
    $("#jk").select2({
        allowClear: true,
        placeholder: "Select a Jenis Kelamin",
        dropdownParent: $('#tambah .modal-content')
    });

    var option = `
        <option value=""></option>
        <option value="Pria">Pria</option>
        <option value="Wanita">Wanita</option>
    `

    $('#jk').append(option);
}

function role() {
    $("#role").select2({
        allowClear: true,
        placeholder: "Select a Role",
        dropdownParent: $('#tambah .modal-content')
    });

    var option = `
        <option value=""></option>
        <option value="Inventory">Admin Inventory</option>
        <option value="Produksi">Admin Produksi</option>
        <option value="Manager">Manager</option>
    `

    $('#role').append(option);
}

function selec2departement() {
    $("#departement").select2({
        allowClear: true,
        placeholder: "Select a Departement",
        dropdownParent: $('#tambah .modal-content')
    });
    show_departement()
}

function show_departement() {
    $.ajax({
        url: "../Backend/get_departement",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#departement').html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    $('#departement').append(`<option value="${v.id_departement}">${v.nama_departement}</option>`).html()
                });
            }
        }
    });
}