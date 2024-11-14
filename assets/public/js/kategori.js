$(document).ready(function () {
	var table = $("#table_va").DataTable({
		processing: true,
		serverSide: false,
		ajax: {
			type: "GET",
			url: "../Backend/inquire_virtual_account",
			dataSrc: function (response) {
				if (response && Array.isArray(response)) {
					return response.map(function (item) {
						return item.virtualAccountData;
					});
				}
				return [];
			},
		},
		columns: [
			{
				data: null,
				render: function (data, type, row, meta) {
					return `<div style="text-align:center;">${meta.row + 1}</div>`;
				},
			},
			{
				data: "partnerServiceId",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.partnerServiceId}</div>`;
				},
			},
			{
				data: "customerNo",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.customerNo}</div>`;
				},
			},{
				data: "partnerReferenceNo",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.partnerReferenceNo}</div>`;
				},
			},
			{
				data: "virtualAccountNo",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.virtualAccountNo}</div>`;
				},
			},{
				data: "paidStatus",
				render: function (data, type, row) {
					let statusText, statusColor, statusIcon;
			
					// Mengatur ikon dan warna berdasarkan status pembayaran
					if (row.paidStatus === "Y") {
						statusText = "Paid";
						statusColor = "green";
						statusIcon = "✔️"; // Ikon centang
					} else if (row.paidStatus === "N") {
						statusText = "Pending";
						statusColor = "orange";
						statusIcon = "⏳"; // Ikon jam pasir
					} else {
						statusText = "No Data";
						statusColor = "red";
						statusIcon = "❌"; // Ikon silang
					}
			
					// Mengembalikan elemen dengan gaya dan ikon yang menarik
					return `
						<div style="text-align:center; font-weight:bold; color: ${statusColor};">
							${statusIcon} ${statusText}
						</div>
					`;
				}
			}
			,
			{
				data: "virtualAccountName",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.virtualAccountName}</div>`;
				},
			},
			{
				data: "totalAmount",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.totalAmount.currency} ${row.totalAmount.value}</div>`;
				},
			},
			{
				data: "expiredDate",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${new Date(row.expiredDate).toLocaleString()}</div>`;
				},
			},
			{
				data: "trxId",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.trxId}</div>`;
				},
			},
			{
				data: "additionalInfo",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.additionalInfo.description}</div>`;
				},
			},{
				data: "Status",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.Status}</div>`;
				},
			},
			{
				data: null,
				render: function (data, type, row) {
					var account = row.virtualAccountNo;
					var customerNo = row.customerNo;
					return `
						<div style="text-align:center;">
							<a href="../Backend/Detail_Account/${account}">
								<button class="btn btn-secondary">Detail</button>
							</a>
							<button class="btn btn-primary btn_edit" data-id="${customerNo}" data-bs-toggle="modal" data-bs-target="#edit">Edit</button>
							<button class="btn btn-danger btn_hapus" data-id="${customerNo}">Hapus</button>
						</div>`;
				},
			},
		],
	});

	$("#table_va").on("click", ".btn_edit", function () {
		var customerNo = $(this).data("id"); // Ambil customerNo dari button

		// Disable tombol Save saat data masih dimuat
		$("#save_edit").prop("disabled", true);

		// Panggilan AJAX untuk mendapatkan data berdasarkan customerNo
		$.ajax({
			url: "../Backend/get_virtual_account_data", // URL controller untuk mendapatkan data
			type: "POST", // Gunakan POST untuk mengirim customerNo
			data: { customerNo: customerNo }, // Kirim customerNo ke server
			dataType: "JSON",
			success: function (data) {
				$("#edit_customerNo").val(data.customerNo);
				$("#edit_virtualAccountName").val(data.virtualAccountName);
				$("#edit_totalAmount").val(data.totalAmount);
				$("#edit_totalAmountCurrency").val(data.totalAmountCurrency);
				$("#edit_expiredDate").val(data.expiredDate);
				$("#edit_trxId").val(data.trxId);
				$("#edit_additionalInfo").val(data.additionalInfo);
				$("#save_edit").prop("disabled", false);
				$("#edit").modal("show");
			},
			error: function () {
				alert("Gagal mengambil data virtual account.");
				// Aktifkan tombol Save jika terjadi error
				$("#save_edit").prop("disabled", false);
			},
		});
	});

	// Panggilan kedua: Mengirim data yang telah diedit ke server
	// Panggilan kedua: Mengirim data yang telah diedit ke server
	$("#save_edit").on("click", function () {
		var customerNo = $("#edit_customerNo").val();
		var virtualAccountName = $("#edit_virtualAccountName").val();
		var totalAmount = $("#edit_totalAmount").val();
		var expiredDateInput = $("#edit_expiredDate").val();
		var trxId = $("#edit_trxId").val();
		var additionalInfo = $("#edit_additionalInfo").val();

		// Disable tombol Save untuk mencegah pengiriman data ganda
		$("#save_edit").prop("disabled", true);

		// Panggilan AJAX untuk mengirim data yang sudah diubah ke server
		$.ajax({
			url: "../Backend/update_virtual_account_manual", // URL controller untuk update
			type: "POST",
			data: {
				customerNo: customerNo,
				virtualAccountName: virtualAccountName,
				totalAmount: totalAmount,
				expiredDateInput: expiredDateInput, // Kirim tanggal yang diformat
				trxId: trxId,
				additionalInfo: additionalInfo,
			},
			dataType: "json",
			success: function (response) {
				// Log response ke console
				console.log("Response from server:", response);

				if (response.status === "error") {
					// SweetAlert untuk menampilkan pesan error
					Swal.fire({
						icon: "error",
						title: "Oops...",
						text: response.message,
					});
				} else {
					// Jika sukses, tampilkan SweetAlert success
					Swal.fire({
						icon: "success",
						title: "Success!",
						text: "Virtual account updated successfully.",
						confirmButtonText: "OK",
					}).then((result) => {
						if (result.isConfirmed) {
							$("#edit").modal("hide"); // Tutup modal
							$("#table_va").DataTable().ajax.reload(); // Reload tabel
						}
					});
				}

				// Aktifkan kembali tombol Save setelah selesai
				$("#save_edit").prop("disabled", false);
			},
			error: function (xhr, status, error) {
				// Menampilkan error detail dari server dengan SweetAlert
				Swal.fire({
					icon: "error",
					title: "Error",
					text:
						"Gagal memperbarui virtual account. Status: " +
						status +
						", Error: " +
						error,
				});
				$("#save_edit").prop("disabled", false);
			},
			complete: function () {
				// Pastikan tombol diaktifkan kembali meskipun ada error tak terduga
				$("#save_edit").prop("disabled", false);
			},
		});
	});

	$("#table_va").on("click", ".btn_hapus", function () {
		var customerNo = $(this).data("id"); // Ambil customerNo dari tombol

		// Konfirmasi sebelum penghapusan menggunakan Swal.fire
		Swal.fire({
			title: "Anda yakin?",
			text: "Virtual account akan dihapus!",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Ya, hapus!",
			cancelButtonText: "Batal",
		}).then((result) => {
			if (result.isConfirmed) {
				// Jika dikonfirmasi, kirim permintaan Ajax untuk menghapus data
				$.ajax({
					url: "../Backend/delete_va_controller", // Sesuaikan dengan URL controller Anda
					type: "POST",
					data: {
						customerNo: customerNo, // Kirim customerNo ke controller
						_method: "DELETE", // Simulasi method DELETE
					},
					success: function (response) {
						var result = JSON.parse(response);

						if (result.status === "error") {
							// Menampilkan pesan error dengan Swal.fire
							Swal.fire({
								title: "Gagal!",
								text: result.message,
								icon: "error",
								confirmButtonText: "OK",
							});
						} else {
							// Menampilkan pesan sukses dengan Swal.fire
							Swal.fire({
								title: "Dihapus!",
								text: "Virtual account berhasil dihapus.",
								icon: "success",
								confirmButtonText: "OK",
							}).then(() => {
								table.ajax.reload(); // Reload DataTable setelah penghapusan
							});
						}
					},
					error: function () {
						// Menampilkan pesan error jika request gagal
						Swal.fire({
							title: "Gagal!",
							text: "Gagal menghapus virtual account.",
							icon: "error",
							confirmButtonText: "OK",
						});
					},
				});
			}
		});
	});

	const Toast = Swal.mixin({
		toast: true,
		position: "top-end",
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
	});

	$("#refresh_va").on("click", function () {
		table.ajax.reload();
	});

	$("#search_virtual_account").on("keyup", function () {
		$("#table_va").DataTable().ajax.reload();
	});

	$("#form_create_va").on("submit", function (e) {
		e.preventDefault();
		$.ajax({
			url: $(this).attr("action"),
			method: "POST",
			data: $(this).serialize(),
			success: function () {
				Swal.fire(
					"Success",
					"Virtual Account created successfully!",
					"success"
				);
				$("#tambah").modal("hide");
				$("#table_va").DataTable().ajax.reload();
			},
			error: function () {
				Swal.fire("Error", "Failed to create Virtual Account", "error");
			},
		});
	});

	$.ajax({
		url: "../backend/get_current_datetime", // URL yang mengembalikan waktu dari server
		method: "GET",
		success: function (response) {
			// Mengisi nilai input hidden datetime-local
			$("#trxDateTime").val(response.datetime);
		},
		error: function () {
			console.error("Gagal mendapatkan waktu dari server.");
		},
	});
});
