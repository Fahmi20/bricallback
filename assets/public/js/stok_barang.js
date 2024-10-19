$(function () {  
    select2barang();
    var search = document.getElementById("search");
    var search_detail = document.getElementById("search_detail");
    search.style.display = "none";
    search_detail.style.display = "none";

    $('#id_barang').change(function() {
        var id_barang = $(this).val()
        search_detail.style.display = "none";
        $("#table_detail_stok_barang").empty().append()
        search.style.display = "none";
        $("#table_stok_barang").empty().append()
        $.ajax({
            url: '../Backend/searchStok',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: id_barang,
            },
            success: function(x) {
                console.log(x)

                var stok_barang = `
                    <tr>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: center;">${x.stok_barang.nama_barang}</td>
                        <td style="text-align: center;">${x.stok_barang.qty_stok}</td>
                    </tr>
                `
                search.style.display = "block";
                
                $('#table_stok_barang').html(stok_barang)

                if(x.detail_stok_barang.length > 0) {
                    var no = 1;
                    $.each(x.detail_stok_barang, function (k, v) { 
                        var detail_barang = `
                            <tr>
                                <td style="text-align: center;">${no}</td>
                                <td style="text-align: center;">${v.batch_number}</td>
                                <td style="text-align: center;">${v.nama_barang}</td>
                                <td style="text-align: center;">${v.qty_masuk}</td>
                            </tr>
                        `

                        $('#table_detail_stok_barang').append(detail_barang).html();
                        no++
                    })
                    

                    search_detail.style.display = "block";
                } else {
                    var detail_barang = `
                        <tr>
                            <td style="text-align: center;" colspan="4">Data Detail Stok Barang Tidak Ada</td>
                        </tr>
                    `
                    $('#table_detail_stok_barang').html(detail_barang);
                    search_detail.style.display = "block";
                }
            }
        })
    })


    $('#view_all').click(function () { 
        search_detail.style.display = "none";
        $("#table_detail_stok_barang").empty().append()
        search.style.display = "none";
        $("#table_stok_barang").empty().append()
        $.ajax({
            url: '../Backend/view_all_stok',
            type: 'GET',
            dataType: 'JSON',

            success: function (x) { 
                console.log(x)
                
                if(x.stok_barang.length > 0) {
                    var no = 1
                    $.each(x.stok_barang, function (k, v) {  
                        var stok_barang = `
                            <tr>
                                <td style="text-align:center">${no}</td>
                                <td style="text-align:center">${v.nama_barang}</td>
                                <td style="text-align:center">${v.qty_stok}</td>
                            </tr>
                        `

                        $('#table_stok_barang').append(stok_barang).html()
                        no++
                    })
                    search.style.display = "block";
                } else {
                    var stok_barang = `
                        <tr>
                            <td style="text-align:center" colspan="3">Data Stok Barang Tidak Ada</td>
                        </tr>
                    `

                    $('#table_stok_barang').html(stok_barang)
                    search.style.display = "block"
                }

                if(x.detail_stok_barang.length > 0) {
                    var no = 1
                    $.each(x.detail_stok_barang, function (k, v) {  
                        var detail_stok_barang = `
                            <tr>
                                <td style="text-align: center;">${no}</td>
                                <td style="text-align: center;">${v.batch_number}</td>
                                <td style="text-align: center;">${v.nama_barang}</td>
                                <td style="text-align: center;">${v.qty_masuk}</td>
                            </tr>
                        `

                        $('#table_detail_stok_barang').append(detail_stok_barang).html()
                        no++
                    })
                    search_detail.style.display = "block"
                } else {
                    var detail_stok_barang = `
                        <tr>
                            <td style="text-align:center" colspan="4">Data Detail Stok Barang Tidak Ada</td>
                        </tr>
                    `

                    $('#table_detail_stok_barang').html(detail_stok_barang)
                    search_detail.style.display = "block"
                }
            }
        })
    })
})

function select2barang() {  
    $("#id_barang").select2({
        allowClear: true,
        placeholder: "Search a Barang",
        maximumSelectionLength: 1
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
            
                $('#id_barang').html('<option value=""></option>');
                $.each(x.data, function(k, v) {
                    var long_id = v.id_barang
                    if(long_id.length == 1)
                    {
                        var option1 = `<option value="${v.id_barang}">00${v.id_barang} | ${v.nama_barang}</option>`
                    } else if(long_id.length == 2) {
                        var option1 = `<option value="${v.id_barang}">0${v.id_barang} | ${v.nama_barang}</option>`
                    } else {
                        var option1 = `<option value="${v.id_barang}">${v.id_barang} | ${v.nama_barang}</option>`
                    }
                
                    $('#id_barang').append(option1).html();
                });
            }
        }
    });
}