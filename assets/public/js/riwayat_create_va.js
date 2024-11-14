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
			},
			{
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
				},
			},
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
					return `<div style="text-align:center;">${new Date(
						row.expiredDate
					).toLocaleString()}</div>`;
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
			},
			{
				data: "Status",
				render: function (data, type, row) {
					return `<div style="text-align:center;">${row.Status}</div>`;
				},
			},
			{
				data: null,
				render: function (data, type, row) {
					var account = row.virtualAccountNo;
					return `
						<div style="text-align:center;">
							<button class="btn btn-danger btn_resend" data-id="${account}">Resend</button>
						</div>`;
				},
			},
		],
	});

	$("#table_va").on("click", ".btn_resend", function () {
		var virtualAccountNo = $(this).data("id");
		Swal.fire({
			title: "Anda yakin?",
			text: "Anda akan mengirim ulang Virtual Account ini.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#3085d6",
			cancelButtonColor: "#d33",
			confirmButtonText: "Ya, kirim ulang!",
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "../Backend/resend_failed_virtual_accounts",
					type: "POST",
					data: {
						virtualAccountNo: virtualAccountNo,
					},
					success: function (response) {
						Swal.fire(
							"Berhasil!",
							"Virtual Account berhasil dikirim ulang.",
							"success"
						);
						table.ajax.reload();
					},
					error: function () {
						Swal.fire(
							"Gagal!",
							"Pengiriman ulang Virtual Account gagal.",
							"error"
						);
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
