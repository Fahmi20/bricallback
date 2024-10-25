$(document).ready(function () {
	// SIMULATOR Inquiry Button Click
	$("#inquiryButton").click(function () {
		let virtualAccountNo = "   " + $("#virtualAccountNo").val();

		// Tampilkan spinner dan nonaktifkan tombol
		$("#loadingSpinner").show();
		$("#inquiryButton").prop("disabled", true);

		// AJAX Request untuk Inquiry
		$.ajax({
			url: "../Backend/get_virtual_account_data_simulator",
			type: "POST",
			data: { virtualAccountNo: virtualAccountNo },
			success: function (response) {
				let data = JSON.parse(response);

				// Sembunyikan spinner dan aktifkan kembali tombol
				$("#loadingSpinner").hide();
				$("#inquiryButton").prop("disabled", false);

				if (data.status === "error") {
					Swal.fire({
						icon: "error",
						title: "Virtual Account tidak ditemukan",
						text: "Periksa kembali nomor virtual account yang dimasukkan",
						showConfirmButton: true,
					});
				} else {
					// Tampilkan data dari response
					$("#va_number").text(data.virtualAccountNo);
					$("#customer_number").text(data.customerNo);
					$("#partnerserviceid_number").text(data.partnerServiceId);
					$("#customer_name").text(data.virtualAccountName);
					$("#total_amount").text("Rp. " + data.totalAmount);
					$("#trxId").val(data.trxId);
					$("#additionalInfo").val(data.additionalInfo);

					// Sembunyikan form dan tampilkan detail informasi
					$("#form_inquiry").hide();
					$("#detail_information").show();

					// Ubah warna step pembayaran menjadi hijau
					$("#step-pembayaran")
						.removeClass("border-secondary")
						.addClass("border-success");
					$("#pembayaran-step-text").css("color", "#28a745");
					$("#credit-card-icon").css("color", "#28a745");
				}
			},
			error: function (xhr, status, error) {
				$("#loadingSpinner").hide();
				$("#inquiryButton").prop("disabled", false);

				Swal.fire({
					icon: "error",
					title: "Terjadi kesalahan",
					text: "Gagal memproses permintaan, coba lagi nanti.",
					showConfirmButton: true,
				});
			},
		});
	});

	$(document).ready(function () {
		$(".btn-success.w-50").click(function () {
			let virtualAccountNo = $("#va_number").text();
			let totalAmount = $("#total_amount")
				.text()
				.replace(/[^\d,]/g, "");
			let totalAmountInput = $("#total_amount_input")
				.val()
				.replace(/[^\d,.-]/g, "")
				.replace(",", ".");

			totalAmount = parseFloat(totalAmount) / 100;
			totalAmountInput = parseFloat(totalAmountInput);

			let expiredDateInput = new Date();
			let formattedExpiredDate =
				expiredDateInput.getFullYear() +
				"-" +
				("0" + (expiredDateInput.getMonth() + 1)).slice(-2) +
				"-" +
				("0" + expiredDateInput.getDate()).slice(-2) +
				"T" +
				("0" + expiredDateInput.getHours()).slice(-2) +
				":" +
				("0" + expiredDateInput.getMinutes()).slice(-2) +
				":00+07:00";

			if (totalAmountInput) {
				let remainingAmount = parseFloat(
					(totalAmount - totalAmountInput).toFixed(2)
				);

				// Jalankan semua fungsi tanpa memeriksa kondisi
				processPayment(
					virtualAccountNo,
					totalAmountInput,
					$("#customer_number").text(),
					$("#partnerserviceid_number").text(),
					$("#customer_name").text(),
					function () {
						inquiryPayment(
							virtualAccountNo,
							$("#customer_number").text(),
							$("#partnerserviceid_number").text(),
							function () {
								updateStatusVA(function (
									paidStatus,
									totalAmount,
									customerNo,
									partnerServiceId
								) {
									updatePaidStatus(
										virtualAccountNo,
										paidStatus,
										totalAmount,
										customerNo,
										partnerServiceId,
										function () {
											// Panggil ajaxUpdateVirtualAccount terakhir, tanpa memeriksa kondisi remainingAmount
											ajaxUpdateVirtualAccount(
												remainingAmount,
												formattedExpiredDate
											);
										}
									);
								});
							}
						);
					}
				);
			} else {
				// Jika totalAmountInput tidak ada, jalankan processPayment saja
				processPayment(
					virtualAccountNo,
					totalAmountInput,
					$("#customer_number").text(),
					$("#partnerserviceid_number").text(),
					$("#customer_name").text()
				);
			}
		});

		function processPayment(
			virtualAccountNo,
			totalAmountInput,
			customerNo,
			partnerServiceId,
			virtualAccountName,
			callback
		) {
			$.ajax({
				url: "../Backend/process_payment_transfer_to_va_simulator",
				type: "POST",
				data: {
					virtualAccountNo,
					customerNo,
					partnerServiceId,
					totalAmountInput,
					virtualAccountName,
				},
				dataType: "json",
				success: function (response) {
					console.log("Respon dari server:", response);
					if (response.status === true) {
						Swal.fire({
							icon: "success",
							title: "Pembayaran Berhasil",
							text: response.message || "Status pembayaran telah diperbarui.",
							showConfirmButton: true,
						}).then(() => {
							callback(); // Lanjutkan ke inquiryPayment
						});

						// Sembunyikan informasi lama dan tampilkan informasi sukses
						$("#detail_information").hide();
						$("#detail_information_success").show();
						$("#customer_number_success").text($("#customer_number").text());
						$("#partnerserviceid_number_success").text(
							$("#partnerserviceid_number").text()
						);
						$("#customer_name_success").text($("#customer_name").text());
						$("#va_number_success").text($("#va_number").text());
						$("#total_amount_success").text($("#total_amount_input").val());
						$("#step-selesai .circle")
							.removeClass("border-secondary")
							.addClass("border-success");
						$("#success-icon").css("color", "#28a745");
						$("#text-selesai").css("color", "#28a745");
					} else {
						Swal.fire(
							"Gagal",
							"Terjadi kesalahan saat memperbarui status pembayaran.",
							"error"
						);
					}
				},
				error: function (xhr, status, error) {
					console.error("Error processing payment:", xhr, status, error);
					Swal.fire(
						"Terjadi Kesalahan",
						"Gagal memproses pembayaran, coba lagi nanti.",
						"error"
					);
				},
			});
		}

		function inquiryPayment(
			virtualAccountNo,
			customerNo,
			partnerServiceId,
			callback
		) {
			$.ajax({
				url: "../Backend/inquiry_paymentVA",
				type: "POST",
				data: { virtualAccountNo, customerNo, partnerServiceId },
				dataType: "json",
				success: function (response) {
					callback(); // Lanjutkan ke updateStatusVA
				},
				error: function (xhr, status, error) {
				},
			});
		}

		function updateStatusVA(callback) {
			let virtualAccountNo = $("#va_number").text();
			$.ajax({
				url: "../Backend/get_virtual_account_data_simulator",
				type: "POST",
				data: { virtualAccountNo },
				success: function (response) {
					let data = JSON.parse(response);
					let totalAmount = $("#total_amount")
						.text()
						.replace(/[^\d,]/g, "");
					let totalAmountInput = $("#total_amount_input")
						.val()
						.replace(/[^\d,.-]/g, "")
						.replace(",", ".");

					totalAmount = parseFloat(totalAmount) / 100;
					totalAmountInput = parseFloat(totalAmountInput);
					let remainingAmount = parseFloat(
						(totalAmount - totalAmountInput).toFixed(2)
					);
					let customerNo = data.customerNo;
					let partnerServiceId = data.partnerServiceId;
					let paidStatus = data.paidStatus;
					callback(paidStatus, remainingAmount, customerNo, partnerServiceId); // Lanjutkan ke updatePaidStatus
				},
				error: function (xhr, status, error) {
					console.error("Error fetching VA data:", xhr, status, error);
				},
			});
		}

		function updatePaidStatus(
			virtualAccountNo,
			paidStatus,
			totalAmount,
			customerNo,
			partnerServiceId,
			callback
		) {
			// Tentukan paidStatus berdasarkan totalAmount
			paidStatus = totalAmount <= 0 ? "Y" : "N";

			console.log("Paid Status:", totalAmount);

			// Lakukan update status VA melalui AJAX
			$.ajax({
				url: "../Backend/update_status_va_simulator",
				type: "POST",
				data: {
					virtualAccountNo: virtualAccountNo,
					paidStatus: paidStatus,
					customerNo: customerNo,
					partnerServiceId: partnerServiceId,
				},
				success: function (response) {
					console.log("Response from server:", response);
					if (callback && typeof callback === "function") {
						callback(); // Lanjutkan ke ajaxUpdateVirtualAccount jika diperlukan
					}
				},
				error: function (xhr, status, error) {
					console.error("Error updating status VA:", xhr, status, error);
				},
			});
		}

		function ajaxUpdateVirtualAccount(remainingAmount, formattedExpiredDate) {
			$.ajax({
				url: "../Backend/update_virtual_account_manual",
				type: "POST",
				data: {
					customerNo: $("#customer_number").text(),
					virtualAccountName: $("#customer_name").text(),
					totalAmount: remainingAmount,
					expiredDateInput: formattedExpiredDate,
					trxId: $("#trxId").val(),
					additionalInfo: $("#additionalInfo").val(),
				},
				success: function (response) {
					console.log("VA updated:", response);
				},
				error: function (xhr, status, error) {
					console.error("Error update VA:", xhr, status, error);
				},
			});
		}
	});

	$(document).ready(function () {
		// Event untuk tombol kembali di detail_information
		$("#kembaliButton").click(function () {
			// Sembunyikan detail_information dan tampilkan form inquiry
			$("#detail_information").hide();
			$("#form_inquiry").show();

			// Reset form jika diperlukan
			$("#virtualAccountNo").val(""); // Mengosongkan input Virtual Account
		});

		// Event untuk tombol kembali di detail_information_success
		$("#kembaliButtonSucess").click(function () {
			// Sembunyikan detail_information_success dan tampilkan form inquiry
			$("#detail_information_success").hide();
			$("#form_inquiry").show();

			// Reset form jika diperlukan
			$("#virtualAccountNo").val(""); // Mengosongkan input Virtual Account

			// Reset step indicator kembali ke INQUIRY
			$("#step-pembayaran .circle")
				.removeClass("border-success")
				.addClass("border-secondary");
			$("#credit-card-icon").css("color", "#6c757d");
			$("#pembayaran-step-text").css("color", "#6c757d");

			$("#step-selesai .circle")
				.removeClass("border-success")
				.addClass("border-secondary");
			$("#success-icon").css("color", "#6c757d");
			$("#text-selesai").css("color", "#6c757d");

			// Reset step indicator INQUIRY
			feather.replace(); // Render ulang feather icons jika diperlukan
		});
	});
});
