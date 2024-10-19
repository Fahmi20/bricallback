$(function () { 
    show_notifikasi()
})

function show_notifikasi() { 
    $.ajax({
        url: 'http://localhost/inventory_control/Backend/get_notifikasi',
        type: 'GET',
        dataType: 'JSON',
        
        success: function (x) { 
            if(x.data.length > 0) {
                var icon_notifikasi = `
                    <div class="indicator">
                        <div class="circle"></div>
                    </div>
                `
                $('#icon-notif').html(icon_notifikasi)

                var total_notifikasi = `
                    <p>${x.total.Total} New Notifications</p>
                `

                $('#jml_notifikasi').html(total_notifikasi)

                $.each(x.detail, function (k, v) { 
                    var dropdown_notifikasi = `
                        <a href="Inventory/Proses_Permintaan_Barang/${v.id_permintaan}" class="dropdown-item d-flex align-items-center py-2">
                            <div class="wd-30 ht-30 d-flex align-items-center justify-content-center bg-primary rounded-circle me-3">
                                <i class="icon-sm text-white fa fa-dropbox"></i>
                            </div>
                            <div class="flex-grow-1 me-2">
                                <p>${v.nama_departement}</p>
                                <p class="tx-12 text-muted">${getFormattedDate(v.tgl_permintaan)}</p>
                            </div>
                        </a>
                    `

                    $('#dropdown_notifikasi').append(dropdown_notifikasi).html()
                })

                if(x.data.length > 5) {
                    var view_all = `
                        <div class="px-3 py-2 d-flex align-items-center justify-content-center border-top">
                            <a href="Inventory/Data_Permintaan_Barang">View all</a>
                        </div>
                    `

                    $('#view_all_notifikasi').html(view_all)
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