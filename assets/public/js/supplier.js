$(document).ready(function() {
    //SIMULATOR
    $('#inquiryButton').click(function() {
        let virtualAccountNo = '   ' + $('#virtualAccountNo').val();

        if (virtualAccountNo === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Nomor Virtual Account wajib diisi!',
                showConfirmButton: true
            });
            return;
        }

        // Tampilkan spinner dan nonaktifkan tombol
        $('#loadingSpinner').show();
        $('#inquiryButton').prop('disabled', true);

        // AJAX Request
        $.ajax({
            url: '../Backend/get_virtual_account_data_simulator',  // Ganti dengan URL controller yang benar
            type: 'POST',
            data: { virtualAccountNo: virtualAccountNo },
            success: function(response) {
                let data = JSON.parse(response);

                // Sembunyikan spinner dan aktifkan kembali tombol
                $('#loadingSpinner').hide();
                $('#inquiryButton').prop('disabled', false);

                if (data.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Virtual Account tidak ditemukan',
                        text: 'Periksa kembali nomor virtual account yang dimasukkan',
                        showConfirmButton: true
                    });
                } else {
                    // Tampilkan data dari response
                    $('#va_number').text(data.virtualAccountNo);
                    $('#customer_number').text(data.customerNo);
                    $('#customer_name').text(data.virtualAccountName);
                    $('#total_amount').text('Rp. ' + data.totalAmount);
                    $('#trxId').val(data.trxId);

                    // Sembunyikan form dan tampilkan detail informasi
                    $('#form_inquiry').hide();
                    $('#detail_information').show();

                    // Ubah warna step pembayaran menjadi hijau
                    $('#step-pembayaran').removeClass('border-secondary').addClass('border-success');
                    $('#pembayaran-step-text').css('color', '#28a745');
                    $('#credit-card-icon').css('color', '#28a745');
                }
            },
            error: function(xhr, status, error) {
                $('#loadingSpinner').hide();
                $('#inquiryButton').prop('disabled', false);

                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi kesalahan',
                    text: 'Gagal memproses permintaan, coba lagi nanti.',
                    showConfirmButton: true
                });
            }
        });
    });

    // Tombol Bayar - Mengupdate status pembayaran
    // Tombol Bayar - Mengupdate status pembayaran
$('.btn-success.w-50').click(function() {
    let virtualAccountNo = $('#va_number').text(); // Ambil nomor virtual account
    let paidStatus = 'Y'; // Status pembayaran yang diupdate (misal menjadi PAID)

    // AJAX Request untuk update status pembayaran
    $.ajax({
        url: '../Backend/update_status_va_simulator',  // Ganti dengan URL controller yang benar
        type: 'POST',
        data: {
            virtualAccountNo: virtualAccountNo,
            paidStatus: paidStatus
        },
        success: function(response) {
            let data = JSON.parse(response);

            // Periksa apakah status adalah success
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil',
                    text: data.message || 'Status pembayaran telah diperbarui.',
                    showConfirmButton: true
                });

                // Ubah step pembayaran menjadi selesai
                $('#step-pembayaran').addClass('border-success');
                $('#pembayaran-step-text').css('color', '#28a745');
                $('#credit-card-icon').css('color', '#28a745');

                // Ubah ikon dan teks di step SELESAI
                $('#success-icon-wrapper').removeClass('border-secondary').addClass('border-success');
                $('#success-icon').attr('data-feather', 'check-circle'); // Mengganti ikon dengan ikon "success"
                $('#text-selesai').css('color', '#28a745'); // Mengubah warna teks menjadi hijau

                // Render Feather Icons ulang untuk memperbarui ikon
                feather.replace(); 
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Terjadi kesalahan saat memperbarui status pembayaran.',
                    showConfirmButton: true
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi kesalahan',
                text: 'Gagal memproses permintaan, coba lagi nanti.',
                showConfirmButton: true
            });
        }
    });
});


    // Event handler untuk tombol Kembali
    $('#kembaliButton').click(function() {
        // Sembunyikan detail informasi dan tampilkan form inquiry
        $('#detail_information').hide();
        $('#form_inquiry').show();

        // Bersihkan informasi yang ditampilkan sebelumnya
        $('#va_number').text('');
        $('#customer_number').text('');
        $('#customer_name').text('');
        $('#total_amount').text('');
        $('#trxId').val('');

        // Ubah kembali warna step pembayaran ke abu-abu
        $('#step-pembayaran').removeClass('border-success').addClass('border-secondary');
        $('#pembayaran-step-text').css('color', '#6c757d');
        $('#credit-card-icon').css('color', '#6c757d');
    });
    

});