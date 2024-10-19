$(function () {  
    var show = document.getElementById("barang_masuk")
    show.style.display = "none";

    var doc_print = document.getElementById("doc_print")
    doc_print.style.display = "none";

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    if($('#datePickerStart').length) {
        $('#datePickerStart').datepicker({
            format: "dd-mm-yyyy",
            todayHighlight: true,
            autoclose: true
        });
        $('#datePickerStart').datepicker('setDate');
    }

    if($('#datePickerEnd').length) {
        $('#datePickerEnd').datepicker({
            format: "dd-mm-yyyy",
            todayHighlight: true,
            autoclose: true
        });
        $('#datePickerEnd').datepicker('setDate');
    }

    $('#search').click(function () { 
        var start_date = $('#start_date').val()
        var end_date = $('#end_date').val()

        show.style.display = "none";
        $("#table_barang_masuk").empty().append()
        $("#print_table_barang_masuk").empty().append()
        
        if(start_date){
            if(end_date){
                $.ajax({
                    url: '../Backend/search_laporan_barang_masuk',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    },

                    success: function (x) {  
                        console.log(x)
                        if(x.barang_masuk.length > 0 ) {
                            var no = 1
                            $.each(x.barang_masuk, function (k, v) {  
                                var barang_masuk = `
                                    <tr>
                                        <td style="text-align:center">${no}</td>
                                        <td style="text-align:center">${getFormattedDate(v.tgl_datang)}</td>
                                        <td style="text-align:center">${v.batch_number}</td>
                                        <td style="text-align:center">${v.nama_barang}</td>
                                        <td style="text-align:center">${v.qty_masuk}</td>
                                        <td style="text-align:center">${v.satuan}</td>
                                        <td style="text-align:center">${getFormattedDate(v.exp)}</td>
                                    </tr>
                                `
                                $('#table_barang_masuk').append(barang_masuk).html()
                                $('#print_table_barang_masuk').append(barang_masuk).html()
                                no++
                            })
                            show.style.display = "block";
                        } else {
                            var barang_masuk = `
                                <tr>
                                    <td colspan="7" style="text-align:center">Data Barang Masuk Tidak Ada</td>
                                </tr>
                            `
                            $('#table_barang_masuk').html(barang_masuk)
                            $('#print_table_barang_masuk').html(barang_masuk)
                            show.style.display = "block";
                        }
                    }
                })
            } else {
                Toast.fire({
                    icon: 'error',
                    title: 'Field End Date Harap di isi'
                })
            }
        } else {
            Toast.fire({
                icon: 'error',
                title: 'Field Start Date Harap di isi'
            })
        }
    })

    $('#print').click(function () {  
        doc_print.style.display = "block";
        var divToPrint = document.getElementById('doc_print');

        var newWin = window.open('', 'Print-Window');

        newWin.document.open();

        newWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');

        newWin.document.close();

        setTimeout(function() {
            newWin.close();
            doc_print.style.display = "none";
        }, 10);
    })
})

function getFormattedDate(date) {
    var date = new Date(date);
    let year = date.getFullYear();
    let month = (1 + date.getMonth()).toString().padStart(2, '0');
    let day = date.getDate().toString().padStart(2, '0');

    return day + '-' + month + '-' + year;
}