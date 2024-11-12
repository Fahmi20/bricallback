$(document).ready(function() {
    // Inisialisasi DataTable
    var table = $('#table_va').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "../Backend/inquiry_status_va_controller", // URL backend
            type: "GET",
            dataSrc: function(json) {
                // Mengubah struktur data agar sesuai dengan DataTables
                return json.map(function(item) {
                    return {
                        customerNo: item.virtualAccountData.customerNo || '-',
                        virtualAccountNo: item.virtualAccountData.virtualAccountNo || '-',
                        partnerServiceId: item.virtualAccountData.partnerServiceId || '-',
                        inquiryRequestId: item.virtualAccountData.inquiryRequestId || '-',
                        paidStatus: item.additionalInfo.paidStatus || 'N/A'
                    };
                });
            }
        },
        columns: [
            {
                data: null,
                render: function(data, type, row, meta) {
                    return meta.row + 1;
                },
                title: 'No'
            },
            {
                data: 'customerNo',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'ID Pelanggan'
            },
            {
                data: 'virtualAccountNo',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'No Virtual Akun'
            },
            {
                data: 'partnerServiceId',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'ID Layanan Partner'
            },
            {
                data: 'inquiryRequestId',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'Request ID'
            },
            {
                data: 'paidStatus',
                render: function(data) {
                    let statusText, statusIcon, statusColor;
            
                    // Mengatur teks, ikon, dan warna berdasarkan status pembayaran
                    if (data === 'Y') {
                        statusText = 'Lunas';
                        statusIcon = '✔️'; // Ikon centang
                        statusColor = 'green';
                    } else {
                        statusText = 'Belum Lunas';
                        statusIcon = '❌'; // Ikon silang
                        statusColor = 'red';
                    }
            
                    // Mengembalikan elemen dengan gaya dan ikon
                    return `
                        <div style="text-align:center; font-weight:bold; color:${statusColor};">
                            <span style="font-size: 18px; margin-right: 5px;">${statusIcon}</span> 
                            ${statusText}
                        </div>`;
                },
                title: 'Status Pembayaran'
            }
            
        ]
    });

    // Toast untuk notifikasi
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    // Memanggil AJAX untuk notifikasi sukses atau gagal
    $.ajax({
        url: "../Backend/inquiry_status_va_controller",
        type: "GET",
        success: function(response) {
            Toast.fire({
                icon: 'success',
                title: 'Data riwayat pembayaran berhasil dimuat'
            });
        },
        error: function() {
            Toast.fire({
                icon: 'error',
                title: 'Gagal memuat data'
            });
        }
    });
});
