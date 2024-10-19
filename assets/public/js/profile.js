$(function() {
    var id = $('#id_user').val()

    show_profile(id)

    $('#form_edit').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "../Backend/edit_user",
            data: new FormData(this),
            type: "POST",
            dataType: "JSON",
            processData: false,
            contentType: false,
            cache: false,
            async: false,
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
                        title: 'Gagal Update User'
                    })
                } else {
                    show_profile(id)
                    Toast.fire({
                        icon: 'success',
                        title: 'Data User Berhasil Di Update'
                    })
                }
            }
        })
    })
})

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

function show_profile(id) {
    $.ajax({
        url: "../Backend/get_detail_profile",
        type: "GET",
        data: {
            id: id
        },
        dataType: "JSON",
        success: function(value) {
            var nama_user = '' + value.data.nama_user + '';
            var email = '' + value.data.email + '';
            var jk = '' + value.data.jk + '';
            var no_telp = '' + value.data.no_telp + '';
            var role = '' + value.data.role + '';
            var foto = '../assets/profile/' + value.data.foto + '';
            $('#nama_user').html(nama_user)
            $("#profile").attr("src", foto)
            $('#nama_user_profile').html(nama_user)
            $('#email').html(email)
            $('#departement').html(value.data.nama_departement)
            $('#jk').html(jk)
            $('#no_telp').html(no_telp)
            $('#role').html(role)

            $('#id').val(value.data.id_user)
            $('#old_foto').val(value.data.foto)
            $('#nama_user_form').val(value.data.nama_user)
            $('#no_telp_form').val(value.data.no_telp)
            $('#email_form').val(value.data.email)
            $("#jk_form").select2({
                allowClear: true,
                placeholder: "Select a Jenis Kelamin",
            });
            select2departement(value.data.id_departement)

            var option = `
                <option value=""></option>
                <option value="Pria">Pria</option>
                <option value="Wanita">Wanita</option>
            `
            $('#jk_form').html(option).val(value.data.jk).trigger("change")

            $("#role_form").select2({
                allowClear: true,
                placeholder: "Select a Jenis Kelamin",
            });

            var option = `
                <option value=""></option>
                <option value="Inventory">Admin Inventory</option>
                <option value="Produksi">Admin Produksi</option>
                <option value="Manager">Manager</option>
            `
            $('#role_form').html(option).val(value.data.role).trigger("change")
        }
    })
}

function select2departement(value) {
    $("#departement_form").select2({
        allowClear: true,
        placeholder: "Select a Departement",
    });
    show_departement(value)
}

function show_departement(value) {
    $.ajax({
        url: "../Backend/get_departement",
        type: "GET",
        dataType: "JSON",
        success: function(x) {
            if (x.status == true) {
                $('#departement_form').html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    $('#departement_form').append(`<option value="${v.id_departement}">${v.nama_departement}</option>`).val(value).trigger("change")
                });
            }
        }
    });
}