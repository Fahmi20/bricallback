$(document).ready(function() {
    // Inisialisasi DataTable
    var table = $('#table_va').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "../Backend/process_daily_reports", // URL backend
            type: "GET",
            dataSrc: function(json) {
                // Mengambil array virtualAccountData dari response JSON
                return json.virtualAccountData.map(function(item) {
                    return {
                        customerNo: item.customerNo || '-',
                        virtualAccountNo: item.virtualAccountNo || '-',
                        partnerServiceId: item.partnerServiceId || '-',
                        totalAmount: item.totalAmount ? `${item.totalAmount.value} ${item.totalAmount.currency}` : '-',
                        trxDateTime: item.trxDateTime || '-',
                        virtualAccountName: item.virtualAccountName || '-',
                        trxId: item.trxId || '-',
                        description: item.additionalInfo ? item.additionalInfo.description : '-',
                        channel: item.additionalInfo ? item.additionalInfo.channel : '-',
                        sourceAccountVa: item.additionalInfo ? item.additionalInfo.sourceAccountVa : '-',
                        tellerId: item.additionalInfo ? item.additionalInfo.tellerId : '-',
                        paidStatus: 'N/A' // Default untuk kolom paidStatus
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
                data: 'partnerServiceId',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'ID Layanan Partner'
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
                data: 'totalAmount',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'Total Pembayaran'
            },
            {
                data: 'trxDateTime',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'Tanggal Transaksi'
            },
            {
                data: 'virtualAccountName',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'Nama Virtual Akun'
            },
            {
                data: 'trxId',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'Transaction ID'
            },
            {
                data: 'description',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'Deskripsi'
            },
            {
                data: 'channel',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'Channel'
            },
            {
                data: 'sourceAccountVa',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'Akun Sumber'
            },
            {
                data: 'tellerId',
                render: function(data) {
                    return `<div style="text-align:center;">${data}</div>`;
                },
                title: 'ID Teller'
            },
            {
                data: 'paidStatus',
                render: function(data) {
                    let statusText, statusIcon, statusColor;

                    // Menentukan teks, ikon, dan warna berdasarkan paidStatus
                    if (data === 'Y') {
                        statusText = 'Lunas';
                        statusIcon = '✔️';
                        statusColor = 'green';
                    } else if (data === 'N') {
                        statusText = 'Belum Lunas';
                        statusIcon = '❌';
                        statusColor = 'red';
                    } else {
                        statusText = 'N/A';
                        statusIcon = '-';
                        statusColor = 'gray';
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
