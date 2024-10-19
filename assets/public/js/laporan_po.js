$(function () {  
    var show = document.getElementById("po")
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
        $("#table_po").empty().append()
        $("#print_table_po").empty().append()
        
        if(start_date){
            if(end_date){
                $.ajax({
                    url: '../Backend/search_laporan_po',
                    type: 'GET',
                    dataType: 'JSON',
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                    },

                    success: function (x) {  
                        console.log(x.po)
                        if(x.po.length > 0 ) {
                            var no = 1
                            $.each(x.po, function (k, v) {  
                                var po = `
                                <tr>
                                    <td style="text-align:center">${no}</td>
                                    <td style="text-align:center">${getFormattedDate(v.tgl_po)}</td>
                                    <td style="text-align:center">${v.nama_user}</td>
                                    <td style="text-align:center">${v.total.Total}</td>
                                </tr>
                            `
                            $('#table_po').append(po).html()
                            $('#print_table_po').append(po).html()
                            no++
                            })
                            show.style.display = "block";
                        } else {
                            var po = `
                                <tr>
                                    <td colspan="4" style="text-align:center">Data Barang Masuk Tidak Ada</td>
                                </tr>
                            `
                            $('#table_po').html(po)
                            $('#print_table_po').html(po)
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