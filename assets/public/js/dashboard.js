$(function () {  
    $.ajax({
        url: 'Backend/dashboard',
        type: 'GET',
        dataType: 'JSON',

        success: function (x) {  
            console.log(x)
            var t_barang_masuk = `
                <h3 class="mb-2" style="color: #7c86ff;">${x.barang_masuk.Total}</h3>
            `

            $('#t_barang_masuk').html(t_barang_masuk)

            var t_barang_keluar = `
                <h3 class="mb-2" style="color: #7c86ff;">${x.barang_keluar.Total}</h3>
            `

            $('#t_barang_keluar').html(t_barang_keluar)

            var t_permintaan_barang = `
                <h3 class="mb-2" style="color: #7c86ff;">${x.permintaan_barang.Total}</h3>
            `

            $('#t_permintaan_barang').html(t_permintaan_barang)

            var t_po = `
                <h3 class="mb-2" style="color: #7c86ff;">${x.po.Total}</h3>
            `

            $('#t_po').html(t_po)
        }
    })
})