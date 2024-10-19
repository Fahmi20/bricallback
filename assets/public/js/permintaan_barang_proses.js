$(function() {
    var id_permintaan = $('#id').val();
    show_permintaan(id_permintaan)
    var show_search = document.getElementById("show_search");
    var barang_keluar = document.getElementById("barang_keluar");
    show_search.style.display = "none";
    barang_keluar.style.display = "none";
    show_barang_keluar(id_permintaan);

    var table = $('#table_barang').DataTable({
        processing: true,
        ajax: {
            url: '../../Backend/get_permintaan_proses',
            type: 'GET',
            data: {
                id: id_permintaan
            },
        },
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: 0,
        }, ],
        columns: [{
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.id_detail_permintaan}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.nama_barang}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.qty_permintaan}</div>`;
            },
        }, {
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.ket_permintaan}</div>`;
            },
        },{
            render: function(data, type, row) {
                return `<div style="text-align:center;">${row.qty_keluar_permintaan}</div>`;
            },
        }],
    })

    table.on('order.dt search.dt', function() {
        table.column(0, {
            search: 'applied',
            order: 'applied'
        }).nodes().each(function(cell, i) {
            cell.innerHTML = i + 1;
            table.cell(cell).invalidate('dom');
        });
    }).draw();

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    $('#form_proses').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: '../../Backend/ProsesPermintaan',
            type: 'GET',
            dataType: 'JSON',
            data: $('#form_proses').serialize(),

            success: function (x) { 
                Toast.fire({
                    icon: 'success',
                    title: 'Berhasil Proses Permintaan',
                })
                show_barang_keluar(id_permintaan);
                $("#form_proses").trigger("reset");
                $("#body_batch").empty().append();
                $("#proses_row").empty().append();
                $("#input_search").val('');
                table.ajax.reload();
                show_permintaan(id_permintaan);
                show_search.style.display = "none";
            }
        })
    });

    $('#btn_search').click(function () {  
        var search = $('#input_search').val();
        $.ajax({
            url: '../../Backend/search',
            type: 'GET',
            dataType: 'JSON',
            data: {
                batch_number: search
            },
            success: function (value) {  
                show_search.style.display = "block";
                console.log(value);
                var table_batch = `
                        <tr>
                            <td>
                                <input type="hidden" class="form-control" name="id_barang" value="${value.data.id_barang}">
                                <input type="hidden" class="form-control" name="id_permintaan" value="${id_permintaan}">
                                <input type="text" class="form-control" name="batch_number" value="${value.data.batch_number}" readonly required>
                            </td>
                            <td>
                                <input type="text" class="form-control" value="${value.data.nama_barang}" readonly required>
                            </td>
                            <td>
                                <input type="text" name="ketersediaan" class="form-control" value="${value.data.qty_masuk}" readonly required>
                            </td>
                            <td>
                                <input type="number" class="form-control" id="qty_keluar" name="qty_keluar" min='0' max="${value.data.qty_masuk}" required>
                            </td>
                        </tr>
                    `
                $('#body_batch').append(table_batch).html();
            }
        })
    });

    $('#btn_reset').click(function () {  
        $("#form_proses").trigger("reset");
        $("#body_batch").empty().append();
        $("#input_search").val('');
        show_search.style.display = "none";
    })
})

function show_barang_keluar(value) {
    $.ajax({
        type: "GET",
        url: "../../Backend/barang_keluar",
        data: {
            id: value
        },
        dataType: 'JSON',
        success: function (x) { 
            console.log(x)
            if(x.barang_keluar != null) {
                var data_barang_keluar = `
                <div class="col-lg-12 grid-margin">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Data Barang Keluar</h4>
                            <hr>
                            <table id="table_keluar" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th> Tanggal Keluar </th>
                                        <th> Batch Number </th>
                                        <th> Nama Barang </th>
                                        <th> Kuantiti </th>
                                    </tr>
                                </thead>
                                <tbody id="loop_barang_keluar">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                `

                $('#barang_keluar').html(data_barang_keluar)

                $.each(x.barang_keluar, function (k, v) {
                    var body = `
                    <tr>
                        <td>${getFormattedDate(v.tgl_keluar)}</td>
                        <td>${v.batch_number}</td>
                        <td>${v.nama_barang}</td>
                        <td>${v.qty_diserahkan}</td>
                    </tr>`
                    $('#loop_barang_keluar').append(body)
                })
                barang_keluar.style.display = "block";
            }
        }
    })
}

function show_permintaan(value) {
    $.ajax({
        url: '../../Backend/get_proses_permintaan',
        type: 'GET',
        dataType: 'JSON',
        data: {
            id: value
        },
        success: function(x) {
            console.log(x)
            var permintaan = `
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td>Departement</td>
                        <td>: ${x.data.nama_departement}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Permintaan</td>
                        <td>: ${getFormattedDate(x.data.tgl_permintaan)}</td>
                    </tr>
                </tbody>
            </table>
            `;
            $('#permintaan_barang').html(permintaan);
            
            var no = 1
            if(x.rekomendasi != null) {
                
                $.each(x.rekomendasi, function(k, v) {
                
                    var table = `
                            <tr>
                                <td>
                                    ${no}
                                </td>
                                <td>
                                    <input type="hidden" class="form-control" name="id_barang[]" value="${v.id_barang}">
                                    <input type="hidden" class="form-control" name="id_detail_permintaan[]" value="${v.id_detail_permintaan}">
                                    <input type="hidden" class="form-control" name="id_permintaan" value="${v.id_permintaan}">
                                    <input type="hidden" class="form-control" name="id_departement" value="${v.id_departement}">
                                    <input type="text" class="form-control" name="batch_number[]" value="${v.batch_number}" readonly required>
                                </td>
                                <td>
                                    <input type="text" class="form-control" value="${v.nama_barang}" readonly required>
                                </td>
                                <td>
                                    <input type="text" name="ketersediaan[]" class="form-control" value="${v.qty_masuk}" readonly required>
                                </td>
                            </tr>
                    `
                    $('#proses_row').append(table).html();
                    no++
                })
                
            } else {
                if(x.data.status_permintaan == 'Proses') {
                    if(x.rekomendasi != 'barang_habis') {
                        $.ajax({
                            url: '../../Backend/update_status_permintaan',
                            type: 'GET',
                            dataType: 'JSON',
                            data: {
                                id_permintaan: x.data.id_permintaan
                            },
                            success: function (value) { 
                                console.log(value)
                            }
                        })
                    }
                }

                var table = `
                    <tr>
                        <td colspan="5" style="text-align:center;">
                            Permintaan Barang Sudah Terpenuhi
                        </td>
                    </tr>
            `
            $('#proses_row').append(table).html();
            }

            if(x.barang_keluar != null) {
                if(x.data.status_permintaan == 'Waiting') {
                    $.ajax({
                        url: '../../Backend/status_proses_permintaan',
                        type: 'GET',
                        dataType: 'JSON',
                        data: {
                            id: value
                        },
                        success: function (y) { 
                            console.log(y)
                        }
                    })
                }
            }
        }
    })
}

function getFormattedDate(date) {
    var date = new Date(date);
    let year = date.getFullYear();
    let month = (1 + date.getMonth()).toString().padStart(2, '0');
    let day = date.getDate().toString().padStart(2, '0');

    return day + '-' + month + '-' + year;
}