$(document).ready(function () {
    // Inisialisasi DataTables untuk tabel push notifikasi
    var tablePushNotifications = $('#table_push_notifications').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: "GET",
            url: "../Backend/push_notif", // Sesuaikan dengan endpoint controller yang benar untuk mengambil data push notifikasi
        },
        columnDefs: [{
            searchable: false,
            orderable: false,
            targets: 0,
        }],
        columns: [{
            render: function (data, type, row) {
                return `<div style="text-align:center;">${row.partnerServiceId}</div>`;
            },
        }, {
            render: function (data, type, row) {
                return `<div style="text-align:center;">${row.customerNo}</div>`;
            },
        }, {
            render: function (data, type, row) {
                return `<div style="text-align:center;">${row.virtualAccountNo}</div>`;
            },
        }, {
            render: function (data, type, row) {
                return `<div style="text-align:center;">${row.paymentRequestId}</div>`;
            },
        }, {
            render: function (data, type, row) {
                return `<div style="text-align:center;">${row.trxDateTime}</div>`;
            },
        }, {
            render: function (data, type, row) {
                return `<div style="text-align:center;">${row.paymentAmount}</div>`;
            },
        }, {
            render: function (data, type, row) {
                return `<div style="text-align:center;">${row.terminalId}</div>`;
            },
        }, {
            render: function (data, type, row) {
                return `<div style="text-align:center;">${row.bankId}</div>`;
            },
        }, {
            render: function (data, type, row) {
                return `<div style="text-align:center;">${row.status}</div>`;
            },
        }]
    });

    // Inisialisasi SweetAlert untuk notifikasi
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    // Handler untuk tombol "Push Notif"
    $(document).ready(function () {
        // Inisialisasi form validation menggunakan jQuery Validation Plugin
        $("#pushNotifForm").validate({
            submitHandler: function (form) {
                // Mengambil data dari form
                const formData = {
                    partnerServiceId: $("#partnerServiceId").val(),
                    customerNo: $("#customerNo").val(),
                    virtualAccountNo: $("#virtualAccountNo").val(),
                    trxDateTime: $("#trxDateTime").val(), // Sesuaikan dengan input yang benar
                    paymentRequestId: $("#paymentRequestId").val(),
                    idApp: $("#idApp").val(),  // Sesuaikan dengan input yang benar untuk additionalInfo
                    passApp: $("#passApp").val(),
                    paymentAmount: $("#paymentAmount").val(),
                    terminalId: $("#terminalId").val(),
                    bankId: $("#bankId").val()
                };

                // Mengirimkan data melalui AJAX ke controller
                $.ajax({
                    url: "../Backend/push_notif", // URL ke controller yang memproses push notification
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        if (response.error) {
                            // Menampilkan pesan error menggunakan SweetAlert2 jika ada error
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error
                            });
                        } else {
                            // Menampilkan pesan sukses
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Notifikasi berhasil dikirim!'
                            });
                            // Menutup modal setelah berhasil
                            $('#pushNotifModal').modal('hide');

                            // Memperbarui DataTables jika ada data yang relevan
                            tablePushNotifications.ajax.reload(null, false); // Reload tabel tanpa reset pagination
                        }
                    },
                    error: function (xhr, status, error) {
                        // Menampilkan pesan error jika terjadi masalah pada server
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: 'Terjadi kesalahan pada server: ' + error
                        });
                    }
                });

                // Mencegah submit default form
                return false;
            }
        });
    });
});
