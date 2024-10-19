$(document).ready(function() {
    // Inisialisasi DataTable
    var table = $('#table_va').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: "../Backend/inquiry_payment", // URL backend
            type: "GET",
            dataSrc: "data", // Sesuaikan dengan key dalam respons JSON
        },
        columns: [
            { data: null, render: function(data, type, row, meta) { return meta.row + 1; }, title: 'No' }, // Nomor urut
            { data: 'customerNo', render: function(data) { return `<div style="text-align:center;">${data.trim()}</div>`; }, title: 'ID Pelanggan' },
            { data: 'virtualAccountNo', render: function(data) { return `<div style="text-align:center;">${data.trim()}</div>`; }, title: 'No Virtual Akun' },
            { data: 'virtualAccountName', render: function(data) { return `<div style="text-align:center;">${data}</div>`; }, title: 'Nama Nasabah' },
            { data: 'totalAmount', render: function(data) { return `<div style="text-align:center;">${data.currency} ${data.value}</div>`; }, title: 'Jumlah Pembayaran' },
            { data: 'additionalInfo', render: function(data) { return `<div style="text-align:center;">${data.description}</div>`; }, title: 'Deskripsi' }
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
        url: "../Backend/inquiry_payment",
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
